<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Payment;
use App\Models\ChatRoom;
use App\Services\IdempotencyService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class SendClientPaymentNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $paymentId;
    protected $chatRoomId;
    protected $idempotencyKey;

    /**
     * Create a new job instance.
     *
     * @param int $paymentId
     * @param int $chatRoomId
     * @return void
     */
    public function __construct($paymentId, $chatRoomId)
    {
        $this->paymentId = $paymentId;
        $this->chatRoomId = $chatRoomId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Generate idempotency key for this job execution
        $this->idempotencyKey = IdempotencyService::createKey('send_client_payment_notification', [
            'payment_id' => $this->paymentId,
            'chat_room_id' => $this->chatRoomId
        ]);

        // Check if this job has already been processed
        if (IdempotencyService::hasBeenProcessed($this->idempotencyKey, 'send_client_payment_notification')) {
            Log::info('Idempotency check: SendClientPaymentNotificationJob already processed', [
                'payment_id' => $this->paymentId,
                'chat_room_id' => $this->chatRoomId,
                'idempotency_key' => $this->idempotencyKey
            ]);
            return;
        }

        try {
            // Get the payment and chat room
            $payment = Payment::find($this->paymentId);
            $chatRoom = ChatRoom::find($this->chatRoomId);
            
            if (!$payment || !$chatRoom) {
                Log::warning('Payment or chat room not found', [
                    'payment_id' => $this->paymentId,
                    'chat_room_id' => $this->chatRoomId
                ]);
                return;
            }

            // Send notification to client with client-specific password
            if ($payment->number_whatsapp && $payment->email) {
                $userId = $payment->gateway->user_id ?? 1;
                
                $message = "✅ Seu pagamento de *R$" . number_format($payment->valor, 2, ',', '.') . "* foi aprovado!\n\n" .
                           "💬 Sua sala de chat com o prestador de serviço foi criada.\n\n" .
                           "🔗 Link de acesso (Cliente): " . route('chat.login', $chatRoom->room_code) . "\n" .
                           "🔒 Senha do Cliente: {$chatRoom->client_password}\n\n" .
                           "Você será redirecionado automaticamente para a sala de chat.";

                Log::info('Preparing to send client notification', [
                    'user_id' => $userId,
                    'payment_id' => $payment->id,
                    'chat_room_id' => $chatRoom->id,
                    'message_length' => strlen($message),
                    'has_whatsapp' => !empty($payment->number_whatsapp),
                    'has_email' => !empty($payment->email),
                    'gateway_exists' => !empty($payment->gateway)
                ]);

                // Use NotificationService to send the notification
                $notificationService = app(NotificationService::class);
                
                $notificationResult = $notificationService->sendNotification(
                    $userId,
                    $payment->number_whatsapp,
                    $payment->email,
                    $message,
                    'Pagamento Aprovado - FRP Rent',
                    $this->idempotencyKey
                );
                
                Log::info('Client notification result', [
                    'user_id' => $userId,
                    'payment_id' => $payment->id,
                    'chat_room_id' => $chatRoom->id,
                    'success' => $notificationResult['success'] ?? false,
                    'message' => $notificationResult['message'] ?? 'No message',
                    'result' => $notificationResult
                ]);
                
                // Mark this operation as processed
                IdempotencyService::markAsProcessed($this->idempotencyKey, 'send_client_payment_notification', [
                    'payment_id' => $payment->id,
                    'chat_room_id' => $chatRoom->id,
                    'user_id' => $userId
                ]);
            } else {
                Log::warning('Client missing phone number or email', [
                    'payment_id' => $payment->id,
                    'chat_room_id' => $chatRoom->id,
                    'has_whatsapp' => !empty($payment->number_whatsapp),
                    'has_email' => !empty($payment->email)
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error in SendClientPaymentNotificationJob', [
                'payment_id' => $this->paymentId,
                'chat_room_id' => $this->chatRoomId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Mark as processed even in case of error to prevent retries
            try {
                IdempotencyService::markAsProcessed($this->idempotencyKey, 'send_client_payment_notification', [
                    'payment_id' => $this->paymentId,
                    'chat_room_id' => $this->chatRoomId,
                    'error' => $e->getMessage()
                ]);
            } catch (\Exception $markingException) {
                Log::error("Failed to mark idempotency key as processed after exception", [
                    'payment_id' => $this->paymentId,
                    'chat_room_id' => $this->chatRoomId,
                    'marking_exception' => $markingException->getMessage()
                ]);
            }
        }
    }
}