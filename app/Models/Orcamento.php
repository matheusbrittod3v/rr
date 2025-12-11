<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Jobs\CheckProviderQueueJob;
use App\Jobs\AssignBudgetToProviderJob;
use App\Models\FilaOrcamento as FilaOrcamentoModel;
use App\Models\Service;

class Orcamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'numero',
        'informacoes_adicionais',
        'valor',
        'aceito',
        'status',
        'id_orcamento',
        'service_id'
    ];

    protected $casts = [
        'informacoes_adicionais' => 'array',
        'valor' => 'decimal:2'
    ];

    /**
     * Boot model
     */
    protected static function boot()
    {
        parent::boot();

        //
        // EVENTO: Orcamento criado
        //
        static::created(function ($orcamento) {

            // Cria entrada na fila
            $fila = FilaOrcamentoModel::create([
                'orcamento_id' => $orcamento->id,
                'prestador_id' => null
            ]);

            // Dispara jobs
            CheckProviderQueueJob::dispatch();
            AssignBudgetToProviderJob::dispatch($fila->id);
        });
    }

    public function filaOrcamento()
    {
        return $this->hasOne(FilaOrcamentoModel::class, 'orcamento_id');
    }

    public function filaOrcamentoWithProvider()
    {
        return $this->hasOne(FilaOrcamentoModel::class, 'orcamento_id')
                    ->with('prestador');
    }
    
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}