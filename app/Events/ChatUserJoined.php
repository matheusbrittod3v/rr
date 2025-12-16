<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatUserJoined implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $roomCode;
    public string $userType;
    public ?int $userId;

    /**
     * Create a new event instance.
     */
    public function __construct(string $roomCode, string $userType, ?int $userId = null)
    {
        $this->roomCode = $roomCode;
        $this->userType = $userType;
        $this->userId = $userId;
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
        return 'user.joined';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'user_type' => $this->userType,
            'user_id' => $this->userId,
            'timestamp' => now()->toISOString(),
        ];
    }
}
