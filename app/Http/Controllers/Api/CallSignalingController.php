<?php

namespace App\Http\Controllers\Api;

use App\Events\CallSignal;
use App\Http\Controllers\Controller;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CallSignalingController extends Controller
{
    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Enviar sinal WebRTC (offer, answer, ice-candidate, etc)
     */
    public function sendSignal(Request $request, string $roomCode): JsonResponse
    {
        try {
            // Validar autenticação
            if (!$this->chatService->isAuthenticated($roomCode)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não autenticado',
                ], 401);
            }

            // Validar dados
            $validator = Validator::make($request->all(), [
                'signal_type' => 'required|string|in:offer,answer,ice-candidate,call-start,call-end,call-reject',
                'signal_data' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $userRole = session('chat_user_role', 'client');
            $signalType = $request->input('signal_type');
            $signalData = $request->input('signal_data');

            // Disparar evento de sinal
            broadcast(new CallSignal($roomCode, $userRole, $signalType, $signalData));

            Log::info('Sinal WebRTC enviado', [
                'room_code' => $roomCode,
                'sender_type' => $userRole,
                'signal_type' => $signalType,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sinal enviado',
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao enviar sinal WebRTC', [
                'room_code' => $roomCode,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar sinal',
            ], 500);
        }
    }
}
