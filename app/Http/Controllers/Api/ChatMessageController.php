<?php

namespace App\Http\Controllers\Api;

use App\Events\ChatMessageSent;
use App\Events\ChatUserTyping;
use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ChatMessageController extends Controller
{
    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Enviar mensagem de chat
     * 
     * @param Request $request
     * @param string $roomCode
     * @return JsonResponse
     */
    public function sendMessage(Request $request, string $roomCode): JsonResponse
    {
        try {
            // Validar que o usuário está autenticado na sala
            if (!$this->chatService->isAuthenticated($roomCode)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não autenticado nesta sala de chat',
                ], 401);
            }

            // Buscar sala de chat
            $chatRoom = ChatRoom::where('room_code', $roomCode)->first();
            if (!$chatRoom) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sala de chat não encontrada',
                ], 404);
            }

            // Validar dados
            $validator = Validator::make($request->all(), [
                'message' => 'nullable|string|max:1000',
                'file' => 'nullable|file|max:10240', // 10MB max
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Pelo menos mensagem ou arquivo deve estar presente
            if (!$request->has('message') && !$request->hasFile('file')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mensagem ou arquivo é obrigatório',
                ], 422);
            }

            $userRole = session('chat_user_role', 'client');
            $filePath = null;
            $fileType = null;

            // Processar upload de arquivo se presente
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                
                // Validar tipo de arquivo
                $allowedMimes = [
                    'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ];

                if (!in_array($file->getMimeType(), $allowedMimes)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tipo de arquivo não permitido',
                    ], 422);
                }

                // Salvar arquivo
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('chat_files/' . $roomCode, $fileName, 'public');
                
                // Determinar tipo de arquivo
                if (str_starts_with($file->getMimeType(), 'image/')) {
                    $fileType = 'image';
                } else {
                    $fileType = 'document';
                }
            }

            // Criar mensagem
            $message = ChatMessage::create([
                'chat_room_id' => $chatRoom->id,
                'sender_id' => null, // Não temos usuário autenticado via Auth
                'sender_type' => $userRole,
                'message' => $request->input('message'),
                'file_path' => $filePath,
                'file_type' => $fileType,
            ]);

            // Disparar evento de broadcasting
            broadcast(new ChatMessageSent($message, $roomCode));

            Log::info('Mensagem de chat enviada', [
                'room_code' => $roomCode,
                'sender_type' => $userRole,
                'message_id' => $message->id,
                'has_file' => !is_null($filePath),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mensagem enviada com sucesso',
                'data' => [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_type' => $message->sender_type,
                    'file_path' => $message->file_path,
                    'file_type' => $message->file_type,
                    'created_at' => $message->created_at->toISOString(),
                    'formatted_time' => $message->getFormattedTime(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao enviar mensagem de chat', [
                'room_code' => $roomCode,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar mensagem',
            ], 500);
        }
    }

    /**
     * Listar mensagens de chat (paginadas)
     * 
     * @param Request $request
     * @param string $roomCode
     * @return JsonResponse
     */
    public function getMessages(Request $request, string $roomCode): JsonResponse
    {
        try {
            // Validar que o usuário está autenticado na sala
            if (!$this->chatService->isAuthenticated($roomCode)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não autenticado nesta sala de chat',
                ], 401);
            }

            // Buscar sala de chat
            $chatRoom = ChatRoom::where('room_code', $roomCode)->first();
            if (!$chatRoom) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sala de chat não encontrada',
                ], 404);
            }

            // Buscar mensagens (últimas 50 por padrão)
            $limit = $request->input('limit', 50);
            $limit = min($limit, 100); // Máximo 100 mensagens por vez

            $messages = ChatMessage::where('chat_room_id', $chatRoom->id)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->reverse()
                ->values()
                ->map(function ($message) {
                    return [
                        'id' => $message->id,
                        'message' => $message->message,
                        'sender_type' => $message->sender_type,
                        'sender_id' => $message->sender_id,
                        'file_path' => $message->file_path,
                        'file_type' => $message->file_type,
                        'created_at' => $message->created_at->toISOString(),
                        'formatted_time' => $message->getFormattedTime(),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $messages,
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao buscar mensagens de chat', [
                'room_code' => $roomCode,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar mensagens',
            ], 500);
        }
    }

    /**
     * Enviar evento de "está digitando"
     * 
     * @param Request $request
     * @param string $roomCode
     * @return JsonResponse
     */
    public function sendTypingIndicator(Request $request, string $roomCode): JsonResponse
    {
        try {
            // Validar que o usuário está autenticado na sala
            if (!$this->chatService->isAuthenticated($roomCode)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não autenticado nesta sala de chat',
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'is_typing' => 'required|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $userRole = session('chat_user_role', 'client');
            $isTyping = $request->input('is_typing', true);

            // Disparar evento de "está digitando"
            broadcast(new ChatUserTyping($roomCode, $userRole, $isTyping));

            return response()->json([
                'success' => true,
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao enviar indicador de digitação', [
                'room_code' => $roomCode,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar indicador',
            ], 500);
        }
    }
}
