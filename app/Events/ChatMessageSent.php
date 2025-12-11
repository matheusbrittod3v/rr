<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\ChatMessage;

class ChatMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $roomId;
    public $userId;
    public $userName;

    /**
     * Create a new event instance.
     *
     * @param ChatMessage $message
     * @param int $roomId
     * @param int $userId
     * @param string $userName
     */
    public function __construct(ChatMessage $message, $roomId, $userId, $userName)
    {
        $this->message = $message;
        $this->roomId = $roomId;
        $this->userId = $userId;
        $this->userName = $userName;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('chat-room.' . $this->roomId),
            new PrivateChannel('chat-room.' . $this->roomId),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'chat.message.sent';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith()
    {
        return [
            'message_id' => $this->message->id,
            'room_id' => $this->roomId,
            'user_id' => $this->userId,
            'user_name' => $this->userName,
            'content' => $this->message->message,
            'sender_type' => $this->message->sender_type, // Include sender type
            'file_path' => $this->message->file_path,
            'file_type' => $this->message->file_type,
            'timestamp' => $this->message->created_at->toISOString(),
        ];
    }
}