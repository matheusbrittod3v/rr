<?php

use Illuminate\Support\Facades\Broadcast;

// Public channel - no authentication required
Broadcast::channel('chat-room.{roomCode}', function ($user, $roomCode) {
    // Allow anyone to join the channel
    return true;
});
