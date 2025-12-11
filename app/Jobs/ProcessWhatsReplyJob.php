<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Models\Orcamento;
use App\Jobs\NotifyGroupOrderProcessedJob;

class ProcessWhatsReplyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $uniqueId;
    protected string $status;
    protected ?float $valor;

    /**
     * Create a new job instance.
     */
    public function __construct(string $uniqueId, string $status, ?float $valor = null)
    {
        $this->uniqueId = $uniqueId;
        $this->status = $status;
        $this->valor = $valor;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('ProcessWhatsReplyJob processing reply', [
            'id' => $this->uniqueId,
            'status' => $this->status,
            'valor' => $this->valor
        ]);

        // Debug: List all orcamentos with id_orcamento set
        $allOrcamentos = Orcamento::whereNotNull('id_orcamento')->get(['id', 'id_orcamento', 'status']);
        Log::info('All orcamentos with id_orcamento', [
            'count' => $allOrcamentos->count(),
            'records' => $allOrcamentos->toArray()
        ]);

        // Find orcamento by id_orcamento
        $orc = Orcamento::where('id_orcamento', $this->uniqueId)->first();

        if (!$orc) {
            Log::warning('Orcamento not found for unique id', [
                'id' => $this->uniqueId,
                'searching_for' => 'id_orcamento = ' . $this->uniqueId
            ]);
            return;
        }

        Log::info('Orcamento found', [
            'orcamento_id' => $orc->id,
            'current_status' => $orc->status
        ]);

        // Update based on status
        if ($this->status === 'sim') {
            $orc->aceito = 'sim';
            $orc->status = 'respondido'; // Change from 'sim' to 'respondido'
            if ($this->valor !== null) {
                $orc->valor = $this->valor;
            }
            $orc->save();
            Log::info('Orcamento marked as accepted', [
                'orcamento_id' => $orc->id,
                'unique_id' => $this->uniqueId,
                'valor' => $this->valor
            ]);
            
            // Dispatch notification to WhatsApp groups that the order has been processed
            // Get the provider name from the budget queue
            $filaOrcamento = $orc->filaOrcamentoWithProvider;
            if ($filaOrcamento && $filaOrcamento->prestador) {
                NotifyGroupOrderProcessedJob::dispatch(
                    $orc->id,
                    $this->uniqueId,
                    $filaOrcamento->prestador->name,
                    $this->valor
                );
            }
            
            return;
        }

        if ($this->status === 'nao') {
            $orc->aceito = 'nao';
            $orc->status = 'respondido'; // Change from 'nao' to 'respondido'
            $orc->save();
            Log::info('Orcamento marked as rejected', [
                'orcamento_id' => $orc->id,
                'unique_id' => $this->uniqueId
            ]);
            
            // Dispatch notification to WhatsApp groups that the order has been processed
            // Get the provider name from the budget queue
            $filaOrcamento = $orc->filaOrcamentoWithProvider;
            if ($filaOrcamento && $filaOrcamento->prestador) {
                NotifyGroupOrderProcessedJob::dispatch(
                    $orc->id,
                    $this->uniqueId,
                    $filaOrcamento->prestador->name,
                    null // No value for rejection
                );
            }
            
            return;
        }

        // sem_resposta or unknown
        $orc->status = 'sem_resposta';
        $orc->save();
        Log::info('Orcamento status set to sem_resposta', [
            'orcamento_id' => $orc->id,
            'unique_id' => $this->uniqueId
        ]);
    }
}