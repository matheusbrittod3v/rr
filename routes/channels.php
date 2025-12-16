<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

Broadcast::routes([
    'middleware' => ['web'],
]);

Broadcast::channel('chat-room.{roomCode}', function ($user = null, string $roomCode) {
    return session('authenticated_chat_room') === $roomCode;
});
