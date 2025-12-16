<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
| Aqui ficam APENAS canais e a rota /broadcasting/auth
| NÃO coloque lógica de controller aqui
*/

// Register the broadcasting routes
Broadcast::routes();

// Public channel for chat rooms - anyone can join
Broadcast::channel('chat-room.{roomCode}', fn () => true);