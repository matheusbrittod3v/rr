<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Canal privado para chat rooms
Broadcast::channel('private-chat-room.{roomCode}', function ($user, $roomCode) {
    try {
        // Obter a requisição atual para acessar a sessão
        $request = request();
        
        // Verificar se a requisição tem sessão
        if (!$request->hasSession()) {
            \Log::error('Canal privado: Requisição sem sessão', [
                'roomCode' => $roomCode,
                'ip' => $request->ip(),
                'userAgent' => $request->userAgent(),
            ]);
            return false;
        }
        
        // Verificar se o usuário está autenticado na sala via sessão
        // Como não temos autenticação Laravel padrão, vamos permitir baseado na sessão
        $session = $request->session();
        $authenticatedRoom = $session->get('authenticated_chat_room');
        $userRole = $session->get('chat_user_role', 'client');
        
        // Log detalhado para debug
        \Log::info('Autorizando canal privado', [
            'roomCode' => $roomCode,
            'authenticatedRoom' => $authenticatedRoom,
            'userRole' => $userRole,
            'match' => $authenticatedRoom === $roomCode,
            'hasSession' => $request->hasSession(),
            'sessionId' => $session->getId(),
            'sessionData' => [
                'authenticated_chat_room' => $authenticatedRoom,
                'chat_user_role' => $userRole,
            ],
            'allSessionKeys' => array_keys($session->all()),
        ]);
        
        if ($authenticatedRoom === $roomCode) {
            \Log::info('✅ Canal privado autorizado com sucesso', [
                'roomCode' => $roomCode,
                'userRole' => $userRole,
            ]);
            
            return [
                'id' => $userRole,
                'role' => $userRole,
            ];
        }
        
        \Log::warning('❌ Canal privado negado - sala não corresponde', [
            'roomCode' => $roomCode,
            'authenticatedRoom' => $authenticatedRoom,
            'userRole' => $userRole,
            'sessionId' => $session->getId(),
        ]);
        
        return false;
        
    } catch (\Exception $e) {
        \Log::error('❌ Erro ao autorizar canal privado', [
            'roomCode' => $roomCode,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
        
        return false;
    }
});

