<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\ChatRoom;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Autenticação para canais privados de chat
Broadcast::channel('private-chat-room.{roomCode}', function ($user, $roomCode) {
    // Verificar se o usuário tem acesso à sala via session
    $authenticatedRoom = session('authenticated_chat_room');
    if ($authenticatedRoom === $roomCode) {
        return [
            'id' => $user->id ?? 0,
            'role' => session('chat_user_role', 'client'),
        ];
    }
    
    // Verificar se a sala existe (para permitir acesso via token futuro)
    $chatRoom = ChatRoom::where('room_code', $roomCode)->first();
    if ($chatRoom) {
        // Permitir acesso se tiver autenticação na sessão
        return session('authenticated_chat_room') === $roomCode ? [
            'id' => $user->id ?? 0,
            'role' => session('chat_user_role', 'client'),
        ] : false;
    }
    
    return false;
});
