<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatSignal extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_room_id',
        'sender_type',
        'type',
        'payload',
        'processed',
    ];

    protected $casts = [
        'processed' => 'boolean',
    ];

    public function chatRoom()
    {
        return $this->belongsTo(ChatRoom::class);
    }
}
