<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChatMessage $message;
    public string $roomCode;

    /**
     * Create a new event instance.
     */
    public function __construct(ChatMessage $message, string $roomCode)
    {
        $this->message = $message;
        $this->roomCode = $roomCode;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('chat-room.' . $this->roomCode);
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'message' => $this->message->message,
            'sender_type' => $this->message->sender_type,
            'sender_id' => $this->message->sender_id,
            'file_path' => $this->message->file_path,
            'file_type' => $this->message->file_type,
            'created_at' => $this->message->created_at->toISOString(),
            'formatted_time' => $this->message->getFormattedTime(),
        ];
    }
}
