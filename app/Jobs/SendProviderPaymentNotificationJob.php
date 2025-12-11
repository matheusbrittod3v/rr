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

class SendProviderPaymentNotificationJob implements ShouldQueue
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
        $this->idempotencyKey = IdempotencyService::createKey('send_provider_payment_notification', [
            'payment_id' => $this->paymentId,
            'chat_room_id' => $this->chatRoomId
        ]);

        // Check if this job has already been processed
        if (IdempotencyService::hasBeenProcessed($this->idempotencyKey, 'send_provider_payment_notification')) {
            Log::info('Idempotency check: SendProviderPaymentNotificationJob already processed', [
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

            // Get the budget associated with this payment
            $orcamento = $payment->orcamento;
            
            if (!$orcamento) {
                Log::warning('No orcamento found for payment', [
                    'payment_id' => $payment->id,
                    'chat_room_id' => $chatRoom->id
                ]);
                return;
            }

            // Get the provider assigned to this budget with the provider relationship loaded
            $filaOrcamento = $orcamento->filaOrcamentoWithProvider;
            
            if (!$filaOrcamento || !$filaOrcamento->prestador_id) {
                Log::warning('FilaOrcamento missing or no prestador_id', [
                    'payment_id' => $payment->id,
                    'chat_room_id' => $chatRoom->id,
                    'fila_orcamento_exists' => $filaOrcamento ? 'yes' : 'no',
                    'prestador_id' => $filaOrcamento ? $filaOrcamento->prestador_id : null
                ]);
                return;
            }

            Log::info('FilaOrcamento found with provider ID', [
                'payment_id' => $payment->id,
                'chat_room_id' => $chatRoom->id,
                'fila_orcamento_id' => $filaOrcamento->id,
                'prestador_id' => $filaOrcamento->prestador_id
            ]);
            
            // Use the already loaded provider relationship
            $providerUser = $filaOrcamento->prestador;
            
            if (!$providerUser) {
                Log::warning('Provider user not found', [
                    'payment_id' => $payment->id,
                    'chat_room_id' => $chatRoom->id,
                    'prestador_id' => $filaOrcamento->prestador_id
                ]);
                return;
            }

            Log::info('Provider user found', [
                'payment_id' => $payment->id,
                'chat_room_id' => $chatRoom->id,
                'provider_user_id' => $providerUser->id,
                'provider_numero' => $providerUser->numero,
                'provider_email' => $providerUser->email
            ]);
            
            // Send notification to provider's phone number only
            if ($providerUser->numero) {
                $providerMessage = "💬 *Nova Sala de Chat Criada!*\n\n";
                $providerMessage .= "Um novo cliente pagou pelo serviço e a sala de chat foi criada.\n\n";
                $providerMessage .= "🔗 Link de acesso (Prestador): " . route('chat.login', $chatRoom->room_code) . "\n";
                $providerMessage .= "🔒 Senha do Prestador: {$chatRoom->provider_password}\n\n";
                $providerMessage .= "O cliente está aguardando para iniciar a comunicação.";

                Log::info('Preparing to send provider notification', [
                    'payment_id' => $payment->id,
                    'chat_room_id' => $chatRoom->id,
                    'provider_user_id' => $providerUser->id,
                    'message_length' => strlen($providerMessage),
                    'has_numero' => !empty($providerUser->numero)
                ]);

                // Use NotificationService to send WhatsApp notification only
                $notificationService = app(NotificationService::class);

                // Try to send using a fallback WhatsApp instance if the provider doesn't have one
                $whatsappInstance = \App\Models\WhatsApi::first();
                
                if ($whatsappInstance) {
                    $configOwnerUserId = (int) $whatsappInstance->user_id;
                    
                    $notificationResult = $notificationService->sendWhatsAppNotification(
                        $configOwnerUserId,
                        $providerUser->numero,
                        $providerMessage,
                        null,
                        $this->idempotencyKey
                    );
                    
                    Log::info('Provider WhatsApp notification result', [
                        'payment_id' => $payment->id,
                        'chat_room_id' => $chatRoom->id,
                        'provider_user_id' => $providerUser->id,
                        'success' => $notificationResult['success'] ?? false,
                        'message' => $notificationResult['message'] ?? 'No message',
                        'result' => $notificationResult
                    ]);
                    
                    // Mark this operation as processed
                    IdempotencyService::markAsProcessed($this->idempotencyKey, 'send_provider_payment_notification', [
                        'payment_id' => $payment->id,
                        'chat_room_id' => $chatRoom->id,
                        'provider_user_id' => $providerUser->id
                    ]);
                } else {
                    Log::warning('No WhatsApp instance available for sending provider notification', [
                        'payment_id' => $payment->id,
                        'chat_room_id' => $chatRoom->id,
                        'provider_user_id' => $providerUser->id
                    ]);
                }
            } else {
                Log::warning('Provider user missing phone number', [
                    'payment_id' => $payment->id,
                    'chat_room_id' => $chatRoom->id,
                    'provider_user_id' => $providerUser->id,
                    'provider_numero' => $providerUser->numero
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error in SendProviderPaymentNotificationJob', [
                'payment_id' => $this->paymentId,
                'chat_room_id' => $this->chatRoomId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Mark as processed even in case of error to prevent retries
            try {
                IdempotencyService::markAsProcessed($this->idempotencyKey, 'send_provider_payment_notification', [
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