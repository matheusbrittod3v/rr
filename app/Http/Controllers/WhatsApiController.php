<?php

namespace App\Http\Controllers;

use App\Models\WhatsApi;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsApiController extends Controller
{
    /**
     * Gera e retorna o QR code para a única instância configurada
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQrCode()
    {
        try {
            Log::info('📱 INICIANDO WhatsApiController->getQrCode (instância única)', [
                'user_id' => auth()->id(),
                'timestamp' => now()->toISOString()
            ]);

            // Busca o único registro de API configurado para o usuário
            $whatsApi = WhatsApi::first();
            
            if (!$whatsApi) {
                Log::warning('⚠️ Nenhuma API configurada', [
                    'user_id' => auth()->id(),
                    'timestamp' => now()->toISOString()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma API do WhatsApp configurada'
                ], 404);
            }

            Log::info('🔍 Usando instância encontrada', [
                'whats_api_id' => $whatsApi->id,
                'instance_name' => $whatsApi->instance_name,
                'user_id' => auth()->id(),
                'timestamp' => now()->toISOString()
            ]);

            // Atualiza o status para "qr_code" antes de gerar o QR code
            $whatsApi->update(['connection_status' => 'qr_code']);

            $whatsAppService = new WhatsAppService();
            $result = $whatsAppService->getQRCode($whatsApi->id);

            Log::info('📥 Resultado recebido do WhatsAppService', [
                'whats_api_id' => $whatsApi->id,
                'success' => $result['success'] ?? null,
                'has_data' => isset($result['data']),
                'timestamp' => now()->toISOString()
            ]);

            if (!($result['success'] ?? false)) {
                Log::error('💥 Falha ao obter QR Code', [
                    'whats_api_id' => $whatsApi->id,
                    'error_message' => $result['message'] ?? 'Erro desconhecido',
                    'timestamp' => now()->toISOString()
                ]);

                // Volta o status para "disconnected" em caso de erro
                $whatsApi->update(['connection_status' => 'disconnected']);

                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Falha ao obter QR Code'
                ], 400);
            }

            $qrCodeData = $result['data']['qrCode'] ?? null;

            if (!$qrCodeData) {
                Log::warning('⚠️ QR Code não disponível na resposta', [
                    'whats_api_id' => $whatsApi->id,
                    'timestamp' => now()->toISOString()
                ]);

                // Volta o status para "disconnected" se não houver QR code
                $whatsApi->update(['connection_status' => 'disconnected']);

                return response()->json([
                    'success' => false,
                    'message' => 'QR Code não disponível'
                ], 404);
            }

            $cleanQrCode = trim($qrCodeData);

            Log::info('✅ QR Code obtido com sucesso', [
                'whats_api_id' => $whatsApi->id,
                'data_length' => strlen($cleanQrCode),
                'timestamp' => now()->toISOString()
            ]);

            return response()->json([
                'success' => true,
                'qrCode' => $cleanQrCode,
                'message' => 'QR Code gerado com sucesso'
            ]);

        } catch (\Exception $e) {
            Log::error('🔥 EXCEPTION no WhatsApiController->getQrCode', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'timestamp' => now()->toISOString()
            ]);

            // Tenta voltar o status para "disconnected" em caso de exceção
            try {
                $whatsApi = WhatsApi::first();
                if ($whatsApi) {
                    $whatsApi->update(['connection_status' => 'disconnected']);
                }
            } catch (\Exception $updateException) {
                Log::error('❌ Falha ao atualizar status após exceção', [
                    'error_message' => $updateException->getMessage()
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verifica o status da conexão
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConnectionStatus()
    {
        try {
            Log::info('🔍 Verificando status da conexão', [
                'user_id' => auth()->id(),
                'timestamp' => now()->toISOString()
            ]);

            // Busca o único registro de API configurado para o usuário
            $whatsApi = WhatsApi::first();
            
            if (!$whatsApi) {
                Log::warning('⚠️ Nenhuma API configurada para verificar status', [
                    'user_id' => auth()->id(),
                    'timestamp' => now()->toISOString()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma API do WhatsApp configurada'
                ], 404);
            }

            // Chama o serviço para verificar o status
            $whatsAppService = new WhatsAppService();
            $result = $whatsAppService->getConnectionStatus($whatsApi->id);

            Log::info('📥 Status da conexão recebido', [
                'whats_api_id' => $whatsApi->id,
                'success' => $result['success'] ?? null,
                'status' => $result['data']['status'] ?? null,
                'timestamp' => now()->toISOString()
            ]);

            if (!($result['success'] ?? false)) {
                Log::error('💥 Falha ao obter status da conexão', [
                    'whats_api_id' => $whatsApi->id,
                    'error_message' => $result['message'] ?? 'Erro desconhecido',
                    'timestamp' => now()->toISOString()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Falha ao obter status da conexão'
                ], 400);
            }

            $status = $result['data']['status'] ?? 'unknown';
            
            // Atualiza o status no banco de dados
            $whatsApi->update(['connection_status' => $status]);

            return response()->json([
                'success' => true,
                'status' => $status,
                'message' => 'Status obtido com sucesso'
            ]);

        } catch (\Exception $e) {
            Log::error('🔥 EXCEPTION no WhatsApiController->getConnectionStatus', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'timestamp' => now()->toISOString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Busca a lista de grupos disponíveis
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGroups()
    {
        try {
            Log::info('👥 Buscando lista de grupos', [
                'user_id' => auth()->id(),
                'timestamp' => now()->toISOString()
            ]);

            // Busca o único registro de API configurado para o usuário
            $whatsApi = WhatsApi::first();
            
            if (!$whatsApi) {
                Log::warning('⚠️ Nenhuma API configurada para buscar grupos', [
                    'user_id' => auth()->id(),
                    'timestamp' => now()->toISOString()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma API do WhatsApp configurada'
                ], 404);
            }

            // Verifica se está conectado antes de buscar grupos
            if (!$whatsApi->isConnected()) {
                Log::warning('⚠️ Não é possível buscar grupos - WhatsApp não conectado', [
                    'user_id' => auth()->id(),
                    'connection_status' => $whatsApi->connection_status,
                    'timestamp' => now()->toISOString()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'WhatsApp não está conectado. Conecte-se primeiro.'
                ], 400);
            }

            // Chama o serviço para buscar a lista de grupos
            $whatsAppService = new WhatsAppService();
            $result = $whatsAppService->getGroupsList($whatsApi->id);

            Log::info('📥 Lista de grupos recebida', [
                'whats_api_id' => $whatsApi->id,
                'success' => $result['success'] ?? null,
                'groups_count' => isset($result['data']['groups']) ? count($result['data']['groups']) : 0,
                'timestamp' => now()->toISOString()
            ]);

            if (!($result['success'] ?? false)) {
                Log::error('💥 Falha ao obter lista de grupos', [
                    'whats_api_id' => $whatsApi->id,
                    'error_message' => $result['message'] ?? 'Erro desconhecido',
                    'timestamp' => now()->toISOString()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Falha ao obter lista de grupos'
                ], 400);
            }

            $groups = $result['data']['groups'] ?? [];
            
            // Retorna os grupos e os grupos selecionados atualmente
            return response()->json([
                'success' => true,
                'groups' => $groups,
                'selectedGroups' => $whatsApi->getSelectedGroups(),
                'message' => 'Lista de grupos obtida com sucesso'
            ]);

        } catch (\Exception $e) {
            Log::error('🔥 EXCEPTION no WhatsApiController->getGroups', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'timestamp' => now()->toISOString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Salva os grupos selecionados
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveSelectedGroups(Request $request)
    {
        try {
            Log::info('💾 Salvando grupos selecionados', [
                'user_id' => auth()->id(),
                'groups_data' => $request->input('groups'),
                'timestamp' => now()->toISOString()
            ]);

            // Validação dos dados
            $validated = $request->validate([
                'groups' => 'required|array',
                'groups.*.user' => 'required|string',
                'groups.*.name' => 'required|string',
            ]);

            // Busca o único registro de API configurado para o usuário
            $whatsApi = WhatsApi::first();
            
            if (!$whatsApi) {
                Log::warning('⚠️ Nenhuma API configurada para salvar grupos', [
                    'user_id' => auth()->id(),
                    'timestamp' => now()->toISOString()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma API do WhatsApp configurada'
                ], 404);
            }

            // Salva os grupos selecionados
            $whatsApi->setSelectedGroups($validated['groups']);

            Log::info('✅ Grupos selecionados salvos com sucesso', [
                'whats_api_id' => $whatsApi->id,
                'groups_count' => count($validated['groups']),
                'timestamp' => now()->toISOString()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Grupos selecionados salvos com sucesso'
            ]);

        } catch (\Exception $e) {
            Log::error('🔥 EXCEPTION no WhatsApiController->saveSelectedGroups', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'timestamp' => now()->toISOString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor: ' . $e->getMessage()
            ], 500);
        }
    }
}