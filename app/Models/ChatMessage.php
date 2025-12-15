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

    /**
     * Get file extension from file path.
     */
    public function getFileExtension(): ?string
    {
        if (!$this->file_path) {
            return null;
        }
        
        return strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
    }

    /**
     * Check if the attached file is an image.
     */
    public function isImage(): bool
    {
        $extension = $this->getFileExtension();
        if (!$extension) {
            return false;
        }
        
        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    /**
     * Get safe file path for display (validated).
     */
    public function getSafeFilePath(): ?string
    {
        if (!$this->file_path) {
            return null;
        }
        
        // Validate path to prevent path traversal
        $path = $this->file_path;
        
        // Remove any directory traversal attempts
        $path = str_replace(['../', '..\\'], '', $path);
        
        // Ensure path starts with allowed directory
        if (!str_starts_with($path, 'chat_files/')) {
            return null;
        }
        
        return $path;
    }

    /**
     * Get formatted time for display.
     */
    public function getFormattedTime(): string
    {
        return $this->created_at->format('H:i');
    }
}