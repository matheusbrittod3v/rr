<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use App\Models\Payment;
use App\Services\NotificationService;
use App\Services\IdempotencyService;
use Filament\Actions;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;
    
    protected ?NotificationService $notificationService = null;
    
    protected string $oldStatus = '';
    
    public function boot()
    {
        $this->notificationService = app(NotificationService::class);
    }
    
    public function mount(int | string $record): void
    {
        parent::mount($record);
        // Store the original status when mounting the page
        $this->oldStatus = $this->record->status;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function afterSave(): void
    {
        // Check if status has changed
        $oldStatus = $this->oldStatus;
        $newStatus = $this->record->status;
        
        // Check if user wants to send notification (from form data)
        $sendNotification = $this->data['send_notification'] ?? false;
        
        // If status changed and user wants to send notification
        if ($oldStatus !== $newStatus && $sendNotification) {
            $this->sendStatusChangeNotification($this->record, $oldStatus, $newStatus);
        }
    }
    
    protected function sendStatusChangeNotification(Payment $payment, string $oldStatus, string $newStatus): void
    {
        try {
            // Get the gateway to determine the user ID
            $gateway = $payment->gateway;
            $userId = $gateway->user_id ?? 1; // Default to user ID 1 if not found
            
            // Get status labels
            $statusOptions = Payment::getStatusOptions();
            $oldStatusLabel = $statusOptions[$oldStatus] ?? $oldStatus;
            $newStatusLabel = $statusOptions[$newStatus] ?? $newStatus;
            
            // Prepare the message
            $message = "O status do seu pagamento foi atualizado:\n\n";
            $message .= "ID do Pagamento: {$payment->id}\n";
            $message .= "Status Anterior: {$oldStatusLabel}\n";
            $message .= "Novo Status: {$newStatusLabel}\n";
            $message .= "Valor: R$ " . number_format($payment->valor, 2, ',', '.') . "\n";
            $message .= "Ferramenta: " . ($payment->tool->nome ?? 'N/A');
            
            $subject = "Atualização de Status do Pagamento - ID: {$payment->id}";
            
            // Send notification
            if ($this->notificationService && $payment->number_whatsapp && $payment->email) {
                // Generate idempotency key for this notification
                $idempotencyKey = IdempotencyService::createKey('payment_status_update', [
                    'payment_id' => $payment->id,
                    'user_id' => $userId,
                    'new_status' => $newStatus
                ]);

                $result = $this->notificationService->sendNotification(
                    $userId,
                    $payment->number_whatsapp,
                    $payment->email,
                    $message,
                    $subject,
                    $idempotencyKey
                );
                
                if ($result['success']) {
                    Notification::make()
                        ->title('Notificação enviada')
                        ->body('A notificação de atualização de status foi enviada com sucesso para o usuário.')
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title('Falha no envio')
                        ->body('Não foi possível enviar a notificação de atualização de status.')
                        ->danger()
                        ->send();
                }
            } else {
                Notification::make()
                    ->title('Notificação não enviada')
                    ->body('Não foi possível enviar a notificação. Verifique se o serviço de notificação está configurado e se o usuário tem número de WhatsApp e email.')
                    ->warning()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Erro')
                ->body('Ocorreu um erro ao enviar a notificação: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}