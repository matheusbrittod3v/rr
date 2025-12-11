<?php

namespace App\Filament\Resources\WhatsApis\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\WhatsApi;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

class CheckConnectionStatusAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'checkConnectionStatus';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Checar Status da Sessão')
            ->icon('heroicon-o-question-mark-circle')
            ->color('info')
            ->action(function (): void {
                // Get the record from the action context
                $record = $this->getRecord();
                
                if (!$record) {
                    Notification::make()
                        ->title('Erro')
                        ->body('Registro não encontrado.')
                        ->danger()
                        ->send();
                    return;
                }

                try {
                    // Use the WhatsApp service to check connection status
                    $service = new WhatsAppService();
                    // We'll implement this method in the service
                    // For now, let's just show the current status
                    $statusMessage = $record->isConnected() ? 
                        'Conectado' : 
                        'Desconectado';
                    
                    $statusColor = $record->isConnected() ? 
                        'success' : 
                        'danger';
                    
                    Notification::make()
                        ->title('Status da Conexão')
                        ->body("Status atual: {$statusMessage}")
                        ->{$statusColor}()
                        ->send();
                    
                    Log::info('Connection status checked', [
                        'whats_api_id' => $record->id,
                        'user_id' => auth()->id(),
                        'status' => $record->connection_status
                    ]);
                } catch (\Exception $e) {
                    $errorMessage = "Exceção ao checar status: " . $e->getMessage();
                    
                    Notification::make()
                        ->title('Erro')
                        ->body($errorMessage)
                        ->danger()
                        ->send();
                    
                    Log::error($errorMessage, [
                        'whats_api_id' => $record->id,
                        'user_id' => auth()->id(),
                        'exception' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            });
    }
}