<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RegistrationLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'roles',
        'is_provider',
        'created_by',
        'expires_at',
        'max_uses',
        'uses_count',
    ];

    protected $casts = [
        'roles' => 'array',
        'is_provider' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * Generate a unique token
     */
    public static function generateToken(): string
    {
        return Str::random(32);
    }

    /**
     * Check if link is still valid
     */
    public function isValid(): bool
    {
        // Check expiration
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        // Check max uses
        if ($this->max_uses && $this->uses_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    /**
     * Increment usage count
     */
    public function incrementUsage(): void
    {
        $this->increment('uses_count');
    }

    /**
     * Get the user who created this link
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the full registration URL
     */
    public function getUrlAttribute(): string
    {
        return url("/register/{$this->token}");
    }
}
