<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatRoomController extends Controller
{
    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Mostrar página de login da sala
     */
    public function showLogin($roomCode)
    {
        $chatRoom = ChatRoom::where('room_code', $roomCode)->firstOrFail();
        
        return view('chat.login', compact('roomCode', 'chatRoom'));
    }

    /**
     * Autenticar acesso à sala
     */
    public function authenticate(Request $request, $roomCode)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $chatRoom = ChatRoom::where('room_code', $roomCode)->firstOrFail();

        // Validar senha usando o service
        $userRole = $this->chatService->validatePassword($chatRoom, $request->password);

        if (!$userRole) {
            return back()->withErrors(['password' => 'Senha incorreta.'])->withInput();
        }
        
        // Armazenar na sessão
        session(['chat_user_role' => $userRole]);
        session(['authenticated_chat_room' => $roomCode]);

        Log::info('Usuário autenticado na sala de chat', [
            'room_code' => $roomCode,
            'user_role' => $userRole,
        ]);

        return redirect()->route('chat.room', $roomCode);
    }

    /**
     * Mostrar a sala de chat
     */
    public function showRoom($roomCode)
    {
        // Verificar autenticação
        if (!$this->chatService->isAuthenticated($roomCode)) {
            // Verificar se é uma sessão fresca de aprovação de pagamento
            $recentlyAuthenticated = session('authenticated_from_payment', false);
            
            if ($recentlyAuthenticated) {
                // Auto-autenticar como cliente (fluxo de pagamento é do lado do cliente)
                session(['authenticated_chat_room' => $roomCode]);
                session(['chat_user_role' => 'client']);
                session()->forget('authenticated_from_payment');
            } else {
                return redirect()->route('chat.login', $roomCode);
            }
        }

        $chatRoom = ChatRoom::where('room_code', $roomCode)
            ->with('messages')
            ->firstOrFail();

        return view('chat.room', compact('chatRoom'));
    }
}

