<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\FilaOrcamento;
use App\Models\Orcamento;
use App\Models\WhatsApi;
use App\Services\NotificationService;
use App\Services\IdempotencyService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NotifyProviderNewBudgetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $budgetQueueId;
    protected $idempotencyKey;

    /**
     * Create a new job instance.
     *
     * @param int $budgetQueueId
     * @return void
     */
    public function __construct($budgetQueueId)
    {
        $this->budgetQueueId = $budgetQueueId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Generate idempotency key for this job execution
        $this->idempotencyKey = IdempotencyService::createKey('notify_provider_budget', [
            'budget_queue_id' => $this->budgetQueueId
        ]);

        try {
            // Check if this notification has already been processed
            if (IdempotencyService::hasBeenProcessed($this->idempotencyKey, 'notify_provider_budget')) {
                Log::info('Idempotency check: Provider notification already sent for budget queue', [
                    'budget_queue_id' => $this->budgetQueueId,
                    'idempotency_key' => $this->idempotencyKey
                ]);
                return;
            }

            Log::info('Processing NotifyProviderNewBudgetJob', ['budget_queue_id' => $this->budgetQueueId]);

            // Get the budget queue entry with related models
            $budgetQueue = FilaOrcamento::with(['orcamento', 'prestador'])->find($this->budgetQueueId);

            if (!$budgetQueue) {
                Log::warning("Budget queue entry not found: {$this->budgetQueueId}");
                return;
            }

            $orcamento = $budgetQueue->orcamento;
            $prestador = $budgetQueue->prestador;

            if (!$orcamento || !$prestador) {
                Log::warning("Provider not assigned for budget queue: {$this->budgetQueueId}");
                return;
            }

            // Generate or reuse unique ID for the budget
            if (empty($orcamento->id_orcamento)) {
                // Generate a unique ID for this budget (8 chars + 2 digits)
                $uniqueId = strtoupper(trim(Str::random(8) . rand(10, 99)));
                
                // Save it to the database
                $orcamento->id_orcamento = $uniqueId;
                $orcamento->save();
                
                Log::info('Generated and persisted unique id to orcamento', [
                    'orcamento_id' => $orcamento->id,
                    'id_orcamento' => $uniqueId,
                ]);
            } else {
                $uniqueId = $orcamento->id_orcamento;
                Log::info('Reusing existing id_orcamento for notification', [
                    'orcamento_id' => $orcamento->id,
                    'id_orcamento' => $uniqueId,
                ]);
            }

            // Get the WhatsApp instance for the provider. If not found, fallback to the first available instance.
            $whatsappInstance = WhatsApi::where('user_id', $prestador->id)->first();

            if (!$whatsappInstance) {
                Log::warning("WhatsApp instance not found for provider: {$prestador->id}. Attempting fallback to any available instance.");
                $whatsappInstance = WhatsApi::first();

                if ($whatsappInstance) {
                    Log::info("Using fallback WhatsApp instance '{$whatsappInstance->instance_name}' (user_id={$whatsappInstance->user_id}) for provider {$prestador->id}");
                } else {
                    Log::error("No WhatsApp instance available to send notification for provider: {$prestador->id}");
                    return;
                }
            }

            // Parse additional information
            $informacoesAdicionais = is_string($orcamento->informacoes_adicionais)
                ? json_decode($orcamento->informacoes_adicionais, true)
                : $orcamento->informacoes_adicionais;

            // Build the message
            $message = $this->buildNotificationMessage($uniqueId, $orcamento, $informacoesAdicionais);

            // Clean provider phone
            $cleanPhone = preg_replace('/\D/', '', $prestador->numero);
            if (!str_starts_with($cleanPhone, '55')) {
                $cleanPhone = '55' . $cleanPhone;
            }

            // Use the NotificationService to send the WhatsApp message.
            // We pass the owner `user_id` of the selected WhatsApi instance so NotificationService
            // loads the correct configuration (host, key, instance_name, etc.).
            /** @var NotificationService $notificationService */
            $notificationService = app(NotificationService::class);

            $configOwnerUserId = (int) $whatsappInstance->user_id;
            Log::info('Sending WhatsApp notification', [
                'budget_queue_id' => $this->budgetQueueId,
                'provider_id' => $prestador->id,
                'config_owner_user_id' => $configOwnerUserId,
                'phone_number' => $cleanPhone,
                'idempotency_key' => $this->idempotencyKey
            ]);
            
            $result = $notificationService->sendWhatsAppNotification($configOwnerUserId, $cleanPhone, $message, null, $this->idempotencyKey);
            
            Log::info('Received notification result', [
                'budget_queue_id' => $this->budgetQueueId,
                'provider_id' => $prestador->id,
                'result' => $result,
                'result_type' => gettype($result),
                'result_keys' => is_array($result) ? array_keys($result) : null
            ]);

            // Check if the notification was sent successfully
            // We're being more lenient here to handle cases where the message is sent but the response format varies
            $isSuccess = false;
            $isAlreadySent = false;
            
            if (is_array($result)) {
                // Check for explicit success
                $isSuccess = !empty($result['success']) && $result['success'] === true;
                
                // Also check if there's a success message that indicates the message was already sent (idempotency)
                $isAlreadySent = isset($result['message']) && strpos($result['message'], 'already sent') !== false;
            }
            
            Log::info('Result evaluation', [
                'budget_queue_id' => $this->budgetQueueId,
                'is_success' => $isSuccess,
                'is_already_sent' => $isAlreadySent,
                'result_details' => $result
            ]);
            
            if ($isSuccess || $isAlreadySent) {
                Log::info("WhatsApp notification sent for budgetQueue {$this->budgetQueueId}", [
                    'provider_id' => $prestador->id,
                    'unique_id' => $uniqueId,
                    'result' => $result,
                ]);
                
                // Mark this operation as processed
                IdempotencyService::markAsProcessed($this->idempotencyKey, 'notify_provider_budget', [
                    'budget_queue_id' => $this->budgetQueueId,
                    'provider_id' => $prestador->id,
                    'unique_id' => $uniqueId,
                ]);
                
                // Job completed successfully
                return;
            } else {
                Log::error("WhatsApp notification failed for budgetQueue {$this->budgetQueueId}", [
                    'provider_id' => $prestador->id,
                    'result' => $result,
                ]);
                
                // Even if the notification failed, we shouldn't fail the job if the message was sent
                // This prevents duplicate notifications due to job retries
                $errorMessage = 'Unknown error';
                if (is_array($result)) {
                    $errorMessage = $result['message'] ?? $result['error'] ?? 'Notification failed';
                } elseif (is_string($result)) {
                    $errorMessage = $result;
                }
                
                Log::warning("Job completed with notification issue but not failing the job", [
                    'budget_queue_id' => $this->budgetQueueId,
                    'error_message' => $errorMessage
                ]);
                
                // Still mark as processed to prevent retries
                IdempotencyService::markAsProcessed($this->idempotencyKey, 'notify_provider_budget', [
                    'budget_queue_id' => $this->budgetQueueId,
                    'provider_id' => $prestador->id,
                    'unique_id' => $uniqueId,
                    'error_message' => $errorMessage
                ]);
                
                // Don't throw an exception, just complete the job
                return;
            }
        } catch (\Exception $e) {
            Log::error("Error notifying provider about new budget: {$e->getMessage()}", [
                'budget_queue_id' => $this->budgetQueueId,
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Even if there's an exception, mark the job as processed to prevent duplicate notifications
            // This is a safety measure to prevent the same notification from being sent multiple times
            try {
                IdempotencyService::markAsProcessed($this->idempotencyKey, 'notify_provider_budget', [
                    'budget_queue_id' => $this->budgetQueueId,
                    'error_message' => $e->getMessage(),
                    'exception_type' => get_class($e)
                ]);
            } catch (\Exception $markingException) {
                Log::error("Failed to mark idempotency key as processed after exception", [
                    'budget_queue_id' => $this->budgetQueueId,
                    'marking_exception' => $markingException->getMessage()
                ]);
            }
            
            // We're not re-throwing the exception to prevent job failure
            // This prevents the job from being retried and potentially sending duplicate notifications
            Log::warning("Job completed with exception but not failing to prevent duplicate notifications", [
                'budget_queue_id' => $this->budgetQueueId
            ]);
        }
    }

    /**
     * Build the notification message for the provider
     *
     * @param string $uniqueId
     * @param \App\Models\Orcamento $orcamento
     * @param array $informacoesAdicionais
     * @return string
     */
    private function buildNotificationMessage($uniqueId, $orcamento, $informacoesAdicionais)
    {
        $message = "📋 *NOVO ORÇAMENTO DISPONÍVEL*\n\n";
        $message .= "*ID: {$uniqueId}*\n\n";

        // Additional information
        if ($informacoesAdicionais && !empty($informacoesAdicionais)) {
            $message .= "📝 *Informações Adicionais:*\n";
            foreach ($informacoesAdicionais as $key => $value) {
                // Format the key
                $formattedKey = ucfirst(str_replace('_', ' ', $key));
                
                if (is_array($value)) {
                    $message .= "{$formattedKey}: " . implode(', ', $value) . "\n";
                } else {
                    $message .= "{$formattedKey}: {$value}\n";
                }
            }
            $message .= "\n";
        }

        // Action request
        $message .= " *Responda no grupo esse e so um alerta* \n\n";
        

        return $message;
    }

    /**
     * Send the WhatsApp notification
     *
     * @param \App\Models\WhatsApi $whatsappInstance
     * @param string $phoneNumber
     * @param string $message
     * @param string $uniqueId
     * @param int $budgetQueueId
     * @return void
     */
    private function sendWhatsAppNotification($whatsappInstance, $phoneNumber, $message, $uniqueId, $budgetQueueId)
    {
        // Deprecated: sending is handled by NotificationService
        Log::debug('sendWhatsAppNotification called but sending is delegated to NotificationService', [
            'budget_queue_id' => $budgetQueueId,
        ]);
    }
}