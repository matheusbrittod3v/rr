<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Orcamento;
use App\Models\WhatsApi;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

class NotifyGroupOrderProcessedJob implements ShouldQueue
{
    use Queueable;

    protected $orcamentoId;
    protected $uniqueId;
    protected $providerName;
    protected $acceptedValue;

    /**
     * Create a new job instance.
     *
     * @param int $orcamentoId
     * @param string $uniqueId
     * @param string $providerName
     * @param float|null $acceptedValue
     */
    public function __construct(int $orcamentoId, string $uniqueId, string $providerName, ?float $acceptedValue = null)
    {
        $this->orcamentoId = $orcamentoId;
        $this->uniqueId = $uniqueId;
        $this->providerName = $providerName;
        $this->acceptedValue = $acceptedValue;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            Log::info('📨 Iniciando notificação de ordem processada para grupos do WhatsApp', [
                'orcamento_id' => $this->orcamentoId,
                'unique_id' => $this->uniqueId,
                'provider_name' => $this->providerName,
                'accepted_value' => $this->acceptedValue,
                'job_id' => $this->job->getJobId() ?? 'unknown',
                'attempt' => $this->attempts(),
            ]);

            // Get the budget with service information
            $orcamento = Orcamento::with('service.marca')->find($this->orcamentoId);
            
            if (!$orcamento) {
                Log::warning('⚠️ Orçamento não encontrado para notificação de processamento', [
                    'orcamento_id' => $this->orcamentoId,
                    'job_id' => $this->job->getJobId() ?? 'unknown',
                ]);
                return;
            }

            Log::info('📄 Orçamento encontrado para notificação de processamento', [
                'orcamento_id' => $orcamento->id,
                'email' => $orcamento->email,
                'numero' => $orcamento->numero,
                'valor' => $orcamento->valor,
                'job_id' => $this->job->getJobId() ?? 'unknown',
            ]);

            // Get the WhatsApp API configuration
            $whatsApi = WhatsApi::first();
            
            if (!$whatsApi) {
                Log::warning('⚠️ Configuração do WhatsApp não encontrada para notificação de processamento', [
                    'job_id' => $this->job->getJobId() ?? 'unknown',
                ]);
                return;
            }

            Log::info('📱 Configuração do WhatsApp encontrada para notificação de processamento', [
                'whats_api_id' => $whatsApi->id,
                'instance_name' => $whatsApi->instance_name,
                'user_id' => $whatsApi->user_id,
                'host' => $whatsApi->host,
                'job_id' => $this->job->getJobId() ?? 'unknown',
            ]);

            // Get selected groups from the WhatsApp API configuration
            $selectedGroups = $whatsApi->selected_groups ?? [];
            
            if (empty($selectedGroups)) {
                Log::info('📭 Nenhum grupo selecionado para envio de notificação de processamento', [
                    'job_id' => $this->job->getJobId() ?? 'unknown',
                ]);
                return;
            }

            Log::info('👥 Grupos selecionados para notificação de processamento', [
                'groups_count' => count($selectedGroups),
                'groups' => array_map(function($group) {
                    return [
                        'user' => $group['user'] ?? 'unknown',
                        'name' => $group['name'] ?? 'Sem nome',
                    ];
                }, $selectedGroups),
                'job_id' => $this->job->getJobId() ?? 'unknown',
            ]);

            // Create the message content
            $message = $this->createNotificationMessage($orcamento);
            
            Log::info('✉️ Mensagem de notificação de processamento criada', [
                'message_length' => strlen($message),
                'message_preview' => substr($message, 0, 100) . '...',
                'job_id' => $this->job->getJobId() ?? 'unknown',
            ]);

            // Initialize WhatsApp service
            $whatsAppService = new WhatsAppService();
            
            Log::info('🔧 Serviço WhatsApp inicializado para notificação de processamento', [
                'job_id' => $this->job->getJobId() ?? 'unknown',
            ]);

            // Send message to each selected group
            $sentCount = 0;
            $failedCount = 0;
            
            foreach ($selectedGroups as $index => $group) {
                if (isset($group['user'])) {
                    Log::info('📤 Enviando mensagem de processamento para grupo', [
                        'group_index' => $index,
                        'group_id' => $group['user'],
                        'group_name' => $group['name'] ?? 'Sem nome',
                        'job_id' => $this->job->getJobId() ?? 'unknown',
                    ]);
                    
                    $result = $whatsAppService->sendGroupMessage(
                        $whatsApi->id,
                        $group['user'],
                        $message
                    );

                    if ($result['success']) {
                        Log::info('✅ Mensagem de processamento enviada com sucesso para o grupo', [
                            'group_id' => $group['user'],
                            'group_name' => $group['name'] ?? 'Sem nome',
                            'result_data' => $result['data'] ?? null,
                            'job_id' => $this->job->getJobId() ?? 'unknown',
                        ]);
                        $sentCount++;
                    } else {
                        Log::error('❌ Falha ao enviar mensagem de processamento para o grupo', [
                            'group_id' => $group['user'],
                            'group_name' => $group['name'] ?? 'Sem nome',
                            'error' => $result['message'],
                            'result_data' => $result['data'] ?? null,
                            'job_id' => $this->job->getJobId() ?? 'unknown',
                        ]);
                        $failedCount++;
                    }
                } else {
                    Log::warning('⚠️ Grupo inválido encontrado na lista para notificação de processamento', [
                        'group_index' => $index,
                        'group_data' => $group,
                        'job_id' => $this->job->getJobId() ?? 'unknown',
                    ]);
                    $failedCount++;
                }
            }

            Log::info('🏁 Finalizado envio de notificação de processamento para grupos do WhatsApp', [
                'orcamento_id' => $this->orcamentoId,
                'unique_id' => $this->uniqueId,
                'provider_name' => $this->providerName,
                'accepted_value' => $this->acceptedValue,
                'groups_count' => count($selectedGroups),
                'sent_count' => $sentCount,
                'failed_count' => $failedCount,
                'job_id' => $this->job->getJobId() ?? 'unknown',
            ]);

        } catch (\Exception $e) {
            Log::error('🔥 Erro ao enviar notificação de processamento para grupos do WhatsApp', [
                'orcamento_id' => $this->orcamentoId,
                'unique_id' => $this->uniqueId,
                'provider_name' => $this->providerName,
                'accepted_value' => $this->acceptedValue,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'job_id' => $this->job->getJobId() ?? 'unknown',
            ]);
        }
    }

    /**
     * Create the notification message content
     *
     * @param Orcamento $orcamento
     * @return string
     */
    private function createNotificationMessage(Orcamento $orcamento): string
    {
        $message = "✅ *ORDEM PROCESSADA COM SUCESSO*\n\n";
        
        // Add the unique ID
        if ($this->uniqueId) {
            $message .= "🆔 *ID Único:* {$this->uniqueId}\n\n";
        }
        
        $message .= "Olá, equipe!\n\n";
        $message .= "A ordem de serviço foi processada e enviada para o cliente.\n";
        $message .= "Aguardando resposta do cliente sobre aceitação.\n\n";
        
        // Add service information if available
        if ($orcamento->service) {
            $serviceName = $orcamento->service->nome_servico ?? 'Serviço desconhecido';
            $brandName = $orcamento->service->marca->nome ?? 'Marca desconhecida';
            $message .= "🔧 *Serviço Solicitado:*\n";
            $message .= "• Marca: {$brandName}\n";
            $message .= "• Serviço: {$serviceName}\n\n";
        }
        
        // Add provider information
        if ($this->providerName) {
            $message .= "👷 *Prestador Responsável:*\n";
            $message .= "• Nome: {$this->providerName}\n\n";
        }
        
        // Add accepted value if available
        if ($this->acceptedValue !== null) {
            $message .= "💰 *Valor Aceito pelo Prestador:*\n";
            $message .= "• R$ " . number_format($this->acceptedValue, 2, ',', '.') . "\n\n";
        }
        
        // Add budget information
        $message .= "📄 *Detalhes do Orçamento:*\n";
         
        if (!empty($orcamento->informacoes_adicionais)) {
            // Handle both array and JSON string formats
            $infoAdicionais = $orcamento->informacoes_adicionais;
            if (is_string($infoAdicionais)) {
                $infoAdicionais = json_decode($infoAdicionais, true);
            }
            
            // Ensure $infoAdicionais is an array
            if (!is_array($infoAdicionais)) {
                $infoAdicionais = [];
            }
            
            // Add service and brand name to additional information if service exists
            if ($orcamento->service) {
                // Add service name
                $serviceName = $orcamento->service->nome_servico ?? 'Serviço desconhecido';
                $serviceExists = false;
                foreach ($infoAdicionais as $info) {
                    if (is_array($info) && isset($info['name']) && $info['name'] === 'Serviço') {
                        $serviceExists = true;
                        break;
                    }
                }
                
                if (!$serviceExists) {
                    $infoAdicionais[] = [
                        'name' => 'Serviço',
                        'value' => $serviceName
                    ];
                }
                
                // Add brand name if brand exists
                if ($orcamento->service->marca) {
                    $brandName = $orcamento->service->marca->nome ?? 'Marca desconhecida';
                    $brandExists = false;
                    foreach ($infoAdicionais as $info) {
                        if (is_array($info) && isset($info['name']) && $info['name'] === 'Marca') {
                            $brandExists = true;
                            break;
                        }
                    }
                    
                    if (!$brandExists) {
                        $infoAdicionais[] = [
                            'name' => 'Marca',
                            'value' => $brandName
                        ];
                    }
                }
            }
            
            if (!empty($infoAdicionais)) {
                $message .= "• Informações adicionais:\n";
                foreach ($infoAdicionais as $info) {
                    if (is_array($info) && isset($info['name']) && isset($info['value'])) {
                        $message .= "  • {$info['name']}: {$info['value']}\n";
                    } elseif (is_string($info)) {
                        $message .= "  • {$info}\n";
                    }
                }
            }
        }
        
        $message .= "\n🔄 *Status Atual:*\n";
        $message .= "Aguardando resposta do cliente...\n\n";
        $message .= "Obrigado pela sua colaboração!";

        return $message;
    }
}