<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Jobs\SendWhatsAppGroupNotificationJob;
use Illuminate\Support\Str;

class FilaOrcamento extends Model
{
    use HasFactory;

    protected $table = 'fila_orcamentos';

    protected $fillable = [
        'orcamento_id',
        'prestador_id'
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Listen for updates to the fila_orcamentos table
        static::updated(function ($fila) {
            // Dispara somente quando prestador é atribuído
            if ($fila->wasChanged('prestador_id') && $fila->prestador_id) {
                $orcamento = $fila->orcamento;
                $provider  = $fila->prestador;

                if ($orcamento && $provider) {
                    // Generate or reuse unique ID for the budget
                    if (empty($orcamento->id_orcamento)) {
                        // Generate a unique ID for this budget (8 chars + 2 digits)
                        $uniqueId = strtoupper(trim(Str::random(8) . rand(10, 99)));
                        
                        // Save it to the database
                        $orcamento->id_orcamento = $uniqueId;
                        $orcamento->save();
                    } else {
                        $uniqueId = $orcamento->id_orcamento;
                    }
                    
                    SendWhatsAppGroupNotificationJob::dispatch(
                        $orcamento->id,
                        $provider->name,
                        $uniqueId  // Pass the unique ID to the job
                    );
                }
            }
        });
    }

    public function orcamento()
    {
        return $this->belongsTo(Orcamento::class, 'orcamento_id');
    }

    public function prestador()
    {
        return $this->belongsTo(User::class, 'prestador_id');
    }
}