<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\ChatRoom;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
| Aqui ficam APENAS canais e a rota /broadcasting/auth
| NÃO coloque lógica de controller aqui
*/

// Register the broadcasting routes with proper middleware
Broadcast::routes([
    'middleware' => [
        'web',
        'chat.session',
    ],
]);

// Private channel for chat rooms
// This requires proper authentication via chat.session middleware
Broadcast::channel('chat-room.{roomCode}', function ($user, $roomCode) {
    // The chat.session middleware ensures the user is authenticated
    // We can access the session data set by the middleware
    $authenticatedRoom = session('authenticated_chat_room');
    $userRole = session('chat_user_role');
    
    // Verify the user is authenticated for this specific room
    if ($authenticatedRoom === $roomCode && !empty($userRole)) {
        return [
            'id' => session()->getId(),
            'role' => $userRole,
        ];
    }
    
    return false;
});