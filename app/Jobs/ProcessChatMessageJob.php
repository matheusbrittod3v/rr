<?php

namespace App\Jobs;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Events\ChatMessageSent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProcessChatMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $chatRoomId;
    public $senderId;
    public $senderType;
    public $message;
    public $filePath;
    public $fileType;

    /**
     * Create a new job instance.
     */
    public function __construct($chatRoomId, $senderId, $senderType, $message, $filePath = null, $fileType = null)
    {
        $this->chatRoomId = $chatRoomId;
        $this->senderId = $senderId;
        $this->senderType = $senderType;
        $this->message = $message;
        $this->filePath = $filePath;
        $this->fileType = $fileType;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Criar mensagem no banco de dados
        $chatMessage = ChatMessage::create([
            'chat_room_id' => $this->chatRoomId,
            'sender_id' => $this->senderId,
            'sender_type' => $this->senderType,
            'message' => $this->message,
            'file_path' => $this->filePath,
            'file_type' => $this->fileType,
        ]);

        // Buscar a sala para obter o room_code
        $chatRoom = ChatRoom::find($this->chatRoomId);
        
        if ($chatRoom) {
            // Disparar evento de broadcast via Reverb
            event(new ChatMessageSent(
                $chatMessage,
                $chatRoom->room_code,
                $this->senderId,
                $this->senderType === 'provider' ? 'Prestador' : 'Cliente'
            ));
        }
    }
}

