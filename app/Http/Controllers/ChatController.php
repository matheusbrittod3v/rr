<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\ChatSignal;
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
        // If user is admin, redirect directly to room
        if (Auth::check() && Auth::user()->can('access_admin_chat')) {
            return redirect()->route('chat.room', $roomCode);
        }

        $chatRoom = ChatRoom::where('room_code', $roomCode)->firstOrFail();
        
        return view('chat.login', compact('roomCode'));
    }

    /**
     * Authenticate access to the chat room.
     */
    public function authenticate(Request $request, $roomCode)
    {
        // Admin Bypass
        if (Auth::check() && Auth::user()->can('access_admin_chat')) {
             session(['chat_user_role' => 'admin']);
             // No need to set authenticated_chat_room because showRoom checks permission
             return redirect()->route('chat.room', $roomCode);
        }

        $request->validate([
            'password' => 'required|string',
        ]);

        $chatRoom = ChatRoom::where('room_code', $roomCode)->firstOrFail();

        $role = null;

        // Strict Check
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
        $isAdmin = Auth::check() && Auth::user()->can('access_admin_chat');

        // Check permission if not admin
        if (! $isAdmin) {
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
                    // Auto-authenticate as client because payment flow is client-side
                    session(['authenticated_chat_room' => $roomCode]);
                    session(['chat_user_role' => 'client']); 
                    session()->forget('authenticated_from_payment');
                    \Log::info('Auto-authenticated user for chat room', ['room_code' => $roomCode]);
                } else {
                    \Log::info('Redirecting to chat login', ['room_code' => $roomCode]);
                    return redirect()->route('chat.login', $roomCode);
                }
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

        return view('chat.room', compact('chatRoom', 'client', 'provider', 'isAdmin'));
    }

    /**
     * Send a message in the chat room.
     */
    public function sendMessage(Request $request, $roomCode)
    {
        $isAdmin = Auth::check() && Auth::user()->can('access_admin_chat');

        // Check permission
        if (! $isAdmin && session('authenticated_chat_room') !== $roomCode) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'message' => 'nullable|string|max:1000',
            // Allow almost any file type except executables for security, max 20MB
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,mp3,wav,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,zip,rar,7z,tar,gz|max:20480',
        ]);

        $chatRoom = ChatRoom::where('room_code', $roomCode)->firstOrFail();

        // Determine sender type
        $senderType = 'client'; // Default

        if ($isAdmin) {
            $senderType = 'admin';
        } elseif (session('chat_user_role')) {
            $senderType = session('chat_user_role');
        } elseif (session('authenticated_from_payment')) {
            $senderType = 'client';
        }

        $messageData = [
            'chat_room_id' => $chatRoom->id,
            'sender_id' => Auth::id(), // Store user ID if logged in (admin/provider logged in system)
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
        $isAdmin = Auth::check() && Auth::user()->can('access_admin_chat');

        // Check permission
        if (! $isAdmin && session('authenticated_chat_room') !== $roomCode) {
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
     * Send a signaling message for WebRTC via AJAX.
     */
    public function sendSignal(Request $request, $roomCode)
    {
        $isAdmin = Auth::check() && Auth::user()->can('access_admin_chat');

        if (! $isAdmin && session('authenticated_chat_room') !== $roomCode) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'type' => 'required|string', // offer, answer, candidate
            'payload' => 'required', // json or array
        ]);

        $chatRoom = ChatRoom::where('room_code', $roomCode)->firstOrFail();

        // Determine sender type
        $senderType = 'client';
        if ($isAdmin) {
            $senderType = 'admin';
        } elseif (session('chat_user_role')) {
            $senderType = session('chat_user_role');
        } elseif (session('authenticated_from_payment')) {
            $senderType = 'client';
        }

        // Store signal in database
        // Ensure payload is properly JSON encoded
        $payload = $request->payload;
        
        // If payload is an array (from JSON request body), encode it
        if (is_array($payload)) {
            // For SDP objects, ensure SDP string is preserved correctly
            if (isset($payload['type']) && isset($payload['sdp']) && is_string($payload['sdp'])) {
                // Clean the SDP string - remove any control characters except newlines
                $payload['sdp'] = preg_replace('/[\x00-\x08\x0B-\x1C\x1E-\x1F\x7F]/', '', $payload['sdp']);
                // Normalize line endings
                $payload['sdp'] = str_replace(["\r\n", "\r"], "\n", $payload['sdp']);
            }
            // Encode with flags to preserve newlines and special characters
            $payload = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
        } elseif (is_string($payload)) {
            // If it's already a JSON string, validate and clean it
            $decoded = json_decode($payload, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                // Clean SDP if present
                if (isset($decoded['type']) && isset($decoded['sdp']) && is_string($decoded['sdp'])) {
                    $decoded['sdp'] = preg_replace('/[\x00-\x08\x0B-\x1C\x1E-\x1F\x7F]/', '', $decoded['sdp']);
                    $decoded['sdp'] = str_replace(["\r\n", "\r"], "\n", $decoded['sdp']);
                }
                // Re-encode to ensure proper formatting
                $payload = json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
            }
            // If it's not valid JSON, use as is (shouldn't happen)
        }
        
        ChatSignal::create([
            'chat_room_id' => $chatRoom->id,
            'sender_type' => $senderType,
            'type' => $request->type,
            'payload' => $payload,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Fetch signaling messages for WebRTC via AJAX.
     */
    public function fetchSignals(Request $request, $roomCode)
    {
        $isAdmin = Auth::check() && Auth::user()->can('access_admin_chat');

        if (! $isAdmin && session('authenticated_chat_room') !== $roomCode) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $chatRoom = ChatRoom::where('room_code', $roomCode)->firstOrFail();
        $lastSignalId = $request->query('last_signal_id', 0);

        // Determine current user type to avoid fetching own signals
        $currentUserType = 'client';
        if ($isAdmin) {
            $currentUserType = 'admin';
        } elseif (session('chat_user_role')) {
            $currentUserType = session('chat_user_role');
        }

        // Fetch signals from OTHER participants in THIS specific chat room only
        // Critical: Always filter by chat_room_id first to ensure isolation
        $signals = ChatSignal::where('chat_room_id', $chatRoom->id) // Isolate by room
            ->where('id', '>', $lastSignalId)
            ->where('sender_type', '!=', $currentUserType) // Only signals from other participants
            ->where('created_at', '>=', now()->subMinutes(5)) // Only recent signals (increased window)
            ->orderBy('id', 'asc') // Process in order
            ->get();

        return response()->json([
            'success' => true,
            'signals' => $signals
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