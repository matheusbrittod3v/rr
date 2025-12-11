<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilaPrestador extends Model
{
    use HasFactory;

    protected $table = 'fila_prestadores';

    protected $fillable = [
        'user_id',
        'position'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}