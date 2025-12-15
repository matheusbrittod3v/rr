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
        
        // Armazenar na sessão de forma explícita
        $session = $request->session();
        $session->put('chat_user_role', $userRole);
        $session->put('authenticated_chat_room', $roomCode);
        
        // Garantir que a sessão seja salva imediatamente
        $session->save();
        
        // Log para debug
        Log::info('ChatRoomController: Sessão de autenticação salva', [
            'roomCode' => $roomCode,
            'userRole' => $userRole,
            'sessionId' => $session->getId(),
            'authenticated_chat_room' => $session->get('authenticated_chat_room'),
            'chat_user_role' => $session->get('chat_user_role'),
        ]);

        // Criar redirect e garantir que a sessão seja preservada
        $redirect = redirect()->route('chat.room', $roomCode)
            ->with('success', 'Autenticado com sucesso!');
        
        // Garantir que os cookies de sessão sejam enviados no redirect
        // Isso é importante para que a sessão seja compartilhada com a requisição de broadcasting
        return $redirect;
    }

    /**
     * Mostrar a sala de chat
     */
    public function showRoom(Request $request, $roomCode)
    {
        // Verificar autenticação
        if (!$this->chatService->isAuthenticated($roomCode)) {
            // Verificar se é uma sessão fresca de aprovação de pagamento
            $recentlyAuthenticated = session('authenticated_from_payment', false);
            
            if ($recentlyAuthenticated) {
                // Auto-autenticar como cliente (fluxo de pagamento é do lado do cliente)
                $session = $request->session();
                $session->put('authenticated_chat_room', $roomCode);
                $session->put('chat_user_role', 'client');
                $session->forget('authenticated_from_payment');
                $session->save();
                
                Log::info('ChatRoomController: Auto-autenticação do fluxo de pagamento', [
                    'roomCode' => $roomCode,
                    'sessionId' => $session->getId(),
                ]);
            } else {
                return redirect()->route('chat.login', $roomCode);
            }
        }
        
        // Garantir que a sessão está salva antes de renderizar a view
        // Isso é importante para que o Echo possa autenticar o canal privado
        $session = $request->session();
        $session->save();
        
        Log::info('ChatRoomController: Exibindo sala de chat', [
            'roomCode' => $roomCode,
            'sessionId' => $session->getId(),
            'authenticated_chat_room' => $session->get('authenticated_chat_room'),
            'chat_user_role' => $session->get('chat_user_role'),
        ]);

        $chatRoom = ChatRoom::where('room_code', $roomCode)
            ->with('messages')
            ->firstOrFail();

        return view('chat.room', compact('chatRoom'));
    }
}

