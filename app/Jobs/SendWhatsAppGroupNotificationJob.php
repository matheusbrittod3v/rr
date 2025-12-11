<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Orcamento;
use App\Models\WhatsApi;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

class SendWhatsAppGroupNotificationJob implements ShouldQueue
{
    use Queueable;

    protected $orcamentoId;
    protected $providerName;
    protected $uniqueId;

    /**
     * Create a new job instance.
     *
     * @param int $orcamentoId
     * @param string $providerName
     * @param string $uniqueId
     */
    public function __construct(int $orcamentoId, string $providerName, string $uniqueId)
    {
        $this->orcamentoId = $orcamentoId;
        $this->providerName = $providerName;
        $this->uniqueId = $uniqueId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            Log::info('📨 Iniciando envio de notificação para grupos do WhatsApp', [
                'orcamento_id' => $this->orcamentoId,
                'provider_name' => $this->providerName,
                'job_id' => $this->job->getJobId() ?? 'unknown',
                'attempt' => $this->attempts(),
            ]);

            // Get the budget with service information
            $orcamento = Orcamento::with('service.marca')->find($this->orcamentoId);
            
            if (!$orcamento) {
                Log::warning('⚠️ Orçamento não encontrado', [
                    'orcamento_id' => $this->orcamentoId,
                    'job_id' => $this->job->getJobId() ?? 'unknown',
                ]);
                return;
            }

            Log::info('📄 Orçamento encontrado', [
                'orcamento_id' => $orcamento->id,
                'email' => $orcamento->email,
                'numero' => $orcamento->numero,
                'valor' => $orcamento->valor,
                'job_id' => $this->job->getJobId() ?? 'unknown',
            ]);

            // Get the WhatsApp API configuration (assuming there's only one per user)
            $whatsApi = WhatsApi::first();
            
            if (!$whatsApi) {
                Log::warning('⚠️ Configuração do WhatsApp não encontrada', [
                    'job_id' => $this->job->getJobId() ?? 'unknown',
                ]);
                return;
            }

            Log::info('📱 Configuração do WhatsApp encontrada', [
                'whats_api_id' => $whatsApi->id,
                'instance_name' => $whatsApi->instance_name,
                'user_id' => $whatsApi->user_id,
                'host' => $whatsApi->host,
                'job_id' => $this->job->getJobId() ?? 'unknown',
            ]);

            // Get selected groups from the WhatsApp API configuration
            $selectedGroups = $whatsApi->selected_groups ?? [];
            
            if (empty($selectedGroups)) {
                Log::info('📭 Nenhum grupo selecionado para envio de notificações', [
                    'job_id' => $this->job->getJobId() ?? 'unknown',
                ]);
                return;
            }

            Log::info('👥 Grupos selecionados para notificação', [
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
            $message = $this->createNotificationMessage($orcamento, $this->providerName);
            
            Log::info('✉️ Mensagem de notificação criada', [
                'message_length' => strlen($message),
                'message_preview' => substr($message, 0, 100) . '...',
                'job_id' => $this->job->getJobId() ?? 'unknown',
            ]);

            // Initialize WhatsApp service
            $whatsAppService = new WhatsAppService();
            
            Log::info('🔧 Serviço WhatsApp inicializado', [
                'job_id' => $this->job->getJobId() ?? 'unknown',
            ]);

            // Send message to each selected group
            $sentCount = 0;
            $failedCount = 0;
            
            foreach ($selectedGroups as $index => $group) {
                if (isset($group['user'])) {
                    Log::info('📤 Enviando mensagem para grupo', [
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
                        Log::info('✅ Mensagem enviada com sucesso para o grupo', [
                            'group_id' => $group['user'],
                            'group_name' => $group['name'] ?? 'Sem nome',
                            'result_data' => $result['data'] ?? null,
                            'job_id' => $this->job->getJobId() ?? 'unknown',
                        ]);
                        $sentCount++;
                    } else {
                        Log::error('❌ Falha ao enviar mensagem para o grupo', [
                            'group_id' => $group['user'],
                            'group_name' => $group['name'] ?? 'Sem nome',
                            'error' => $result['message'],
                            'result_data' => $result['data'] ?? null,
                            'job_id' => $this->job->getJobId() ?? 'unknown',
                        ]);
                        $failedCount++;
                    }
                } else {
                    Log::warning('⚠️ Grupo inválido encontrado na lista', [
                        'group_index' => $index,
                        'group_data' => $group,
                        'job_id' => $this->job->getJobId() ?? 'unknown',
                    ]);
                    $failedCount++;
                }
            }

            Log::info('🏁 Finalizado envio de notificações para grupos do WhatsApp', [
                'orcamento_id' => $this->orcamentoId,
                'provider_name' => $this->providerName,
                'groups_count' => count($selectedGroups),
                'sent_count' => $sentCount,
                'failed_count' => $failedCount,
                'job_id' => $this->job->getJobId() ?? 'unknown',
            ]);

        } catch (\Exception $e) {
            Log::error('🔥 Erro ao enviar notificações para grupos do WhatsApp', [
                'orcamento_id' => $this->orcamentoId,
                'provider_name' => $this->providerName,
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
     * @param string $providerName
     * @return string
     */
    private function createNotificationMessage(Orcamento $orcamento, string $providerName): string
    {
        $message = "📋 *NOVO ORÇAMENTO DISPONÍVEL*\n\n";
         // Add the unique ID if available
        if ($this->uniqueId) {
            $message .= "*ID: {$this->uniqueId}*\n\n";
        }
        $message .= "Olá, {$providerName}!\n\n";
        $message .= "Uma nova ordem de serviço acabou de chegar.\n\n";
        
        // Add the unique ID if available
        if ($this->uniqueId) {
            $message .= "*ID: {$this->uniqueId}*\n\n";
        }
        
        // Add service information if available
        if ($orcamento->service) {
            $serviceName = $orcamento->service->nome_servico ?? 'Serviço desconhecido';
            $brandName = $orcamento->service->marca->nome ?? 'Marca desconhecida';
            $message .= "🔧 *Serviço Solicitado:*\n";
            $message .= "• Marca: {$brandName}\n";
            $message .= "• Serviço: {$serviceName}\n\n";
        }
        
        $message .= "📄 *Detalhes do Orçamento:*\n";
         
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
        
        $message .= "\nPor favor, verifique os detalhes e responda no grupo.\n";
        $message .= "Responda com *REPLY* com sim ou nao, seguido do seu preço.\n\n";
        $message .= "Obrigado!";

        return $message;
    }
}