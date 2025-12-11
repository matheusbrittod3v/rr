<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailConfig extends Model
{
    protected $fillable = [
        'user_id',
        'host',
        'port',
        'encryption_type',
        'email',
        'password',
        'type',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public static function userHasConfig($userId): bool
    {
        return self::where('user_id', $userId)->exists();
    }
}
