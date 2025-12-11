<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\FilaOrcamento;
use App\Models\FilaPrestador;
use App\Services\IdempotencyService;
use Illuminate\Support\Facades\Log;

class AssignBudgetToProviderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $budgetId;
    protected $idempotencyKey;

    /**
     * Create a new job instance.
     *
     * @param int $budgetId
     * @return void
     */
    public function __construct($budgetId)
    {
        $this->budgetId = $budgetId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Generate idempotency key for this job execution
        $this->idempotencyKey = IdempotencyService::createKey('assign_budget_to_provider', [
            'budget_id' => $this->budgetId
        ]);

        // Check if this job has already been processed
        if (IdempotencyService::hasBeenProcessed($this->idempotencyKey, 'assign_budget_to_provider')) {
            Log::info('Idempotency check: AssignBudgetToProviderJob already processed', [
                'budget_id' => $this->budgetId,
                'idempotency_key' => $this->idempotencyKey
            ]);
            return;
        }

        try {
            // Get the first provider in queue
            $firstProvider = FilaPrestador::orderBy('position')->first();
            
            if ($firstProvider) {
                // Assign budget to provider
                $budgetQueue = FilaOrcamento::find($this->budgetId);
                if ($budgetQueue) {
                    $budgetQueue->prestador_id = $firstProvider->user_id;
                    $budgetQueue->save();
                    
                    // Move provider to end of queue
                    $maxPosition = FilaPrestador::max('position');
                    $firstProvider->position = $maxPosition + 1;
                    $firstProvider->save();
                    
                    Log::info("Budget {$this->budgetId} assigned to provider {$firstProvider->user_id}");
                    
                    // Dispatch notification job to notify provider about new budget
                    NotifyProviderNewBudgetJob::dispatch($this->budgetId);
                }
            } else {
                Log::warning("No providers available in queue for budget {$this->budgetId}");
            }
            
            // Mark this operation as processed
            IdempotencyService::markAsProcessed($this->idempotencyKey, 'assign_budget_to_provider', [
                'budget_id' => $this->budgetId,
                'provider_id' => $firstProvider->user_id ?? null
            ]);
        } catch (\Exception $e) {
            Log::error("Error in AssignBudgetToProviderJob: {$e->getMessage()}", [
                'budget_id' => $this->budgetId,
                'exception' => $e
            ]);
            
            // Even if there's an exception, mark the job as processed to prevent duplicate executions
            try {
                IdempotencyService::markAsProcessed($this->idempotencyKey, 'assign_budget_to_provider', [
                    'budget_id' => $this->budgetId,
                    'error_message' => $e->getMessage()
                ]);
            } catch (\Exception $markingException) {
                Log::error("Failed to mark idempotency key as processed in AssignBudgetToProviderJob", [
                    'budget_id' => $this->budgetId,
                    'marking_exception' => $markingException->getMessage()
                ]);
            }
        }
    }
}