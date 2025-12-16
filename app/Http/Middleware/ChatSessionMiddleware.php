<?php

namespace App\Http\Middleware;

use App\Services\ChatService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para gerenciar autenticação de sessão de chat
 * 
 * Isola completamente a lógica de autenticação do Controller (SRP)
 * Verifica se o usuário está autenticado na sala de chat antes de permitir acesso
 */
class ChatSessionMiddleware
{
    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roomCode = null): Response
    {
        // Obter roomCode da rota se não foi passado como parâmetro
        $roomCode = $roomCode ?? $request->route('roomCode');

        // Para requisições de broadcasting, verificar se é uma requisição de autenticação de canal
        $isBroadcastingAuth = $request->is('api/broadcasting/auth');
        
        if (!$roomCode && !$isBroadcastingAuth) {
            Log::warning('ChatSessionMiddleware: roomCode não encontrado na requisição');
            abort(404, 'Sala de chat não encontrada');
        }

        // Se for uma requisição de autenticação de canal, permitir que prossiga
        // desde que haja uma sessão válida
        if ($isBroadcastingAuth) {
            // Verificar se há sessão ativa
            if (!$request->hasSession() || !$request->session()->isStarted()) {
                Log::warning('ChatSessionMiddleware: Sessão não encontrada ou não iniciada para broadcasting auth');
                return response()->json(['error' => 'Sessão não encontrada'], 401);
            }
            
            // Para broadcasting auth, basta ter uma sessão válida
            Log::debug('ChatSessionMiddleware: Sessão válida para broadcasting auth', [
                'session_id' => $request->session()->getId(),
                'authenticated_room' => session('authenticated_chat_room'),
                'user_role' => session('chat_user_role'),
            ]);
            
            return $next($request);
        }

        // Verificar se está autenticado na sala (para acesso normal às rotas)
        if (!$this->chatService->isAuthenticated($roomCode)) {
            Log::info('ChatSessionMiddleware: Usuário não autenticado', [
                'roomCode' => $roomCode,
                'path' => $request->path(),
                'session_id' => $request->hasSession() ? $request->session()->getId() : null,
            ]);

            // Redirecionar para login se não estiver autenticado
            return redirect()->route('chat.login', $roomCode)
                ->with('error', 'Você precisa fazer login para acessar esta sala.');
        }

        Log::debug('ChatSessionMiddleware: Usuário autenticado', [
            'roomCode' => $roomCode,
            'session_id' => $request->session()->getId(),
            'role' => session('chat_user_role'),
        ]);

        $response = $next($request);

        // Garantir que a sessão está salva após o processamento
        // Isso é crítico para que o Echo possa autenticar o canal privado
        if ($request->hasSession()) {
            $request->session()->save();
        }

        return $response;
    }
}