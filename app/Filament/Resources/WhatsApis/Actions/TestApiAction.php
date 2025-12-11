<?php

namespace App\Filament\Resources\WhatsApis\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use App\Models\WhatsApi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestApiAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'testApi';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Test API')
            ->icon('heroicon-o-paper-airplane')
            ->form([
                Placeholder::make('instructions')
                    ->label('Instructions')
                    ->content(
                        'Use the complete message endpoint in the host field and pass the instance variable in curly braces. ' .
                        'For example: https://api-zapzap.com/{instance_name}/send-message'
                    ),
                TextInput::make('phone_number')
                    ->label('Phone Number')
                    ->required()
                    ->placeholder('Enter phone number to test (e.g., 553499442627 or 6281288888888)'),
            ])
            ->action(function (array $data): void {
                $whatsApis = WhatsApi::all();

                foreach ($whatsApis as $record) {
                    Log::info('Starting WhatsApp API test', [
                        'user_id' => auth()->id(),
                        'whats_api_id' => $record->id,
                        'phone_number' => $data['phone_number']
                    ]);

                    // Monta a URL
                    if (strpos($record->host, '{instance}') !== false) {
                        $url = str_replace('{instance}', $record->instance_name, $record->host);
                    } else {
                        $url = rtrim($record->host, '/') . '/' . $record->instance_name;
                    }

                    Log::info('URL built for API request', ['url' => $url]);

                    // Dados da requisição
                    $requestData = [
                        'chatId' => $data['phone_number'] . '@c.us',
                        'contentType' => $record->type,
                        'content' => 'Test message sent successfully from renttool.com'
                    ];

                    Log::info('Request data prepared', ['request_data' => $requestData]);

                    // Cabeçalhos
                    $headers = [
                        'accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ];

                    switch ($record->authenticate) {
                        case 'bearer':
                            $headers['Authorization'] = 'Bearer ' . $record->key;
                            break;
                        case 'x-api-key':
                            $headers['x-api-key'] = $record->key;
                            break;
                        case 'basic':
                            $headers['Authorization'] = 'Basic ' . base64_encode($record->key);
                            break;
                    }

                    Log::info('Headers prepared', ['headers' => $headers]);

                    try {
                        $response = Http::withHeaders($headers)->post($url, $requestData);

                        Log::info('Received response from WhatsApp API', [
                            'status' => $response->status(),
                            'response_body' => $response->body(),
                            'response_headers' => $response->headers()
                        ]);

                        if ($response->successful()) {
                            Notification::make()
                                ->title('Success')
                                ->body("Mensagem enviada com sucesso via API ID {$record->id}!")
                                ->success()
                                ->send();

                            Log::info("Test message sent successfully for API ID {$record->id}");
                        } else {
                            $errorMessage = "Falha ao enviar mensagem (API ID {$record->id}). " .
                                "Status: {$response->status()} - Response: {$response->body()}";

                            Notification::make()
                                ->title('Error')
                                ->body($errorMessage)
                                ->danger()
                                ->send();

                            Log::error($errorMessage);
                        }
                    } catch (\Exception $e) {
                        $errorMessage = "Exceção na API ID {$record->id}: " . $e->getMessage();

                        Notification::make()
                            ->title('Error')
                            ->body($errorMessage)
                            ->danger()
                            ->send();

                        Log::error($errorMessage, ['trace' => $e->getTraceAsString()]);
                    }
                }
            });
    }
}