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
Broadcast::channel('private-chat-room.{roomCode}', function ($user, $roomCode) {
    $request = request();
    
    // Verificar se tem sessão
    if (!$request->hasSession()) {
        return false;
    }
    
    // Verificar se a sessão tem os dados corretos
    $authenticatedRoom = $request->session()->get('authenticated_chat_room');
    $userRole = $request->session()->get('chat_user_role');
    
    // Se a sala autenticada na sessão corresponde à sala do canal, autorizar
    if ($authenticatedRoom === $roomCode && $userRole) {
        return [
            'id' => $userRole,
            'role' => $userRole,
        ];
    }
    
    return false;
});

