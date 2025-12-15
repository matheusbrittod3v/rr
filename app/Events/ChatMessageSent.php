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
    public $roomCode;
    public $userId;
    public $userName;

    /**
     * Create a new event instance.
     *
     * @param ChatMessage $message
     * @param string $roomCode
     * @param int|null $userId
     * @param string|null $userName
     */
    public function __construct(ChatMessage $message, $roomCode, $userId = null, $userName = null)
    {
        $this->message = $message;
        $this->roomCode = $roomCode;
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
            new PrivateChannel('private-chat-room.' . $this->roomCode),
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
            'message' => [
                'id' => $this->message->id,
                'message' => $this->message->message,
                'sender_type' => $this->message->sender_type,
                'file_path' => $this->message->file_path,
                'file_type' => $this->message->file_type,
                'created_at' => $this->message->created_at->toISOString(),
            ]
        ];
    }
}