<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Services\ChatService;
use App\Events\ChatUserJoined;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

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
    public function showLogin(Request $request, string $roomCode)
    {
        $chatRoom = ChatRoom::where('room_code', $roomCode)->firstOrFail();

        // Garantir que a sessão está iniciada (não regenerar token - isso causaria 419)
        if ($request->hasSession()) {
            Log::debug('ChatRoomController: Página de login exibida', [
                'roomCode' => $roomCode,
                'session_id' => $request->session()->getId(),
                'csrf_token_exists' => !empty($request->session()->token()),
            ]);
        }

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
        try {
            // Log para debug do CSRF
            Log::debug('ChatRoomController: Tentativa de autenticação', [
                'roomCode' => $roomCode,
                'has_session' => $request->hasSession(),
                'session_id' => $request->hasSession() ? $request->session()->getId() : null,
                'csrf_token_in_request' => $request->has('_token'),
                'csrf_token_in_header' => $request->hasHeader('X-CSRF-TOKEN'),
            ]);

            $validated = $request->validate([
                'password' => ['required', 'string'],
            ]);

            $chatRoom = ChatRoom::where('room_code', $roomCode)->firstOrFail();

            // Valida senha (retorna: client | provider | false)
            $userRole = $this->chatService->validatePassword(
                $chatRoom,
                $validated['password']
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

            Log::info('ChatRoomController: Usuário autenticado no chat', [
                'roomCode'   => $roomCode,
                'role'       => $userRole,
                'session_id' => session()->getId(),
            ]);

            return redirect()->route('chat.room', $roomCode);
        } catch (\Exception $e) {
            Log::error('ChatRoomController: Erro durante autenticação', [
                'roomCode' => $roomCode,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withErrors(['password' => 'Ocorreu um erro durante a autenticação. Por favor, tente novamente.'])
                ->withInput();
        }
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

        $userRole = session('chat_user_role', 'client');

        Log::info('ChatRoomController: Sala de chat carregada', [
            'roomCode' => $roomCode,
            'session_id' => session()->getId(),
            'role' => $userRole,
        ]);

        // Disparar evento de usuário que entrou na sala
        broadcast(new ChatUserJoined($roomCode, $userRole));

        return view('chat.room', [
            'chatRoom' => $chatRoom,
        ]);
    }
}
