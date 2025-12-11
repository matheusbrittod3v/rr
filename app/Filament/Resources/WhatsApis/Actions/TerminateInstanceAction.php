<?php

namespace App\Filament\Resources\WhatsApis\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\WhatsApi;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

class TerminateInstanceAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'terminateInstance';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Terminar Instância')
            ->icon('heroicon-o-stop')
            ->color('danger')
            ->requiresConfirmation()
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
                    // Use the WhatsApp service to terminate session
                    $service = new WhatsAppService();
                    $result = $service->terminateSession($record->id);
                    
                    // Independentemente do resultado da API, limpamos os dados locais
                    // Atualizamos o status e limpamos os campos da instância
                    $record->connection_status = WhatsApi::STATUS_DISCONNECTED;
                    $record->instance_name = null;
                    $record->numero_instancia = null;
                    $record->save();
                    
                    if ($result['success']) {
                        Notification::make()
                            ->title('Sucesso')
                            ->body('Instância terminada com sucesso!')
                            ->success()
                            ->send();
                        
                        Log::info('Successfully terminated instance', [
                            'whats_api_id' => $record->id,
                            'user_id' => auth()->id(),
                            'response_status' => $result['status']
                        ]);
                    } else {
                        // Mesmo com erro na API, informamos que os dados locais foram limpos
                        $errorMessage = "A API retornou um erro, mas a instância foi limpa localmente. " . $result['message'];
                        
                        Notification::make()
                            ->title('Aviso')
                            ->body($errorMessage)
                            ->warning()
                            ->send();
                        
                        Log::warning('Instance terminated locally despite API error', [
                            'whats_api_id' => $record->id,
                            'user_id' => auth()->id(),
                            'api_error_message' => $result['message'],
                            'response_status' => $result['status'] ?? 'unknown',
                            'response_data' => $result['data'] ?? null
                        ]);
                    }
                } catch (\Exception $e) {
                    // Mesmo com exceção, limpamos os dados locais
                    try {
                        $record->connection_status = WhatsApi::STATUS_DISCONNECTED;
                        $record->instance_name = null;
                        $record->numero_instancia = null;
                        $record->save();
                        
                        $errorMessage = "Exceção ocorrida, mas a instância foi limpa localmente. " . $e->getMessage();
                        
                        Notification::make()
                            ->title('Aviso')
                            ->body($errorMessage)
                            ->warning()
                            ->send();
                        
                        Log::error('Exception occurred but instance cleaned locally', [
                            'whats_api_id' => $record->id,
                            'user_id' => auth()->id(),
                            'exception' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                    } catch (\Exception $cleanupException) {
                        // Se nem a limpeza local funcionar, registramos o erro
                        $errorMessage = "Falha ao limpar instância localmente: " . $cleanupException->getMessage();
                        
                        Notification::make()
                            ->title('Erro')
                            ->body($errorMessage)
                            ->danger()
                            ->send();
                        
                        Log::error('Failed to clean instance locally', [
                            'whats_api_id' => $record->id,
                            'user_id' => auth()->id(),
                            'exception' => $cleanupException->getMessage(),
                            'trace' => $cleanupException->getTraceAsString()
                        ]);
                    }
                }
            });
    }
}