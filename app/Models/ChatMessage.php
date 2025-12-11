<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_room_id',
        'sender_id',
        'sender_type',
        'message',
        'file_path',
        'file_type',
    ];

    /**
     * Get the chat room that owns the message.
     */
    public function chatRoom()
    {
        return $this->belongsTo(ChatRoom::class);
    }

    /**
     * Get the sender user.
     */
    public function sender()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include client messages.
     */
    public function scopeFromClient($query)
    {
        return $query->where('sender_type', 'client');
    }

    /**
     * Scope a query to only include provider messages.
     */
    public function scopeFromProvider($query)
    {
        return $query->where('sender_type', 'provider');
    }
}