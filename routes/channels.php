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
        \Log::warning('Broadcast: Sem sessão', ['roomCode' => $roomCode]);
        return false;
    }
    
    $session = $request->session();
    
    // Verificar se a sessão tem os dados corretos
    $authenticatedRoom = $session->get('authenticated_chat_room');
    $userRole = $session->get('chat_user_role');
    
    \Log::info('Broadcast: Verificando autorização', [
        'roomCode' => $roomCode,
        'authenticatedRoom' => $authenticatedRoom,
        'userRole' => $userRole,
        'match' => $authenticatedRoom === $roomCode,
        'hasRole' => !empty($userRole),
    ]);
    
    // Se a sala autenticada na sessão corresponde à sala do canal, autorizar
    if ($authenticatedRoom === $roomCode && $userRole) {
        \Log::info('Broadcast: Autorizado', ['roomCode' => $roomCode, 'userRole' => $userRole]);
        return [
            'id' => $userRole,
            'role' => $userRole,
        ];
    }
    
    \Log::warning('Broadcast: Negado', [
        'roomCode' => $roomCode,
        'authenticatedRoom' => $authenticatedRoom,
        'userRole' => $userRole,
    ]);
    
    return false;
});

