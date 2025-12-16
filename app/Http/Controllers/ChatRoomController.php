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
     * Exibe a página de login da sala de chat
     */
    public function showLogin(string $roomCode)
    {
        $chatRoom = ChatRoom::where('room_code', $roomCode)->firstOrFail();

        return view('chat.login', [
            'roomCode' => $roomCode,
            'chatRoom' => $chatRoom,
        ]);
    }

    /**
     * Autentica o usuário na sala de chat
     * Fluxo consistente com pagamento e compatível com Reverb
     */
    public function authenticate(Request $request, string $roomCode)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $chatRoom = ChatRoom::where('room_code', $roomCode)->firstOrFail();

        // Valida senha (retorna: client | provider | false)
        $userRole = $this->chatService->validatePassword(
            $chatRoom,
            $request->input('password')
        );

        if (!$userRole) {
            return back()
                ->withErrors(['password' => 'Senha incorreta.'])
                ->withInput();
        }

        /**
         * Cria sessão do chat
         * NÃO usa Auth do Laravel
         * NÃO usa flash
         */
        $this->chatService->setChatRoomSession(
            $chatRoom,
            $userRole,
            false
        );

        /**
         * 🔑 CRÍTICO
         * Força persistência imediata da sessão
         * antes do redirect (Echo/Reverb dependem disso)
         */
        $request->session()->save();

        Log::info('ChatRoomController: Usuário autenticado no chat', [
            'roomCode'   => $roomCode,
            'role'       => $userRole,
            'session_id' => session()->getId(),
        ]);

        return redirect()->route('chat.room', $roomCode);
    }

    /**
     * Exibe a sala de chat
     * 
     * Controller 100% "burro" - autenticação isolada no middleware
     * Apenas renderiza a view
     */
    public function showRoom(Request $request, string $roomCode)
    {
        // Middleware já garantiu autenticação e salvou a sessão
        // Aqui apenas buscamos dados e renderizamos

        $chatRoom = ChatRoom::where('room_code', $roomCode)
            ->with('messages')
            ->firstOrFail();

        Log::info('ChatRoomController: Sala de chat carregada', [
            'roomCode' => $roomCode,
            'session_id' => session()->getId(),
            'role' => session('chat_user_role'),
        ]);

        return view('chat.room', [
            'chatRoom' => $chatRoom,
        ]);
    }
}
