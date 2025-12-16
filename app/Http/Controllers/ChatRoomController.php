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
     * Apenas renderiza — NÃO autentica
     */
    public function showRoom(Request $request, string $roomCode)
    {
        /**
         * Segurança: se não estiver autenticado,
         * nunca tenta "corrigir" estado aqui
         */
        if (!$this->chatService->isAuthenticated($roomCode)) {
            return redirect()->route('chat.login', $roomCode);
        }

        /**
         * Garante que a sessão já está escrita
         * antes da view carregar (Echo conecta ao abrir a página)
         */
        $request->session()->save();

        Log::info('ChatRoomController: Sala de chat carregada', [
            'roomCode'                => $roomCode,
            'session_id'              => session()->getId(),
            'authenticated_chat_room' => session('authenticated_chat_room'),
            'chat_user_role'          => session('chat_user_role'),
        ]);

        $chatRoom = ChatRoom::where('room_code', $roomCode)
            ->with('messages')
            ->firstOrFail();

        return view('chat.room', [
            'chatRoom' => $chatRoom,
        ]);
    }
}
