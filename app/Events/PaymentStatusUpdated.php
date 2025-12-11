<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Payment;

class PaymentStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $payment;
    public $status;
    public $message;

    /**
     * Create a new event instance.
     *
     * @param Payment $payment
     * @param string $status
     * @param string $message
     */
    public function __construct(Payment $payment, $status, $message = '')
    {
        $this->payment = $payment;
        $this->status = $status;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Handle case where payment might not have an ID (e.g., for testing)
        $paymentId = $this->payment->id ?? 0;
        
        return [
            new Channel('payments'),
            new PrivateChannel('payments.' . $paymentId),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'payment.status.updated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith()
    {
        return [
            'payment_id' => $this->payment->id ?? null,
            'status' => $this->status,
            'message' => $this->message,
            'timestamp' => now()->toISOString(),
        ];
    }
}