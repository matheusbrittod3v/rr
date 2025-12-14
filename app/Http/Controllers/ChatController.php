<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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

        $chatRoom = ChatRoom::where('room_code', $roomCode)->firstOrFail();

        return view('chat.room', compact('chatRoom', 'isAdmin'));
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