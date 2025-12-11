<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdempotencyKey extends Model
{
    protected $fillable = [
        'key',
        'operation_type',
        'payload',
        'processed_at'
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $table = 'idempotency_keys';
}