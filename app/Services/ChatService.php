<?php

namespace App\Services;

use App\Models\ChatRoom;
use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ChatService
{
    /**
     * Criar sala de chat para um pagamento
     * 
     * @param Payment $payment
     * @return ChatRoom
     */
    public function createChatRoomForPayment(Payment $payment): ChatRoom
    {
        // Gerar código único da sala
        $roomCode = Str::random(20);
        
        // Garantir que o código seja único
        while (ChatRoom::where('room_code', $roomCode)->exists()) {
            $roomCode = Str::random(20);
        }
        
        // Gerar senhas separadas para cliente e prestador
        $clientPassword = Str::random(8);
        
        // Garantir que as senhas sejam diferentes
        do {
            $providerPassword = Str::random(8);
        } while ($providerPassword === $clientPassword);
        
        // Criar a sala de chat
        $chatRoom = ChatRoom::create([
            'payment_id' => $payment->id,
            'room_code' => $roomCode,
            'client_password' => $clientPassword,
            'provider_password' => $providerPassword,
        ]);
        
        Log::info('Chat room criada com sucesso', [
            'payment_id' => $payment->id,
            'chat_room_id' => $chatRoom->id,
            'room_code' => $roomCode,
        ]);
        
        return $chatRoom;
    }

    /**
     * Validar senha e retornar o tipo de usuário
     * 
     * @param ChatRoom $chatRoom
     * @param string $password
     * @return string|null 'client', 'provider' ou null se inválida
     */
    public function validatePassword(ChatRoom $chatRoom, string $password): ?string
    {
        if ($password === $chatRoom->client_password) {
            return 'client';
        }
        
        if ($password === $chatRoom->provider_password) {
            return 'provider';
        }
        
        return null;
    }

    /**
     * Verificar se o usuário está autenticado na sala
     * 
     * @param string $roomCode
     * @return bool
     */
    public function isAuthenticated(string $roomCode): bool
    {
        return session('authenticated_chat_room') === $roomCode;
    }

    /**
     * Set chat room session for authentication
     * Usado tanto no login direto quanto no fluxo de pagamento
     *
     * @param ChatRoom $chatRoom
     * @param string $userRole 'client' ou 'provider'
     * @param bool $fromPayment Se true, adiciona flag de autenticação via pagamento
     * @return void
     */
    public function setChatRoomSession(ChatRoom $chatRoom, string $userRole, bool $fromPayment = false): void
    {
        try {
            // Payload de sessão padrão
            $sessionData = [
                'authenticated_chat_room' => $chatRoom->room_code,
                'chat_user_role' => $userRole,
            ];
            
            // Se for do fluxo de pagamento, adiciona flag específica
            if ($fromPayment) {
                $sessionData['authenticated_from_payment'] = true;
            }
            
            // Salvar na sessão
            session($sessionData);
            
            // Save the session immediately to ensure it's available
            session()->save();
            
            // Log for debugging
            Log::info('Chat room session set', [
                'room_code' => $chatRoom->room_code,
                'user_role' => $userRole,
                'from_payment' => $fromPayment,
                'authenticated_chat_room' => session('authenticated_chat_room'),
                'chat_user_role' => session('chat_user_role'),
            ]);
        } catch (\Exception $e) {
            Log::error('Error setting chat room session', [
                'chat_room_id' => $chatRoom->id,
                'user_role' => $userRole,
                'from_payment' => $fromPayment,
                'error' => $e->getMessage()
            ]);
        }
    }
}

