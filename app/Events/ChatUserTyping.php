<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatUserTyping implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $roomCode;
    public string $userType;
    public bool $isTyping;

    /**
     * Create a new event instance.
     */
    public function __construct(string $roomCode, string $userType, bool $isTyping = true)
    {
        $this->roomCode = $roomCode;
        $this->userType = $userType;
        $this->isTyping = $isTyping;
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
        return 'user.typing';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'user_type' => $this->userType,
            'is_typing' => $this->isTyping,
        ];
    }
}
