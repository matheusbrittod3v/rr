<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
| Aqui ficam APENAS canais e a rota /api/broadcasting/auth
| NÃO coloque lógica de controller aqui
*/

Broadcast::routes([
    'middleware' => [
        
        'chat.session',  // Use chat session middleware for proper chat authentication
    ],
    'prefix' => 'api',
]);

// Canal privado (exige auth via chat session)
Broadcast::channel('chat-room.{roomCode}', function ($user = null, $roomCode) {
    // This will only be called if the user is properly authenticated via chat.session middleware
    // The user role is stored in the session
    $authenticatedRoom = session('authenticated_chat_room');
    $userRole = session('chat_user_role');
    
    if ($authenticatedRoom === $roomCode && !empty($userRole)) {
        return [
            'id' => session()->getId(),
            'role' => $userRole,
        ];
    }
    
    return false;
});