<?php

namespace App\Listeners;

use App\Events\ChatMessageSent;
use App\Events\CallSignal;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Jobs\ProcessChatMessageJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class HandleChatClientEvent
{
    /**
     * Handle client events (whispers) from WebSocket
     * 
     * @param string $eventName
     * @param array $data
     * @param string $socketId
     * @return void
     */
    public function handle($eventName, $data, $socketId)
    {
        Log::info('Client event received', [
            'event' => $eventName,
            'socket_id' => $socketId,
            'data' => $data
        ]);

        switch ($eventName) {
            case 'client-send-message':
                $this->handleSendMessage($data, $socketId);
                break;
            
            case 'client-call-signal':
                $this->handleCallSignal($data, $socketId);
                break;
            
            default:
                Log::warning('Unknown client event', ['event' => $eventName]);
        }
    }

    /**
     * Handle send message client event
     */
    protected function handleSendMessage($data, $socketId)
    {
        try {
            $roomCode = $data['room_code'] ?? null;
            $message = $data['message'] ?? null;
            $fileData = $data['file'] ?? null; // base64 encoded file
            $fileType = $data['file_type'] ?? null;
            $fileName = $data['file_name'] ?? null;
            $senderType = $data['sender_type'] ?? 'client';

            if (!$roomCode) {
                Log::error('Room code missing in client event');
                return;
            }

            // Verificar autenticação da sala (via session ou token)
            // Por enquanto, vamos confiar no client event já que o canal é privado
            $chatRoom = ChatRoom::where('room_code', $roomCode)->first();
            
            if (!$chatRoom) {
                Log::error('Chat room not found', ['room_code' => $roomCode]);
                return;
            }

            // Validar sender_type
            if (!in_array($senderType, ['client', 'provider'])) {
                $senderType = 'client';
            }

            $filePath = null;
            $finalFileType = $fileType;

            // Processar arquivo se fornecido (base64)
            if ($fileData && $fileName) {
                try {
                    // Decodificar base64
                    $fileContent = base64_decode($fileData);
                    if ($fileContent === false) {
                        throw new \Exception('Invalid base64 file data');
                    }

                    // Validar tamanho (max 20MB)
                    if (strlen($fileContent) > 20 * 1024 * 1024) {
                        throw new \Exception('File too large');
                    }

                    // Gerar nome único
                    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                    $uniqueFileName = time() . '_' . Str::random(10) . '.' . $extension;
                    
                    // Salvar arquivo
                    $filePath = Storage::disk('public')->put('chat_files/' . $uniqueFileName, $fileContent);
                    
                    if (!$filePath) {
                        throw new \Exception('Failed to save file');
                    }

                    $finalFileType = $fileType ?: Storage::disk('public')->mimeType($filePath);
                } catch (\Exception $e) {
                    Log::error('Error processing file in client event', [
                        'error' => $e->getMessage(),
                        'room_code' => $roomCode
                    ]);
                    // Continuar sem arquivo
                }
            }

            // Se não há mensagem nem arquivo, ignorar
            if (empty($message) && !$filePath) {
                Log::warning('Empty message and no file in client event');
                return;
            }

            // Dispatch job para processar mensagem
            ProcessChatMessageJob::dispatch(
                $chatRoom->id,
                Auth::id() ?? 0,
                $senderType,
                $message,
                $filePath,
                $finalFileType
            );

            Log::info('Message queued from client event', [
                'room_code' => $roomCode,
                'has_message' => !empty($message),
                'has_file' => !!$filePath
            ]);

        } catch (\Exception $e) {
            Log::error('Error handling send message client event', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Handle call signal client event
     */
    protected function handleCallSignal($data, $socketId)
    {
        try {
            $roomCode = $data['room_code'] ?? null;
            $type = $data['type'] ?? null;
            $payload = $data['payload'] ?? null;
            $senderType = $data['sender_type'] ?? 'client';

            if (!$roomCode || !$type || !$payload) {
                Log::error('Invalid call signal data', ['data' => $data]);
                return;
            }

            // Validar tipo
            $allowedTypes = ['offer', 'answer', 'candidate', 'bye', 'media_frame', 'call_action', 'user_presence'];
            if (!in_array($type, $allowedTypes)) {
                Log::error('Invalid call signal type', ['type' => $type]);
                return;
            }

            // Validar sender_type
            if (!in_array($senderType, ['client', 'provider'])) {
                $senderType = 'client';
            }

            // Verificar se a sala existe
            $chatRoom = ChatRoom::where('room_code', $roomCode)->first();
            if (!$chatRoom) {
                Log::error('Chat room not found for call signal', ['room_code' => $roomCode]);
                return;
            }

            // Validar SDP para offer/answer
            if (in_array($type, ['offer', 'answer']) && is_array($payload)) {
                if (!isset($payload['type']) || !isset($payload['sdp'])) {
                    Log::error('Invalid SDP structure', ['payload' => $payload]);
                    return;
                }
            }

            // Broadcast signal via Reverb
            event(new CallSignal($roomCode, [
                'type' => $type,
                'payload' => $payload,
                'sender_type' => $senderType,
            ]));

            Log::info('Call signal broadcasted from client event', [
                'room_code' => $roomCode,
                'type' => $type
            ]);

        } catch (\Exception $e) {
            Log::error('Error handling call signal client event', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}

