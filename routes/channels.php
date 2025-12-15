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
    // Verificar se o usuário está autenticado na sala via sessão
    // Como não temos autenticação Laravel padrão, vamos permitir baseado na sessão
    if (session('authenticated_chat_room') === $roomCode) {
        return [
            'id' => session('chat_user_role', 'client'),
            'role' => session('chat_user_role', 'client'),
        ];
    }
    
    return false;
});

