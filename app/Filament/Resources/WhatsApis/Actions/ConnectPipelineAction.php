<?php

namespace App\Filament\Resources\WhatsApis\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\WhatsApi;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ConnectPipelineAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'connectPipeline';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Iniciar Sessão')
            ->icon('heroicon-o-play')
            ->color('primary')
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
                    // Generate a new instance name
                    $instanceName = Str::uuid()->toString();
                    
                    // Use the WhatsApp service to create session first, passing the instance name
                    $service = new WhatsAppService();
                    $result = $service->startSession($record->id, $instanceName);
                    
                    if ($result['success']) {
                        // Only save the instance name if session creation was successful
                        $record->instance_name = $instanceName;
                        $record->save();
                        
                        Notification::make()
                            ->title('Sessão Iniciada')
                            ->body("Sessão iniciada com sucesso!\nID: {$instanceName}")
                            ->success()
                            ->send();
                        
                        Log::info('Successfully started session', [
                            'whats_api_id' => $record->id,
                            'user_id' => auth()->id(),
                            'instance_name' => $instanceName,
                            'response_status' => $result['status']
                        ]);
                    } else {
                        $errorMessage = "Falha ao iniciar sessão na API. " . ($result['message'] ?? 'Unknown error');
                        
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
                    $errorMessage = "Exceção ao iniciar sessão: " . $e->getMessage();
                    
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