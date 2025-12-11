<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $table = 'refunds';

    protected $fillable = [
        'link',
        'id_pedido',
        'numero',
        'email',
        'relato_problema',
        'tool_id',
    ];

    protected $casts = [
        //
    ];

    // Define relationship with Tool model
    public function tool()
    {
        return $this->belongsTo(Tool::class);
    }
}