<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallSignal implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomCode;
    public $data;

    /**
     * Create a new event instance.
     *
     * @param string $roomCode
     * @param array $data Structure: ['type' => 'offer|answer|candidate|bye', 'payload' => ..., 'sender_type' => ...]
     */
    public function __construct(string $roomCode, array $data)
    {
        $this->roomCode = $roomCode;
        $this->data = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Usar canal privado para segurança
        return [
            new PrivateChannel('private-chat-room.' . $this->roomCode),
        ];
    }

    public function broadcastAs()
    {
        return 'call.signal';
    }
    
    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith()
    {
        return [
            'data' => $this->data,
        ];
    }
}
