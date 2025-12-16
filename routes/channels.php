<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat-room.{roomCode}', function ($user, $roomCode) {
    $request = request();

    // Sessão DEVE existir
    if (!$request->hasSession()) {
        \Log::warning('Broadcast: sessão ausente');
        return false;
    }

    $session = $request->session();

    // ❌ NUNCA chame start() aqui
    if (!$session->isStarted()) {
        \Log::warning('Broadcast: sessão não iniciada');
        return false;
    }

    $authenticatedRoom = $session->get('authenticated_chat_room');
    $userRole = $session->get('chat_user_role');

    \Log::info('Broadcast: validação', [
        'roomCode' => $roomCode,
        'sessionId' => $session->getId(),
        'authenticatedRoom' => $authenticatedRoom,
        'userRole' => $userRole,
    ]);

    if ($authenticatedRoom === $roomCode && !empty($userRole)) {
        return [
            'id'   => $session->getId(),
            'role' => $userRole,
        ];
    }

    return false;
});
