<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Events\ChatMessageSent;

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

        // Check if password matches either client or provider password
        if ($request->password !== $chatRoom->client_password && $request->password !== $chatRoom->provider_password) {
            return back()->withErrors(['password' => 'Senha incorreta.']);
        }
        
        // Store which role the user authenticated as
        $role = ($request->password === $chatRoom->client_password) ? 'client' : 'provider';
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
        // Log for debugging
        \Log::info('Checking chat room access', [
            'requested_room' => $roomCode,
            'authenticated_room' => session('authenticated_chat_room'),
            'authenticated_from_payment' => session('authenticated_from_payment')
        ]);
        
        // Check if user is authenticated for this room
        $authenticatedRoom = session('authenticated_chat_room');
        if ($authenticatedRoom !== $roomCode) {
            // Check if this is a fresh session from payment approval
            $recentlyAuthenticated = session('authenticated_from_payment', false);
            
            if ($recentlyAuthenticated) {
                // Auto-authenticate the user for this room
                session(['authenticated_chat_room' => $roomCode]);
                session()->forget('authenticated_from_payment');
                \Log::info('Auto-authenticated user for chat room', ['room_code' => $roomCode]);
            } else {
                \Log::info('Redirecting to chat login', ['room_code' => $roomCode]);
                return redirect()->route('chat.login', $roomCode);
            }
        }

        $chatRoom = ChatRoom::where('room_code', $roomCode)->with('messages.sender')->firstOrFail();
        
        // Get participants (client and provider)
        $payment = $chatRoom->payment;
        $client = null;
        $provider = null;
        
        if ($payment) {
            $client = [
                'email' => $payment->email,
                'whatsapp' => $payment->number_whatsapp
            ];
            
            // Get provider from the budget if available
            if ($payment->orcamento) {
                $filaOrcamento = $payment->orcamento->filaOrcamento;
                if ($filaOrcamento && $filaOrcamento->prestador_id) {
                    $providerUser = \App\Models\User::find($filaOrcamento->prestador_id);
                    if ($providerUser) {
                        $provider = [
                            'name' => $providerUser->name,
                            'email' => $providerUser->email
                        ];
                    }
                }
            }
        }

        return view('chat.room', compact('chatRoom', 'client', 'provider'));
    }

    /**
     * Send a message in the chat room.
     */
    public function sendMessage(Request $request, $roomCode)
    {
        // Check if user is authenticated for this room
        if (session('authenticated_chat_room') !== $roomCode) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'message' => 'nullable|string|max:1000',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi,mp3,wav,pdf,doc,docx|max:10240', // 10MB limit
        ]);

        $chatRoom = ChatRoom::where('room_code', $roomCode)->firstOrFail();

        // Determine sender type based on session
        $senderType = 'client'; // Default to client
        
        // If authenticated from payment, it's definitely the client
        if (session('authenticated_from_payment')) {
            $senderType = 'client';
        } 
        // If authenticated with a specific role, use that
        elseif (session('chat_user_role')) {
            $senderType = session('chat_user_role');
        }

        $messageData = [
            'chat_room_id' => $chatRoom->id,
            'sender_id' => null, // Not storing user ID for privacy
            'sender_type' => $senderType,
            'message' => $request->message,
        ];

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->store('chat_files', 'public');
            
            $messageData['file_path'] = $filePath;
            $messageData['file_type'] = $file->getClientMimeType();
        }

        // Create message directly in database
        $message = ChatMessage::create($messageData);

        return response()->json([
            'success' => true,
            'message' => $message->load('sender'),
            'sender_type' => $messageData['sender_type'],
        ]);
    }

    /**
     * Get messages for the chat room.
     */
    public function getMessages($roomCode)
    {
        // Check if user is authenticated for this room
        if (session('authenticated_chat_room') !== $roomCode) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $chatRoom = ChatRoom::where('room_code', $roomCode)->firstOrFail();
        
        $messages = $chatRoom->messages()->with('sender')->orderBy('created_at', 'asc')->get();

        return response()->json([
            'success' => true,
            'messages' => $messages
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
        $providerPassword = Str::random(8);
        
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