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
        'web',
    ],
    'prefix' => 'api',
]);

// Canal público (não exige auth)
Broadcast::channel('chat-room.{roomCode}', function ($user = null, $roomCode) {
    return true;
});
