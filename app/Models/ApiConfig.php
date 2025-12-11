<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ApiConfig extends Model
{
    use HasFactory;

    protected $table = 'apiconfig';

    protected $fillable = [
        'user_id',
        'key',
        'username',
        'host',
        'value',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the API configuration.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include active API configurations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if the user already has an API configuration.
     */
    public static function userHasConfig($userId)
    {
        return self::where('user_id', $userId)->exists();
    }

    /**
     * Get the API configuration for a user.
     */
    public static function getUserConfig($userId)
    {
        return self::where('user_id', $userId)->first();
    }
}