@extends('layouts.app')

@section('title', 'Chat de Atendimento - Renttool')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-4 sm:py-8">
    <div class="max-w-5xl mx-auto px-4">
        <!-- Chat Container -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-100">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-white font-bold text-lg">Chat de Atendimento</h1>
                            <p class="text-blue-100 text-sm flex items-center space-x-2">
                                <span id="connection-status-indicator" class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></span>
                                <span>Você: {{ session('chat_user_role', 'client') === 'client' ? 'Cliente' : 'Prestador' }}</span>
                                <span class="mx-1">•</span>
                                <span id="other-user-status" class="flex items-center">
                                    <span class="w-2 h-2 bg-gray-400 rounded-full mr-1"></span>
                                    <span>{{ session('chat_user_role', 'client') === 'client' ? 'Prestador' : 'Cliente' }}</span>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="hidden sm:inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 text-white backdrop-blur-sm font-mono">
                            {{ $chatRoom->room_code }}
                        </span>
                        <a href="{{ url('/') }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-white/10 text-white hover:bg-white/20 transition-all backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Sair
                        </a>
                    </div>
                </div>
            </div>

            <!-- Messages Container -->
            <div id="chat-messages" class="h-[500px] overflow-y-auto p-6 bg-gradient-to-b from-white to-gray-50 scroll-smooth">
                @forelse($chatRoom->messages as $message)
                    <div class="mb-4 animate-fade-in" data-message-id="{{ $message->id }}">
                        <div class="flex {{ $message->sender_type === 'client' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-xs sm:max-w-md lg:max-w-lg">
                                <div class="flex items-end gap-2 {{ $message->sender_type === 'client' ? 'flex-row-reverse' : 'flex-row' }}">
                                    <div class="px-4 py-3 rounded-2xl shadow-sm {{ 
                                        $message->sender_type === 'client' ? 'bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-br-sm' : 
                                        'bg-white text-gray-800 border border-gray-200 rounded-bl-sm'
                                    }}">
                                        @if($message->message)
                                            <p class="text-sm break-words">{{ e($message->message) }}</p>
                                        @endif
                                        
                                        @if($message->file_path)
                                            @php
                                                $fileExt = strtolower(pathinfo($message->file_path, PATHINFO_EXTENSION));
                                                $isImage = in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                            @endphp
                                            @if($isImage)
                                                <div class="mt-2">
                                                    <img src="{{ asset('storage/' . e($message->file_path)) }}" alt="Imagem anexada" class="max-w-xs rounded-lg cursor-pointer" loading="lazy" onclick="window.open('{{ asset('storage/' . e($message->file_path)) }}', '_blank')">
                                                </div>
                                            @else
                                                <div class="mt-2 p-2 bg-black/10 rounded-lg text-sm flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                    </svg>
                                                    <a href="{{ asset('storage/' . e($message->file_path)) }}" target="_blank" rel="noopener noreferrer" class="hover:underline">
                                                        Ver anexo ({{ strtoupper($fileExt) }})
                                                    </a>
                                                </div>
                                            @endif
                                        @endif
                                        
                                        <p class="text-xs mt-1.5 {{ $message->sender_type === 'client' ? 'text-blue-200' : 'text-gray-500' }}">
                                            {{ $message->created_at->format('H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <p class="mt-4 text-sm text-gray-500">Nenhuma mensagem ainda</p>
                            <p class="mt-1 text-xs text-gray-400">Comece a conversa enviando uma mensagem</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Message Input -->
            <div class="bg-white border-t border-gray-200 p-4">
                <form id="chat-form" class="flex items-end space-x-3" onsubmit="return false;">
                    @csrf
                    <div class="flex-1">
                        <textarea 
                            id="message-input" 
                            class="w-full px-4 py-3 text-gray-700 bg-gray-50 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none transition-all" 
                            placeholder="Digite sua mensagem..." 
                            rows="1" 
                            maxlength="1000"
                        ></textarea>
                        <div class="flex items-center justify-between mt-2 px-1">
                            <span class="text-xs text-gray-500">Pressione Enter para enviar</span>
                            <span id="char-count" class="text-xs text-gray-400">0/1000</span>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2 pb-8">
                        <button 
                            type="button" 
                            id="send-button" 
                            class="p-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all hover:scale-105 shadow-lg active:scale-95 transform duration-150 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            title="Enviar mensagem"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fade-in 0.3s ease-out;
    }
    
    #chat-messages::-webkit-scrollbar { 
        width: 6px; 
    }
    #chat-messages::-webkit-scrollbar-track { 
        background: #f1f5f9; 
        border-radius: 10px; 
    }
    #chat-messages::-webkit-scrollbar-thumb { 
        background: #cbd5e1; 
        border-radius: 10px; 
    }
</style>

<script>
@php
    $chatConfig = [
        'roomCode' => $chatRoom->room_code,
        'senderType' => session('chat_user_role', 'client'),
    ];
    
@endphp

const CHAT_CONFIG = @json($chatConfig);

// ========== CONFIGURAÇÃO DO CHAT ==========
let privateChannel = null;
let isConnected = false;

// Elementos DOM
const elements = {
    messageInput: document.getElementById('message-input'),
    sendButton: document.getElementById('send-button'),
    chatMessages: document.getElementById('chat-messages'),
    charCount: document.getElementById('char-count'),
    chatForm: document.getElementById('chat-form'),
};

// ========== FUNÇÕES AUXILIARES ==========

// Auto-resize textarea
elements.messageInput?.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
    elements.charCount.textContent = this.value.length + '/1000';
});

// Scroll para o final
function scrollToBottom() {
    elements.chatMessages.scrollTop = elements.chatMessages.scrollHeight;
}

// Adicionar mensagem ao chat
function addMessageToChat(message, senderType) {
    const messageDiv = document.createElement('div');
    messageDiv.className = 'mb-4 animate-fade-in';
    
    const isClient = senderType === 'client';
    const isCurrentUser = senderType === CHAT_CONFIG.senderType;
    
    // Escape HTML para prevenir XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    const safeMessage = message ? escapeHtml(message) : '';
    const timeString = new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    
    messageDiv.innerHTML = `
        <div class="flex ${isClient ? 'justify-end' : 'justify-start'}">
            <div class="max-w-xs sm:max-w-md lg:max-w-lg">
                <div class="flex items-end gap-2 ${isClient ? 'flex-row-reverse' : 'flex-row'}">
                    <div class="px-4 py-3 rounded-2xl shadow-sm ${
                        isClient ? 'bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-br-sm' : 
                        'bg-white text-gray-800 border border-gray-200 rounded-bl-sm'
                    }">
                        ${safeMessage ? `<p class="text-sm break-words">${safeMessage}</p>` : ''}
                        <p class="text-xs mt-1.5 ${isClient ? 'text-blue-200' : 'text-gray-500'}">
                            ${escapeHtml(timeString)}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    elements.chatMessages.appendChild(messageDiv);
    scrollToBottom();
}

// ========== WEBSOCKET - LARAVEL ECHO + REVERB ==========

function initializeChat() {
    // Aguardar Echo estar disponível
    function waitForEcho(callback, maxAttempts = 50) {
        let attempts = 0;
        
        function checkEcho() {
            attempts++;
            
            if (window.Echo && typeof window.Echo.private === 'function') {
                console.log('[Chat] ✅ Echo encontrado!');
                callback();
            } else if (attempts >= maxAttempts) {
                console.error('[Chat] ❌ Timeout: Echo não foi carregado');
                alert('Erro ao conectar ao chat. Recarregue a página.');
            } else {
                setTimeout(checkEcho, 100);
            }
        }
        
        checkEcho();
    }
    
    waitForEcho(function() {
        const roomCode = CHAT_CONFIG.roomCode;
        const channelName = `private-chat-room.${roomCode}`;
        
        console.log('[Chat] Conectando ao canal:', channelName);
        console.log('[Chat] Configuração:', {
            roomCode: roomCode,
            senderType: CHAT_CONFIG.senderType,
            channelName: channelName
        });
        
        try {
            // Criar canal privado
            privateChannel = window.Echo.private(channelName);
            
            // Listener para quando o canal é autorizado e subscrito
            privateChannel.subscribed(() => {
                console.log('[Chat] ✅ Canal autorizado e subscrito com sucesso!');
                isConnected = true;
                
                // Atualizar indicador de status
                const statusIndicator = document.getElementById('connection-status-indicator');
                if (statusIndicator) {
                    statusIndicator.classList.remove('bg-yellow-400', 'bg-red-400');
                    statusIndicator.classList.add('bg-green-400');
                }
            });
            
            // Listener para erro de autorização
            privateChannel.error((error) => {
                console.error('[Chat] ❌ Erro ao autorizar canal:', error);
                console.error('[Chat] Detalhes do erro:', {
                    status: error.status,
                    error: error.error,
                    message: error.message,
                    type: error.type
                });
                console.error('[Chat] Configuração atual:', {
                    roomCode: CHAT_CONFIG.roomCode,
                    senderType: CHAT_CONFIG.senderType,
                    channelName: channelName
                });
                
                // Atualizar indicador de status
                const statusIndicator = document.getElementById('connection-status-indicator');
                if (statusIndicator) {
                    statusIndicator.classList.remove('bg-yellow-400', 'bg-green-400', 'animate-pulse');
                    statusIndicator.classList.add('bg-red-400');
                }
                
                // Mensagem de erro mais específica
                let errorMessage = 'Erro ao autorizar acesso ao chat.';
                if (error.status === 403) {
                    errorMessage = 'Acesso negado (403). Verifique se você está autenticado corretamente. Tente fazer login novamente.';
                } else if (error.status === 401) {
                    errorMessage = 'Não autorizado (401). Sua sessão pode ter expirado. Tente fazer login novamente.';
                }
                
                alert(errorMessage);
                isConnected = false;
            });
            
            // Listener para receber mensagens via whisper
            privateChannel.listenForWhisper('client-chat-message', (data) => {
                console.log('[Chat] Mensagem recebida via whisper:', data);
                console.log('[Chat] Comparando sender_type:', {
                    received: data.sender_type,
                    current: CHAT_CONFIG.senderType,
                    match: data.sender_type === CHAT_CONFIG.senderType
                });
                
                // Ignorar mensagens do próprio usuário
                if (data.sender_type === CHAT_CONFIG.senderType) {
                    console.log('[Chat] Mensagem ignorada (próprio usuário)');
                    return;
                }
                
                // Adicionar mensagem ao chat
                console.log('[Chat] Adicionando mensagem ao chat');
                addMessageToChat(data.message, data.sender_type);
            });
            
            // Listener para status de conexão WebSocket
            if (window.Echo.connector && window.Echo.connector.pusher) {
                const pusher = window.Echo.connector.pusher;
                
                pusher.connection.bind('connected', () => {
                    console.log('[Chat] ✅ WebSocket CONECTADO');
                });
                
                pusher.connection.bind('disconnected', () => {
                    console.warn('[Chat] ⚠️ WebSocket DESCONECTADO');
                    isConnected = false;
                });
                
                pusher.connection.bind('error', (error) => {
                    console.error('[Chat] ❌ Erro na conexão WebSocket:', error);
                    isConnected = false;
                });
                
                pusher.connection.bind('state_change', (states) => {
                    console.log('[Chat] Estado da conexão:', states.previous, '->', states.current);
                });
            }
            
            console.log('[Chat] ✅ Chat inicializado! Aguardando autorização do canal...');
            
        } catch (error) {
            console.error('[Chat] ❌ Erro ao inicializar chat:', error);
            console.error('[Chat] Stack trace:', error.stack);
            alert('Erro ao conectar ao chat. Recarregue a página.');
        }
    });
}

// ========== ENVIAR MENSAGEM ==========

async function sendMessage() {
    const message = elements.messageInput.value.trim();
    
    if (!message) {
        return;
    }
    
    if (!privateChannel) {
        console.error('[Chat] Canal privado não disponível');
        alert('Não conectado ao chat. Aguarde a conexão...');
        return;
    }
    
    // Verificar se o canal está subscrito
    if (!isConnected) {
        console.warn('[Chat] Canal ainda não está autorizado. Tentando enviar mesmo assim...');
    }
    
    // Desabilitar botão durante envio
    elements.sendButton.disabled = true;
    elements.sendButton.classList.add('opacity-50', 'cursor-not-allowed');
    
    try {
        const messageData = {
            room_code: CHAT_CONFIG.roomCode,
            message: message,
            sender_type: CHAT_CONFIG.senderType,
            timestamp: Date.now(),
        };
        
        console.log('[Chat] Enviando mensagem via whisper:', messageData);
        
        // Enviar mensagem via whisper (client event)
        privateChannel.whisper('client-chat-message', messageData);
        
        console.log('[Chat] ✅ Mensagem enviada via whisper');
        
        // Adicionar mensagem localmente (otimista)
        addMessageToChat(message, CHAT_CONFIG.senderType);
        
        // Limpar input
        elements.messageInput.value = '';
        elements.messageInput.style.height = 'auto';
        elements.charCount.textContent = '0/1000';
        
    } catch (error) {
        console.error('[Chat] ❌ Erro ao enviar mensagem:', error);
        console.error('[Chat] Stack trace:', error.stack);
        alert('Erro ao enviar mensagem. Tente novamente.');
    } finally {
        // Reabilitar botão
        elements.sendButton.disabled = false;
        elements.sendButton.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

// ========== EVENT LISTENERS ==========

// Form submit
elements.chatForm?.addEventListener('submit', function(e) {
    e.preventDefault();
    sendMessage();
});

// Botão enviar
elements.sendButton?.addEventListener('click', function(e) {
    e.preventDefault();
    sendMessage();
});

// Enter para enviar (Shift+Enter para nova linha)
elements.messageInput?.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});

// ========== INICIALIZAÇÃO ==========

// Aguardar DOM estar pronto
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeChat);
} else {
    initializeChat();
}

// Scroll inicial
window.addEventListener('load', function() {
    scrollToBottom();
});
</script>
@endsection

