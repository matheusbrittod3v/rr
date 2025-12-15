<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\Payment;
use App\Jobs\ProcessChatMessageJob;
use App\Events\CallSignal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    /**
     * Show the chat room login page.
     */
    public function showLogin($roomCode)
    {
        $chatRoom = ChatRoom::where('room_code', $roomCode)->firstOrFail();
        
        return view('chat.login', compact('roomCode'));
    }

    /**
     * Authenticate access to the chat room.
     */
    public function authenticate(Request $request, $roomCode)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $chatRoom = ChatRoom::where('room_code', $roomCode)->firstOrFail();

        $role = null;

        // Strict Check - apenas client ou provider
        if ($request->password === $chatRoom->client_password) {
            $role = 'client';
        } elseif ($request->password === $chatRoom->provider_password) {
            $role = 'provider';
        } else {
            return back()->withErrors(['password' => 'Senha incorreta.']);
        }
        
        // Store which role the user authenticated as
        session(['chat_user_role' => $role]);

        // Store authenticated room code in session
        session(['authenticated_chat_room' => $roomCode]);

        return redirect()->route('chat.room', $roomCode);
    }

    /**
     * Show the chat room.
     */
    public function showRoom($roomCode)
    {
        // Check if user is authenticated for this room
        $authenticatedRoom = session('authenticated_chat_room');
        if ($authenticatedRoom !== $roomCode) {
            // Check if this is a fresh session from payment approval
            $recentlyAuthenticated = session('authenticated_from_payment', false);
            
            if ($recentlyAuthenticated) {
                // Auto-authenticate as client because payment flow is client-side
                session(['authenticated_chat_room' => $roomCode]);
                session(['chat_user_role' => 'client']); 
                session()->forget('authenticated_from_payment');
            } else {
                return redirect()->route('chat.login', $roomCode);
            }
        }

        $chatRoom = ChatRoom::where('room_code', $roomCode)->with('messages')->firstOrFail();

        return view('chat.room', compact('chatRoom'));
    }

    /**
     * Process message from WebSocket client event
     * Este endpoint é chamado internamente quando um whisper é enviado
     * Mantém a arquitetura orientada a eventos
     */
    public function processMessageFromWebSocket(Request $request, $roomCode)
    {
        // Verificar autenticação via session
        if (session('authenticated_chat_room') !== $roomCode) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'message' => 'nullable|string|max:1000',
            'file' => 'nullable|string', // base64 encoded
            'file_name' => 'nullable|string',
            'file_type' => 'nullable|string',
            'sender_type' => 'required|string|in:client,provider',
        ]);

        $chatRoom = ChatRoom::where('room_code', $roomCode)->firstOrFail();
        $senderType = $request->sender_type;

        $filePath = null;
        $fileType = $request->file_type;

        // Processar arquivo se fornecido (base64)
        if ($request->file && $request->file_name) {
            try {
                $fileContent = base64_decode($request->file);
                if ($fileContent === false) {
                    throw new \Exception('Invalid base64 file data');
                }

                // Validar tamanho (max 20MB)
                if (strlen($fileContent) > 20 * 1024 * 1024) {
                    throw new \Exception('File too large');
                }

                $extension = pathinfo($request->file_name, PATHINFO_EXTENSION);
                $uniqueFileName = time() . '_' . Str::random(10) . '.' . $extension;
                
                $filePath = Storage::disk('public')->put('chat_files/' . $uniqueFileName, $fileContent);
                
                if (!$filePath) {
                    throw new \Exception('Failed to save file');
                }

                $fileType = $fileType ?: Storage::disk('public')->mimeType($filePath);
            } catch (\Exception $e) {
                \Log::error('Error processing file from WebSocket', [
                    'error' => $e->getMessage(),
                    'room_code' => $roomCode
                ]);
            }
        }

        // Se não há mensagem nem arquivo, retornar erro
        if (empty($request->message) && !$filePath) {
            return response()->json(['error' => 'Message or file is required'], 400);
        }

        // Dispatch job para processar mensagem
        ProcessChatMessageJob::dispatch(
            $chatRoom->id,
            Auth::id() ?? 0,
            $senderType,
            $request->message,
            $filePath,
            $fileType
        );

        return response()->json([
            'success' => true,
            'message' => 'Mensagem processada com sucesso'
        ]);
    }

    /**
     * Process call signal from WebSocket client event
     * Este endpoint é chamado internamente quando um whisper de call signal é enviado
     */
    public function processCallSignalFromWebSocket(Request $request, $roomCode)
    {
        // Verificar autenticação via session
        if (session('authenticated_chat_room') !== $roomCode) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'type' => 'required|string|in:offer,answer,candidate,bye,media_frame,call_action,user_presence',
            'payload' => 'required',
            'sender_type' => 'required|string|in:client,provider',
        ]);

        $chatRoom = ChatRoom::where('room_code', $roomCode)->firstOrFail();

        // Broadcast signal via Reverb
        event(new CallSignal($roomCode, [
            'type' => $request->type,
            'payload' => $request->payload,
            'sender_type' => $request->sender_type,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Sinal processado com sucesso'
        ]);
    }

    /**
     * Create a chat room for a payment (called when payment is confirmed).
     */
    public static function createChatRoomForPayment(Payment $payment)
    {
        // Generate a unique room code
        $roomCode = Str::random(20);
        
        // Generate separate passwords for client and provider
        $clientPassword = Str::random(8);
        // Ensure they are different
        do {
            $providerPassword = Str::random(8);
        } while ($providerPassword === $clientPassword);
        
        // Create the chat room
        $chatRoom = ChatRoom::create([
            'payment_id' => $payment->id,
            'room_code' => $roomCode,
            'client_password' => $clientPassword,
            'provider_password' => $providerPassword,
        ]);
        
        return $chatRoom;
    }
}
