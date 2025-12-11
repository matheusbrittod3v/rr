<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class WhatsApi extends Model
{
    protected $table = 'whats_apis';
    
    // Connection status constants
    const STATUS_DISCONNECTED = 'disconnected';
    const STATUS_CONNECTED = 'connected';
    
    protected $fillable = [
        'user_id',
        'name',
        'host',
        'key',
        'type',
        'authenticate',
        'instance_name',
        'numero_instancia',
        'connection_status',
        'selected_groups',
    ];
    
    protected $casts = [
        'selected_groups' => 'array',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public static function userHasConfig($userId): bool
    {
        return self::where('user_id', $userId)->exists();
    }
    
    /**
     * Check if the API is connected
     */
    public function isConnected(): bool
    {
        return $this->connection_status === self::STATUS_CONNECTED;
    }
    
    /**
     * Check if the API is disconnected
     */
    public function isDisconnected(): bool
    {
        return $this->connection_status === self::STATUS_DISCONNECTED;
    }
    
    /**
     * Get selected groups
     */
    public function getSelectedGroups(): array
    {
        return $this->selected_groups ?? [];
    }
    
    /**
     * Set selected groups
     */
    public function setSelectedGroups(array $groups): void
    {
        $this->selected_groups = $groups;
        $this->save();
    }
}