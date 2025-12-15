<?php

namespace App\Http\Controllers;

use App\Listeners\HandleChatClientEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReverbWebhookController extends Controller
{
    /**
     * Handle client events from Reverb
     * Este endpoint é chamado pelo Reverb quando recebe client events (whispers)
     * O frontend nunca chama este endpoint diretamente - apenas envia via WebSocket
     */
    public function handleClientEvent(Request $request)
    {
        try {
            $eventName = $request->input('name');
            $channel = $request->input('channel');
            $data = $request->input('data', []);
            $socketId = $request->input('socket_id');

            Log::info('Reverb client event received', [
                'event' => $eventName,
                'channel' => $channel,
                'socket_id' => $socketId,
                'data' => $data
            ]);

            // Processar apenas eventos de chat
            if (strpos($channel, 'private-chat-room.') === 0) {
                $listener = new HandleChatClientEvent();
                $listener->handle($eventName, $data, $socketId);
            }

            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
            Log::error('Error processing Reverb client event', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}

