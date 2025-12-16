<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
| Este arquivo registra canais de broadcast.
| TODOS os canais são PÚBLICOS - não usamos canais privados.
*/

// Rotas de broadcasting (necessário para funcionamento do Reverb/Pusher)
// Não remove esta linha, mesmo usando apenas canais públicos
Broadcast::routes();

// Canais públicos - qualquer um pode se inscrever
// Não é necessário registrar canais públicos, mas deixamos aqui para documentação

// Canal de chat - usado para mensagens, typing, e chamadas WebRTC
// Broadcast::channel('chat-room.{roomCode}', fn () => true);

// Canal de pagamentos - usado para atualizações de status
// Broadcast::channel('payments', fn () => true);
// Broadcast::channel('payments.{id}', fn () => true);

// NOTA: Canais públicos não precisam de autenticação
// O Echo no frontend usa .channel() em vez de .private()
