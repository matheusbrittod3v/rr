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

        if (!$roomCode) {
            Log::warning('ChatSessionMiddleware: roomCode não encontrado na requisição');
            abort(404, 'Sala de chat não encontrada');
        }

        // Verificar se está autenticado na sala
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

