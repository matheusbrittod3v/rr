<?php

namespace App\Services;

use App\Models\WhatsApi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected function getBaseUrl(WhatsApi $whatsApi): string
    {
        return rtrim($whatsApi->host, '/');
    }

    protected function getHeaders(WhatsApi $whatsApi): array
    {
        $headers = [
            'accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        switch ($whatsApi->authenticate) {
            case 'bearer':
                $headers['Authorization'] = 'Bearer ' . $whatsApi->key;
                break;

            case 'x-api-key':
                $headers['x-api-key'] = $whatsApi->key;
                break;

            case 'basic':
                $headers['Authorization'] = 'Basic ' . base64_encode($whatsApi->key);
                break;
        }

        return $headers;
    }

    /* -------------------------------------------
        START SESSION
    --------------------------------------------*/
    public function startSession(int $whatsApiId, string $instanceName): array
    {
        Log::info('🟦 *Iniciando sessão*', [
            'whats_api_id' => $whatsApiId,
            'instance_name' => $instanceName,
        ]);

        $whatsApi = WhatsApi::find($whatsApiId);

        if (!$whatsApi) {
            return [
                'success' => false,
                'message' => 'Configuração da API não encontrada.',
            ];
        }

        // URL para iniciar sessão - usando o endpoint padrão da API
        $url = $this->getBaseUrl($whatsApi) . "/session/start/{$instanceName}";
        $headers = $this->getHeaders($whatsApi);

        Log::info('🔵 Enviando requisição para iniciar sessão → API', [
            'url' => $url,
            'headers' => array_keys($headers),
        ]);

        try {
            $response = Http::withHeaders($headers)->get($url);

            Log::info('🟩 Resposta recebida (Start Session)', [
                'status' => $response->status(),
                'body_preview' => substr($response->body(), 0, 200),
            ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => "Falha ao iniciar sessão: {$response->status()}",
                ];
            }

            $data = $response->json() ?? [];

            Log::info('🟢 Sessão iniciada com sucesso', [
                'response_data' => $data,
            ]);

            return [
                'success' => true,
                'status' => $response->status(),
                'message' => 'Sessão iniciada com sucesso',
                'data' => $data,
            ];

        } catch (\Exception $e) {
            Log::error('🔥 ERRO ao iniciar sessão', [
                'whats_api_id' => $whatsApiId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Erro inesperado: ' . $e->getMessage(),
            ];
        }
    }

    /* -------------------------------------------
        TERMINATE SESSION
    --------------------------------------------*/
    public function terminateSession(int $whatsApiId): array
    {
        Log::info('🟥 *Terminando sessão*', [
            'whats_api_id' => $whatsApiId,
        ]);

        $whatsApi = WhatsApi::find($whatsApiId);

        if (!$whatsApi) {
            return [
                'success' => false,
                'message' => 'Configuração da API não encontrada.',
            ];
        }

        $sessionId = $whatsApi->instance_name;
        
        // Corrigindo o endpoint - talvez seja /terminate/{sessionId} diretamente
        $url = $this->getBaseUrl($whatsApi) . "/session/terminate/{$sessionId}";
        $headers = $this->getHeaders($whatsApi);

        Log::info('🔵 Enviando requisição para terminar sessão → API', [
            'url' => $url,
            'headers' => array_keys($headers),
        ]);

        try {
            // Usando GET conforme a documentação
            $response = Http::withHeaders($headers)->get($url);

            Log::info('🟩 Resposta recebida (Terminate Session)', [
                'status' => $response->status(),
                'body_preview' => substr($response->body(), 0, 500),
            ]);

            // Verificando se a resposta contém um erro específico
            $responseData = $response->json() ?? [];
            
            // Se a API retornar sucesso como false, tratamos como erro
            if (isset($responseData['success']) && !$responseData['success']) {
                $errorMessage = $responseData['error'] ?? $responseData['message'] ?? 'Erro desconhecido ao terminar sessão';
                
                Log::warning('⚠️ API retornou erro ao terminar sessão', [
                    'whats_api_id' => $whatsApiId,
                    'error_message' => $errorMessage,
                    'response_data' => $responseData,
                ]);
                
                // Se for um erro de recurso ocupado, sugerimos uma mensagem mais amigável
                if (strpos($errorMessage, 'EBUSY') !== false || strpos($errorMessage, 'resource busy') !== false) {
                    return [
                        'success' => false,
                        'message' => 'Não foi possível terminar a sessão. O WhatsApp ainda está em uso ou os arquivos da sessão estão bloqueados. Tente novamente em alguns segundos.',
                        'data' => $responseData,
                    ];
                }
                
                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'data' => $responseData,
                ];
            }

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => "Falha ao terminar sessão: {$response->status()}",
                ];
            }

            $data = $responseData;

            Log::info('🔴 Sessão terminada com sucesso', [
                'response_data' => $data,
            ]);

            return [
                'success' => true,
                'status' => $response->status(),
                'message' => 'Sessão terminada com sucesso',
                'data' => $data,
            ];

        } catch (\Exception $e) {
            Log::error('🔥 ERRO ao terminar sessão', [
                'whats_api_id' => $whatsApiId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Erro inesperado: ' . $e->getMessage(),
            ];
        }
    }

    /* -------------------------------------------
        GET CONNECTION STATUS
    --------------------------------------------*/
    public function getConnectionStatus(int $whatsApiId): array
    {
        Log::info('🟨 *Verificando status da conexão*', [
            'whats_api_id' => $whatsApiId,
        ]);

        $whatsApi = WhatsApi::find($whatsApiId);

        if (!$whatsApi) {
            return [
                'success' => false,
                'message' => 'Configuração da API não encontrada.',
            ];
        }

        $sessionId = $whatsApi->instance_name;
        
        // Usando o endpoint correto conforme a documentação oficial
        // GET /session/status/{sessionId}
        $url = $this->getBaseUrl($whatsApi) . "/session/status/{$sessionId}";
        $headers = $this->getHeaders($whatsApi);

        Log::info('🔵 Enviando requisição para verificar status → API', [
            'url' => $url,
            'headers' => array_keys($headers),
        ]);

        try {
            $response = Http::withHeaders($headers)->get($url);

            Log::info('🟩 Resposta recebida (Connection Status)', [
                'status' => $response->status(),
                'body_preview' => substr($response->body(), 0, 200),
            ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => "Falha ao obter status: {$response->status()}",
                ];
            }

            $data = $response->json() ?? [];

            Log::info('🟡 Status obtido com sucesso', [
                'response_data' => $data,
            ]);

            // Extraindo o status correto da resposta
            $status = 'unknown';
            if (isset($data['state'])) {
                $status = strtolower($data['state']); // CONNECTED -> connected
            } elseif (isset($data['status'])) {
                $status = strtolower($data['status']);
            }

            return [
                'success' => true,
                'status' => $response->status(),
                'message' => 'Status obtido com sucesso',
                'data' => [
                    'status' => $status,
                    'raw' => $data
                ],
            ];

        } catch (\Exception $e) {
            Log::error('🔥 ERRO ao obter status', [
                'whats_api_id' => $whatsApiId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Erro inesperado: ' . $e->getMessage(),
            ];
        }
    }

    /* -------------------------------------------
        GET GROUPS LIST
    --------------------------------------------*/
    public function getGroupsList(int $whatsApiId): array
    {
        Log::info('🟩 *Buscando lista de grupos*', [
            'whats_api_id' => $whatsApiId,
        ]);

        $whatsApi = WhatsApi::find($whatsApiId);

        if (!$whatsApi) {
            return [
                'success' => false,
                'message' => 'Configuração da API não encontrada.',
            ];
        }

        $sessionId = $whatsApi->instance_name;
        
        // Endpoint para buscar lista de grupos
        // GET /client/getlistGrup/{sessionId}
        $url = $this->getBaseUrl($whatsApi) . "/client/getlistGrup/{$sessionId}";
        $headers = $this->getHeaders($whatsApi);

        Log::info('🔵 Enviando requisição para buscar grupos → API', [
            'url' => $url,
            'headers' => array_keys($headers),
        ]);

        try {
            $response = Http::withHeaders($headers)->get($url);

            Log::info('🟩 Resposta recebida (Groups List)', [
                'status' => $response->status(),
                'body_preview' => substr($response->body(), 0, 500),
            ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => "Falha ao obter lista de grupos: {$response->status()}",
                ];
            }

            $data = $response->json() ?? [];

            Log::info('🟢 Lista de grupos obtida com sucesso', [
                'groups_count' => isset($data['groups']) ? count($data['groups']) : 0,
                'response_data' => $data,
            ]);

            return [
                'success' => true,
                'status' => $response->status(),
                'message' => 'Lista de grupos obtida com sucesso',
                'data' => $data,
            ];

        } catch (\Exception $e) {
            Log::error('🔥 ERRO ao obter lista de grupos', [
                'whats_api_id' => $whatsApiId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Erro inesperado: ' . $e->getMessage(),
            ];
        }
    }

    /* -------------------------------------------
        SEND MESSAGE TO GROUP
    --------------------------------------------*/
    public function sendGroupMessage(int $whatsApiId, string $groupId, string $message): array
    {
        Log::info('🟦 *Enviando mensagem para grupo*', [
            'whats_api_id' => $whatsApiId,
            'group_id' => $groupId,
        ]);

        $whatsApi = WhatsApi::find($whatsApiId);

        if (!$whatsApi) {
            return [
                'success' => false,
                'message' => 'Configuração da API não encontrada.',
            ];
        }

        $sessionId = $whatsApi->instance_name;
        
        // Endpoint para enviar mensagem para grupo
        // POST /client/sendMessage/{sessionId}
        $url = $this->getBaseUrl($whatsApi) . "/client/sendMessage/{$sessionId}";
        $headers = $this->getHeaders($whatsApi);

        // Preparar os dados da requisição
        $requestData = [
            'chatId' => $groupId . '@g.us', // Grupo IDs terminam com @g.us
            'contentType' => 'string', // Tipo de conteúdo
            'content' => $message
        ];

        Log::info('🔵 Enviando requisição para enviar mensagem → API', [
            'url' => $url,
            'headers' => array_keys($headers),
            'request_data' => $requestData,
        ]);

        try {
            $response = Http::withHeaders($headers)->post($url, $requestData);

            Log::info('🟩 Resposta recebida (Send Group Message)', [
                'status' => $response->status(),
                'body_preview' => substr($response->body(), 0, 500),
            ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => "Falha ao enviar mensagem para o grupo: {$response->status()}",
                ];
            }

            $data = $response->json() ?? [];

            Log::info('🟢 Mensagem enviada para grupo com sucesso', [
                'response_data' => $data,
            ]);

            return [
                'success' => true,
                'status' => $response->status(),
                'message' => 'Mensagem enviada para o grupo com sucesso',
                'data' => $data,
            ];

        } catch (\Exception $e) {
            Log::error('🔥 ERRO ao enviar mensagem para grupo', [
                'whats_api_id' => $whatsApiId,
                'group_id' => $groupId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Erro inesperado: ' . $e->getMessage(),
            ];
        }
    }

    /* -------------------------------------------
        GET QR CODE
    --------------------------------------------*/
    public function getQRCode(int $whatsApiId): array
    {
        Log::info('🟦 *Iniciando busca de QR Code*', [
            'whats_api_id' => $whatsApiId,
        ]);

        $whatsApi = WhatsApi::find($whatsApiId);

        if (!$whatsApi) {
            return [
                'success' => false,
                'message' => 'Configuração da API não encontrada.',
            ];
        }

        $sessionId = $whatsApi->instance_name;
        $url = $this->getBaseUrl($whatsApi) . "/session/qr/{$sessionId}";
        $headers = $this->getHeaders($whatsApi);

        Log::info('🔵 Enviando requisição QR → API', [
            'url' => $url,
            'headers' => array_keys($headers),
        ]);

        try {
            $response = Http::withHeaders($headers)->get($url);

            Log::info('🟩 Resposta recebida (QR Code)', [
                'status' => $response->status(),
                'body_preview' => substr($response->body(), 0, 200),
            ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => "Falha ao obter QR Code: {$response->status()}",
                ];
            }

            $data = $response->json() ?? [];

            /* ---------------------------------------------------------
                EXTRAÇÃO DO QR CODE (API PODE MUDAR A ESTRUTURA)
            ----------------------------------------------------------*/

            $qr = $data['qr'] ??
                  $data['qrCode'] ??
                  $data['base64'] ??
                  $data['data'] ??
                  (is_string($data) ? $data : null);

            if (!$qr) {
                Log::warning('❗ API retornou resposta SEM QR code detectado.', [
                    'response_data' => $data,
                ]);

                return [
                    'success' => false,
                    'message' => 'A API não retornou dados de QR Code.',
                ];
            }

            Log::info('🟢 QR Code extraído com sucesso', [
                'length' => strlen($qr),
            ]);

            return [
                'success' => true,
                'status' => $response->status(),
                'message' => 'QR Code obtido com sucesso',
                'data' => [
                    'qrCode' => $qr,
                    'raw' => $data,
                ],
            ];

        } catch (\Exception $e) {
            Log::error('🔥 ERRO ao obter QR Code', [
                'whats_api_id' => $whatsApiId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Erro inesperado: ' . $e->getMessage(),
            ];
        }
    }
}