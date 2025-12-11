<?php

namespace App\Filament\Resources\WhatsApis\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\WhatsApi;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

class CheckConnectionStatusAndUpdateAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'checkConnectionStatusAndUpdate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Verificar Conexão')
            ->icon('heroicon-o-arrow-path')
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
                    $result = $service->checkSessionStatus($record->id);
                    
                    if ($result['success']) {
                        // Parse the status from the response
                        $statusData = $result['data'] ?? [];
                        $isConnected = false;
                        
                        // Check various possible status fields
                        if (isset($statusData['connected']) && $statusData['connected']) {
                            $isConnected = true;
                        } elseif (isset($statusData['status']) && $statusData['status'] === 'connected') {
                            $isConnected = true;
                        } elseif (isset($statusData['state']) && $statusData['state'] === 'CONNECTED') {
                            $isConnected = true;
                        }
                        
                        if ($isConnected) {
                            // Update the connection status to connected
                            $record->connection_status = WhatsApi::STATUS_CONNECTED;
                            $record->save();
                            
                            Notification::make()
                                ->title('Conectado!')
                                ->body('WhatsApp conectado com sucesso!')
                                ->success()
                                ->send();
                            
                            Log::info('WhatsApp connected successfully', [
                                'whats_api_id' => $record->id,
                                'user_id' => auth()->id(),
                                'status_data' => $statusData
                            ]);
                        } else {
                            // Keep the connecting status or set to disconnected based on the actual status
                            $currentStatus = 'connecting';
                            
                            // Check if explicitly disconnected
                            if (isset($statusData['connected']) && !$statusData['connected']) {
                                $currentStatus = WhatsApi::STATUS_DISCONNECTED;
                            } elseif (isset($statusData['status']) && $statusData['status'] === 'disconnected') {
                                $currentStatus = WhatsApi::STATUS_DISCONNECTED;
                            } elseif (isset($statusData['state']) && $statusData['state'] === 'DISCONNECTED') {
                                $currentStatus = WhatsApi::STATUS_DISCONNECTED;
                            }
                            
                            $record->connection_status = $currentStatus;
                            $record->save();
                            
                            $statusMessage = $currentStatus === WhatsApi::STATUS_DISCONNECTED ? 
                                'Desconectado' : 
                                'Aguardando conexão';
                                
                            Notification::make()
                                ->title('Status da Conexão')
                                ->body("Status atual: {$statusMessage}. Continue escaneando o QR Code.")
                                ->warning()
                                ->send();
                            
                            Log::info('Connection status checked', [
                                'whats_api_id' => $record->id,
                                'user_id' => auth()->id(),
                                'status' => $currentStatus,
                                'status_data' => $statusData
                            ]);
                        }
                    } else {
                        $errorMessage = "Falha ao verificar status da conexão. " . $result['message'];
                        
                        Notification::make()
                            ->title('Erro')
                            ->body($errorMessage)
                            ->danger()
                            ->send();
                        
                        Log::error($errorMessage, [
                            'whats_api_id' => $record->id,
                            'user_id' => auth()->id(),
                            'response_status' => $result['status'] ?? 'unknown',
                            'response_data' => $result['data'] ?? null
                        ]);
                    }
                } catch (\Exception $e) {
                    $errorMessage = "Exceção ao verificar status: " . $e->getMessage();
                    
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