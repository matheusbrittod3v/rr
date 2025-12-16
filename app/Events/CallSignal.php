<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallSignal implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $roomCode;
    public string $senderType;
    public string $signalType; // 'offer', 'answer', 'ice-candidate', 'call-start', 'call-end', 'call-reject'
    public ?array $signalData;

    /**
     * Create a new event instance.
     */
    public function __construct(string $roomCode, string $senderType, string $signalType, ?array $signalData = null)
    {
        $this->roomCode = $roomCode;
        $this->senderType = $senderType;
        $this->signalType = $signalType;
        $this->signalData = $signalData;
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
        return 'call.signal';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'sender_type' => $this->senderType,
            'signal_type' => $this->signalType,
            'signal_data' => $this->signalData,
            'timestamp' => now()->toISOString(),
        ];
    }
}
