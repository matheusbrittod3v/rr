<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class GatewayPagamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'token',
        'user_id',
        'debug_mode',
    ];

    protected $casts = [
        'debug_mode' => 'boolean',
    ];

    // Relacionamento com o usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public static function userHasConfig($userId): bool
    {
        return self::where('user_id', $userId)->exists();
    }
}