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
// Autorização simples baseada apenas na sessão (sem verificar autenticação Laravel/Filament)
// IMPORTANTE: Laravel adiciona automaticamente o prefixo "private-", então não inclua no nome
Broadcast::channel('chat-room.{roomCode}', function ($user, $roomCode) {
    $request = request();
    
    // Verificar se tem sessão
    if (!$request->hasSession()) {
        \Log::warning('Broadcast: Sem sessão', [
            'roomCode' => $roomCode,
            'hasSession' => false,
            'sessionId' => null,
        ]);
        return false;
    }
    
    $session = $request->session();
    
    // Garantir que a sessão está iniciada
    if (!$session->isStarted()) {
        $session->start();
    }
    
    // Verificar se a sessão tem os dados corretos
    $authenticatedRoom = $session->get('authenticated_chat_room');
    $userRole = $session->get('chat_user_role');
    
    // Log detalhado para debug
    \Log::info('Broadcast: Verificando autorização', [
        'roomCode' => $roomCode,
        'sessionId' => $session->getId(),
        'authenticatedRoom' => $authenticatedRoom,
        'userRole' => $userRole,
        'match' => $authenticatedRoom === $roomCode,
        'hasRole' => !empty($userRole),
        'allSessionKeys' => array_keys($session->all()),
    ]);
    
    // Se a sala autenticada na sessão corresponde à sala do canal, autorizar
    if ($authenticatedRoom === $roomCode && $userRole) {
        \Log::info('Broadcast: Autorizado', [
            'roomCode' => $roomCode,
            'userRole' => $userRole,
            'sessionId' => $session->getId(),
        ]);
        return [
            'id' => $userRole,
            'role' => $userRole,
        ];
    }
    
    \Log::warning('Broadcast: Negado', [
        'roomCode' => $roomCode,
        'sessionId' => $session->getId(),
        'authenticatedRoom' => $authenticatedRoom,
        'userRole' => $userRole,
        'allSessionKeys' => array_keys($session->all()),
    ]);
    
    return false;
});

