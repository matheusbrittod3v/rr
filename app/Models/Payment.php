<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tx_id',
        'valor',
        'gateway_id',
        'status',
        'status_notification_sent', // Add this field
        'tool_id',
        'orcamento_id',
        'number_whatsapp',
        'email',
        'response',
        'reference_id',
        'session_uuid',
        'metadata',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
    ];
    
    // Constants for payment statuses
    const STATUS_NAO_PAGO = 'nao pago';
    const STATUS_PAGO = 'pago';
    const STATUS_REFUND = 'refund';
    const STATUS_PROCESSANDO = 'processando';
    const STATUS_SUCCESS = 'success';
    const STATUS_CONTESTACAO = 'contestacao';
    
    public static function getStatusOptions()
    {
        return [
            self::STATUS_NAO_PAGO => 'Não Pago',
            self::STATUS_PAGO => 'Pago',
            self::STATUS_REFUND => 'Refund',
            self::STATUS_PROCESSANDO => 'Processando',
            self::STATUS_SUCCESS => 'Success',
            self::STATUS_CONTESTACAO => 'Contestação',
        ];
    }

    // Relacionamento com GatewayPagamento
    public function gateway()
    {
        return $this->belongsTo(GatewayPagamento::class, 'gateway_id');
    }

    // Relacionamento com Tool
    public function tool()
    {
        return $this->belongsTo(Tool::class, 'tool_id');
    }
    
    // Relacionamento com Orcamento
    public function orcamento()
    {
        return $this->belongsTo(Orcamento::class, 'orcamento_id');
    }
    
    // Accessor to check if payment is approved
    public function getIsApprovedAttribute()
    {
        return $this->status === self::STATUS_PAGO || $this->status === self::STATUS_SUCCESS;
    }
}