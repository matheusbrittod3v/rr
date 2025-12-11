<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'room_code',
        'client_password',
        'provider_password',
    ];

    /**
     * Get the payment that owns the chat room.
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Get the messages for the chat room.
     */
    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * Get the client user (from payment data).
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the provider user (from payment data).
     */
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}
