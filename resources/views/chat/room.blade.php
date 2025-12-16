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
                        {{-- Botão Chamada de Voz --}}
                        <button 
                            id="voice-call-btn"
                            class="p-2 bg-white/10 text-white hover:bg-green-500 rounded-lg transition-all backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-white hover:scale-110"
                            title="Chamada de voz"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </button>
                        
                        {{-- Botão Chamada de Vídeo --}}
                        <button 
                            id="video-call-btn"
                            class="p-2 bg-white/10 text-white hover:bg-blue-500 rounded-lg transition-all backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-white hover:scale-110"
                            title="Chamada de vídeo"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </button>
                        
                        {{-- Separador --}}
                        <div class="w-px h-6 bg-white/20 mx-1"></div>
                        
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

            <!-- Call Interface -->
            @include('chat.partials.call-interface')

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
                            class="p-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all hover:scale-105 shadow-lg active:scale-95 transform duration-150 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            title="Enviar mensagem (Enter)"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transition-transform duration-200" id="send-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
    
    @keyframes send-pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
    
    @keyframes rotate-send {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .sending {
        animation: send-pulse 0.6s ease-in-out infinite;
    }
    
    .sending #send-icon {
        animation: rotate-send 0.8s linear infinite;
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
    #local-video {
        transform: scaleX(-1); /* Mirror effect for local video */
    }
    
    /* Mobile responsive adjustments */
    @media (max-width: 640px) {
        #local-video-container {
            width: 100px;
            height: 140px;
            bottom: 100px;
            top: auto;
            right: 16px;
        }
    }
</style>

<script>
@php
    $chatConfig = [
        'roomCode' => $chatRoom->room_code,
        'senderType' => session('chat_user_role', 'client'),
        'apiBaseUrl' => url('/chat'),
    ];
@endphp

const CHAT_CONFIG = @json($chatConfig);

// ========== CONFIGURAÇÃO DO CHAT ==========
let publicChannel = null;
let privateChannel = null;
let isConnected = false;
let typingTimeout = null;

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
    
    // Enviar indicador de digitação
    handleTypingIndicator();
});

// Throttle para indicador de digitação
function handleTypingIndicator() {
    if (typingTimeout) {
        clearTimeout(typingTimeout);
    }
    
    // Enviar que está digitando
    sendTypingIndicator(true);
    
    // Após 2 segundos sem digitar, enviar que parou
    typingTimeout = setTimeout(() => {
        sendTypingIndicator(false);
    }, 2000);
}

// Enviar indicador de digitação
async function sendTypingIndicator(isTyping) {
    try {
        await fetch(`${CHAT_CONFIG.apiBaseUrl}/${CHAT_CONFIG.roomCode}/typing`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({ is_typing: isTyping }),
        });
    } catch (error) {
        console.error('[Chat] Erro ao enviar indicador de digitação:', error);
    }
}

// Scroll para o final
function scrollToBottom() {
    elements.chatMessages.scrollTop = elements.chatMessages.scrollHeight;
}

// Adicionar mensagem ao chat
function addMessageToChat(messageData) {
    const messageDiv = document.createElement('div');
    messageDiv.className = 'mb-4 animate-fade-in';
    messageDiv.setAttribute('data-message-id', messageData.id);
    
    const isClient = messageData.sender_type === 'client';
    
    // Escape HTML para prevenir XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    const safeMessage = messageData.message ? escapeHtml(messageData.message) : '';
    const timeString = messageData.formatted_time || new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    
    let fileHtml = '';
    if (messageData.file_path) {
        const fileUrl = `/storage/${messageData.file_path}`;
        if (messageData.file_type === 'image') {
            fileHtml = `
                <div class="mt-2">
                    <img src="${escapeHtml(fileUrl)}" alt="Imagem anexada" class="max-w-xs rounded-lg cursor-pointer" loading="lazy" onclick="window.open('${escapeHtml(fileUrl)}', '_blank')">
                </div>
            `;
        } else {
            const extension = messageData.file_path.split('.').pop().toUpperCase();
            fileHtml = `
                <div class="mt-2 p-2 bg-black/10 rounded-lg text-sm flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                    </svg>
                    <a href="${escapeHtml(fileUrl)}" target="_blank" rel="noopener noreferrer" class="hover:underline">
                        Ver anexo (${extension})
                    </a>
                </div>
            `;
        }
    }
    
    messageDiv.innerHTML = `
        <div class="flex ${isClient ? 'justify-end' : 'justify-start'}">
            <div class="max-w-xs sm:max-w-md lg:max-w-lg">
                <div class="flex items-end gap-2 ${isClient ? 'flex-row-reverse' : 'flex-row'}">
                    <div class="px-4 py-3 rounded-2xl shadow-sm ${
                        isClient ? 'bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-br-sm' : 
                        'bg-white text-gray-800 border border-gray-200 rounded-bl-sm'
                    }">
                        ${safeMessage ? `<p class="text-sm break-words">${safeMessage}</p>` : ''}
                        ${fileHtml}
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

// ========== WEBSOCKET - LARAVEL ECHO + REVERB (CANAL PÚBLICO) ==========

function initializeChat() {
    // Aguardar Echo estar disponível
    function waitForEcho(callback, maxAttempts = 50) {
        let attempts = 0;
        
        function checkEcho() {
            attempts++;
            
            if (window.Echo && typeof window.Echo.channel === 'function') {
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
        // IMPORTANTE: Usar canal público agora
        const channelName = `chat-room.${roomCode}`;
        
        console.log('[Chat] Conectando ao canal:', channelName);
        console.log('[Chat] Configuração:', {
            roomCode: roomCode,
            senderType: CHAT_CONFIG.senderType,
            channelName: channelName
        });
        
        try {
            // Criar canal público (sem prefixo "private-")
            publicChannel = window.Echo.channel(channelName);
            privateChannel = publicChannel; // Alias para compatibilidade
            
            // Listener para quando o canal é subscrito
            privateChannel.subscribed(() => {
                console.log('[Chat] ✅ Canal subscrito com sucesso!');
                isConnected = true;
                
                // Atualizar indicador de status
                const statusIndicator = document.getElementById('connection-status-indicator');
                if (statusIndicator) {
                    statusIndicator.classList.remove('bg-yellow-400', 'bg-red-400');
                    statusIndicator.classList.add('bg-green-400');
                }
            });
            
            // Listener para erro de subscrição
            privateChannel.error((error) => {
                console.error('[Chat] ❌ Erro ao subscrever canal:', error);
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
                
                alert('Erro ao conectar ao chat. Recarregue a página.');
                isConnected = false;
            });
            
            // Listener para mensagens enviadas via broadcasting
            publicChannel.listen('.message.sent', (data) => {
                console.log('[Chat] 📩 Mensagem recebida via broadcasting:', data);
                console.log('[Chat] Comparando sender_type:', {
                    received: data.sender_type,
                    current: CHAT_CONFIG.senderType,
                    isOwn: data.sender_type === CHAT_CONFIG.senderType
                });
                
                // Ignorar mensagens do próprio usuário (já adicionadas localmente)
                if (data.sender_type === CHAT_CONFIG.senderType) {
                    console.log('[Chat] ⏭️ Mensagem ignorada (próprio usuário)');
                    return;
                }
                
                // Adicionar mensagem ao chat
                console.log('[Chat] ✅ Adicionando mensagem recebida ao chat');
                addMessageToChat(data);
            });
            
            // Listener para indicador de digitação
            publicChannel.listen('.user.typing', (data) => {
                console.log('[Chat] ⌨️ Indicador de digitação:', data);
                
                // Ignorar próprio indicador
                if (data.user_type === CHAT_CONFIG.senderType) {
                    return;
                }
                
                // Mostrar/ocultar indicador
                const otherUserType = CHAT_CONFIG.senderType === 'client' ? 'Prestador' : 'Cliente';
                const typingIndicator = document.getElementById('typing-indicator');
                
                if (data.is_typing) {
                    if (!typingIndicator) {
                        const indicator = document.createElement('div');
                        indicator.id = 'typing-indicator';
                        indicator.className = 'text-sm text-gray-500 italic px-6 py-2';
                        indicator.textContent = `${otherUserType} está digitando...`;
                        elements.chatMessages.appendChild(indicator);
                        scrollToBottom();
                    }
                } else {
                    if (typingIndicator) {
                        typingIndicator.remove();
                    }
                }
            });
            
            // Listener para usuário que entrou
            publicChannel.listen('.user.joined', (data) => {
                console.log('[Chat] 👋 Usuário entrou:', data);
                console.log('[Chat] Comparando tipos:', {
                    received: data.user_type,
                    current: CHAT_CONFIG.senderType,
                    isDifferent: data.user_type !== CHAT_CONFIG.senderType
                });
                
                // Atualizar status do outro usuário APENAS se for diferente do nosso tipo
                if (data.user_type !== CHAT_CONFIG.senderType) {
                    const otherUserStatus = document.getElementById('other-user-status');
                    console.log('[Chat] Atualizando status visual do outro usuário');
                    
                    if (otherUserStatus) {
                        const statusDot = otherUserStatus.querySelector('.w-2.h-2');
                        if (statusDot) {
                            statusDot.classList.remove('bg-gray-400');
                            statusDot.classList.add('bg-green-400');
                            console.log('[Chat] ✅ Status atualizado para verde!');
                        } else {
                            console.warn('[Chat] ⚠️ Elemento statusDot não encontrado');
                        }
                    } else {
                        console.warn('[Chat] ⚠️ Elemento other-user-status não encontrado');
                    }
                } else {
                    console.log('[Chat] ⏭️ Ignorando próprio evento de entrada');
                }
            });
            
            // Listener para sinais de chamada (WebRTC)
            publicChannel.listen('.call.signal', (data) => {
                CallManager.handleSignal(data);
            });
            
            // Inicializar sistema de chamadas
            CallManager.init();

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
            
            console.log('[Chat] ✅ Chat inicializado com sucesso!');
            
        } catch (error) {
            console.error('[Chat] ❌ Erro ao inicializar chat:', error);
            console.error('[Chat] Stack trace:', error.stack);
            alert('Erro ao conectar ao chat. Recarregue a página.');
        }
    });
}

// ========== ENVIAR MENSAGEM VIA API ==========

async function sendMessage() {
    const message = elements.messageInput.value.trim();
    
    if (!message) {
        return;
    }
    
    if (!isConnected) {
        alert('Aguarde a conexão com o chat...');
        return;
    }
    
    // Desabilitar botão durante envio e adicionar animação
    elements.sendButton.disabled = true;
    elements.sendButton.classList.add('opacity-50', 'cursor-not-allowed', 'sending');
    
    try {
        // Enviar via API REST
        const response = await fetch(`${CHAT_CONFIG.apiBaseUrl}/${CHAT_CONFIG.roomCode}/messages`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({
                message: message,
            }),
        });
        
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.message || 'Erro ao enviar mensagem');
        }
        
        console.log('[Chat] ✅ Mensagem enviada via API:', result.data);
        
        // Adicionar mensagem localmente (otimista)
        addMessageToChat(result.data);
        
        // Limpar input
        elements.messageInput.value = '';
        elements.messageInput.style.height = 'auto';
        elements.charCount.textContent = '0/1000';
        
        // Parar indicador de digitação
        if (typingTimeout) {
            clearTimeout(typingTimeout);
        }
        sendTypingIndicator(false);
        
    } catch (error) {
        console.error('[Chat] ❌ Erro ao enviar mensagem:', error);
        alert('Erro ao enviar mensagem: ' + error.message);
    } finally {
        // Reabilitar botão e remover animação
        elements.sendButton.disabled = false;
        elements.sendButton.classList.remove('opacity-50', 'cursor-not-allowed', 'sending');
    }
}

// ========== WEBRTC CALL SYSTEM (PRO) ==========

const SoundManager = {
    audioContext: null,
    oscillator: null,
    gainNode: null,
    
    init() {
        if (!this.audioContext) {
            this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
        }
        if (this.audioContext.state === 'suspended') {
            this.audioContext.resume();
        }
    },
    
    playTone(freq, type, duration, interval = 0) {
        this.init();
        
        const play = () => {
            const osc = this.audioContext.createOscillator();
            const gain = this.audioContext.createGain();
            
            osc.type = type;
            osc.frequency.setValueAtTime(freq, this.audioContext.currentTime);
            
            gain.gain.setValueAtTime(0.1, this.audioContext.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, this.audioContext.currentTime + duration);
            
            osc.connect(gain);
            gain.connect(this.audioContext.destination);
            
            osc.start();
            osc.stop(this.audioContext.currentTime + duration);
        };
        
        play();
        
        if (interval > 0) {
            this.oscillator = setInterval(play, interval);
        }
    },
    
    startRinging() { // Som de telefone tocando (Recebendo)
        // Padrão Brasil: 1s tom, 4s silêncio (aprox)
        this.stop();
        this.playTone(440, 'sine', 1, 3000); 
    },
    
    startCalling() { // Som de chamando (Enviando)
        this.stop();
        this.playTone(400, 'triangle', 0.2, 2000);
    },
    
    playEndCall() { // Som de desligar
        this.stop();
        this.playTone(300, 'sawtooth', 0.2);
        setTimeout(() => this.playTone(200, 'sawtooth', 0.2), 250);
    },
    
    stop() {
        if (this.oscillator) {
            clearInterval(this.oscillator);
            this.oscillator = null;
        }
    }
};

const CallManager = {
    // Config WebRTC - Otimizado para máxima estabilidade
    config: {
        iceServers: [
            // Google STUN servers
            { urls: 'stun:stun.l.google.com:19302' },
            { urls: 'stun:stun1.l.google.com:19302' },
            { urls: 'stun:stun2.l.google.com:19302' },
            { urls: 'stun:stun3.l.google.com:19302' },
            { urls: 'stun:stun4.l.google.com:19302' },
            
            // TURN servers gratuitos (CRÍTICO para NAT travessia)
            // OpenRelay - Serviço TURN gratuito e aberto
            {
                urls: 'turn:openrelay.metered.ca:80',
                username: 'openrelayproject',
                credential: 'openrelayproject'
            },
            {
                urls: 'turn:openrelay.metered.ca:443',
                username: 'openrelayproject',
                credential: 'openrelayproject'
            },
            {
                urls: 'turn:openrelay.metered.ca:443?transport=tcp',
                username: 'openrelayproject',
                credential: 'openrelayproject'
            },
            
            // Metered TURN (gratuito com limite)
            {
                urls: 'stun:stun.relay.metered.ca:80'
            },
            {
                urls: 'turn:global.relay.metered.ca:80',
                username: 'e8dd65f87acd23a0c1ba935d',
                credential: ''
            },
            {
                urls: 'turn:global.relay.metered.ca:80?transport=tcp',
                username: 'e8dd65f87acd23a0c1ba935d',
                credential: ''
            },
            {
                urls: 'turn:global.relay.metered.ca:443',
                username: 'e8dd65f87acd23a0c1ba935d',
                credential: ''
            },
            {
                urls: 'turns:global.relay.metered.ca:443?transport=tcp',
                username: 'e8dd65f87acd23a0c1ba935d',
                credential: ''
            }
        ],
        // Configurações de ICE mais agressivas
        iceCandidatePoolSize: 10, // Pré-gerar candidates
        iceTransportPolicy: 'all', // IMPORTANTE: Aceita relay (TURN) quando necessário
        bundlePolicy: 'max-bundle', // Agrupar mídia em uma conexão
        rtcpMuxPolicy: 'require', // Multiplexar RTCP
        
        callTimeout: 60000, // 60 segundos para atender (mais tempo)
        connectionTimeout: 45000, // 45 segundos para estabelecer (TURN pode demorar mais)
        reconnectDelay: 1500, // Delay inicial de reconexão
        maxReconnectAttempts: 5, // Mais tentativas
        reconnectBackoffMultiplier: 1.5, // Exponential backoff
        
        // Retry de sinalização
        signalRetryAttempts: 3,
        signalRetryDelay: 1000,
        
        // Heartbeat
        heartbeatInterval: 5000, // Verificar conexão a cada 5s
        heartbeatTimeout: 15000 // 15s sem resposta = reconectar
    },
    
    // Estado
    state: {
        localStream: null,
        remoteStream: null,
        peerConnection: null,
        isCallActive: false,
        isAudioMuted: false,
        isVideoOff: false,
        isScreenSharing: false,
        callType: 'voice',
        startTime: null,
        timerInterval: null,
        statsInterval: null,
        isInitiator: false,
        isFrontCamera: true,
        // Campos enterprise
        callTimeoutId: null,
        connectionTimeoutId: null,
        iceCandidateQueue: [],
        reconnectAttempts: 0,
        connectionState: 'idle', // idle, calling, ringing, connecting, connected, reconnecting
        // Novos campos para estabilidade
        heartbeatInterval: null,
        lastHeartbeat: null,
        bytesReceived: 0,
        lastBytesReceived: 0,
        consecutiveFailures: 0,
        isReconnecting: false,
        signalRetryQueue: [] // Para retry de sinalizações
    },
    
    // Elementos UI
    elements: {
        voiceBtn: document.getElementById('voice-call-btn'),
        videoBtn: document.getElementById('video-call-btn'),
        
        incomingModal: document.getElementById('incoming-call-modal'),
        outgoingModal: document.getElementById('outgoing-call-modal'),
        callInterface: document.getElementById('call-interface'),
        
        localVideo: document.getElementById('local-video'),
        remoteVideo: document.getElementById('remote-video'),
        
        minimizedCall: document.getElementById('minimized-call'),
        minimizedVideo: document.getElementById('minimized-video'),
        
        acceptBtn: document.getElementById('accept-call-btn'),
        rejectBtn: document.getElementById('reject-call-btn'),
        cancelBtn: document.getElementById('cancel-call-btn'),
        endBtn: document.getElementById('end-call-btn'),
        
        micBtn: document.getElementById('toggle-mic-btn'),
        cameraBtn: document.getElementById('toggle-camera-btn'),
        screenBtn: document.getElementById('screen-share-btn'),
        minimizeBtn: document.getElementById('minimize-call-btn'),
        flipBtn: document.getElementById('flip-camera-btn'),
        
        callerName: document.getElementById('caller-name'),
        callTypeLabel: document.getElementById('call-type-label'),
        callDuration: document.getElementById('call-duration'),
        qualityIndicator: document.getElementById('quality-indicator')
    },
    
    // Inicializar
    init() {
        this.addEventListeners();
        console.log('[CallManager] Inicializado (Pro Version)');
    },
    
    // Listeners
    addEventListeners() {
        this.elements.voiceBtn?.addEventListener('click', () => this.startCall('voice'));
        this.elements.videoBtn?.addEventListener('click', () => this.startCall('video'));
        
        this.elements.acceptBtn?.addEventListener('click', () => this.acceptCall());
        this.elements.rejectBtn?.addEventListener('click', () => this.rejectCall());
        this.elements.cancelBtn?.addEventListener('click', () => this.cancelCall());
        this.elements.endBtn?.addEventListener('click', () => this.endCall());
        
        this.elements.micBtn?.addEventListener('click', () => this.toggleMic());
        this.elements.cameraBtn?.addEventListener('click', () => this.toggleCamera());
        this.elements.screenBtn?.addEventListener('click', () => this.toggleScreenShare());
        
        this.elements.minimizeBtn?.addEventListener('click', () => this.toggleMinimize());
        this.elements.flipBtn?.addEventListener('click', () => this.flipCamera());
    },
    
    // Sinalização com Retry Automático
    async sendSignal(type, data = null, attempt = 1) {
        try {
            const response = await fetch(`${CHAT_CONFIG.apiBaseUrl}/${CHAT_CONFIG.roomCode}/call-signal`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({
                    signal_type: type,
                    signal_data: data
                })
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.message || 'Sinal falhou');
            }
            
            // Sucesso! Resetar contadores
            this.state.consecutiveFailures = 0;
            console.log(`[CallManager] ✓ Sinal enviado: ${type} (tentativa ${attempt})`);
            return true;
            
        } catch (error) {
            console.error(`[CallManager] ✗ Erro ao enviar sinal ${type} (tentativa ${attempt}):`, error);
            
            // Tentar novamente se não atingiu o máximo
            if (attempt < this.config.signalRetryAttempts) {
                const delay = this.config.signalRetryDelay * Math.pow(1.5, attempt - 1);
                console.log(`[CallManager] ↻ Tentando novamente em ${delay}ms...`);
                
                await this.sleep(delay);
                return this.sendSignal(type, data, attempt + 1);
            }
            
            // Falhou após todas as tentativas
            this.state.consecutiveFailures++;
            console.error(`[CallManager] ✗✗✗ Falha permanente ao enviar ${type} após ${attempt} tentativas`);
            
            // Se muitas falhas consecutivas, pode ser problema de rede
            if (this.state.consecutiveFailures >= 3) {
                this.showToast('Problemas de conexão detectados');
            }
            
            return false;
        }
    },
    
    sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    },
    
    // Handler de eventos
    handleSignal(data) {
        if (data.sender_type === CHAT_CONFIG.senderType) return;
        
        console.log('[CallManager] Sinal:', data.signal_type);
        
        switch (data.signal_type) {
            case 'call-start':
                this.handleIncomingCall(data.signal_data);
                break;
            case 'call-end':
            case 'call-reject':
            case 'call-cancel':
                this.handleCallEnded(data.signal_type);
                break;
            case 'offer':
                this.handleOffer(data.signal_data);
                break;
            case 'answer':
                this.handleAnswer(data.signal_data);
                break;
            case 'ice-candidate':
                this.handleIceCandidate(data.signal_data);
                break;
            case 'call-busy':
                this.handleCallBusy();
                break;
            case 'call-accept':
                // Outro lado aceitou a chamada! 
                this.handleCallAccepted();
                break;
        }
    },
    
    // Handler quando outro lado aceita a chamada
    // Handler quando outro lado aceita a chamada (Via WebSocket)
    handleCallAccepted() {
        console.log('[CallManager] Chamada aceita! Mudando interface imediatamente...');
        
        // 1. Limpar timeout de "não atendida"
        this.clearCallTimeout();
        
        // 2. Parar som de "chamando"
        SoundManager.stop();
        
        // 3. Esconder modal "Chamando..."
        this.elements.outgoingModal.classList.add('hidden');
        
        // 4. Mostrar interface de chamada ativa
        this.showCallInterface();
        
        // 5. Feedback visual de conexão
        this.state.connectionState = 'connecting';
        this.showToast('Conectando áudio e vídeo...');
        
        // A conexão WebRTC (Offer/Answer) continua em background
    },
    
    // Processar ICE candidate recebido (com queue)
    async handleIceCandidate(candidate) {
        if (!candidate) return;
        
        // Se peer não existe ou não tem remote description, guardar na queue
        if (!this.state.peerConnection || !this.state.peerConnection.remoteDescription) {
            this.state.iceCandidateQueue.push(candidate);
            console.log('[CallManager] ICE candidate enfileirado, aguardando peer');
            return;
        }
        
        try {
            // Usar objeto simples diretamente - browser aceita
            await this.state.peerConnection.addIceCandidate(candidate);
        } catch (error) {
            console.error('[CallManager] Erro ao adicionar ICE candidate:', error);
        }
    },
    
    // Processar queue de ICE candidates
    async processIceCandidateQueue() {
        console.log(`[CallManager] Processando ${this.state.iceCandidateQueue.length} ICE candidates da queue`);
        while (this.state.iceCandidateQueue.length > 0) {
            const candidate = this.state.iceCandidateQueue.shift();
            try {
                await this.state.peerConnection.addIceCandidate(candidate);
            } catch (e) {
                console.error('[CallManager] Erro ao processar ICE da queue:', e);
            }
        }
    },
    
    // Handler para quando outro usuário está ocupado
    handleCallBusy() {
        SoundManager.stop();
        this.clearCallTimeout();
        this.showToast('Usuário ocupado em outra chamada');
        this.resetCall();
    },
    
    // Timeout management
    startCallTimeout() {
        this.clearCallTimeout();
        this.state.callTimeoutId = setTimeout(() => {
            console.log('[CallManager] Timeout - chamada não atendida');
            this.showToast('Chamada não atendida');
            this.cancelCall();
        }, this.config.callTimeout);
    },
    
    clearCallTimeout() {
        if (this.state.callTimeoutId) {
            clearTimeout(this.state.callTimeoutId);
            this.state.callTimeoutId = null;
        }
    },
    
    // Toast helper
    showToast(msg, duration = 3000) {
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-gray-800 text-white px-6 py-3 rounded-xl shadow-2xl z-[60] animate-fade-in';
        toast.textContent = msg;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), duration);
    },
    
    // Cancelar (Quem ligou desiste)
    cancelCall() {
        this.clearCallTimeout();
        this.sendSignal('call-cancel');
        this.resetCall();
    },
    
    // Receber Chamada (Incoming)
    handleIncomingCall(data) {
        // Se já está em chamada, enviar sinal de ocupado
        if (this.state.isCallActive) {
            this.sendSignal('call-busy');
            return;
        }
        
        this.state.callType = data.type;
        this.elements.callTypeLabel.textContent = data.type === 'video' ? 'Chamada de Vídeo' : 'Chamada de Voz';
        
        // UI Incoming
        this.elements.incomingModal.classList.remove('hidden');
        SoundManager.startRinging();
        
        // Vibração
        if (navigator.vibrate) {
            navigator.vibrate([200, 100, 200, 100, 500, 100, 200, 100, 200]);
        }
    },
    
    // Estado para offer pendente (quando recebemos offer antes de aceitar)
    pendingOffer: null,
    
    // Handler para offer recebido
    async handleOffer(offer) {
        // Se modal de chamada recebida ainda visível, guardar offer para processar depois
        if (!this.elements.incomingModal.classList.contains('hidden')) {
            this.pendingOffer = offer;
            console.log('[CallManager] Offer guardado, aguardando aceite');
            return;
        }
        
        // Se já aceitou (interface visível), processar imediatamente
        await this.processOffer(offer);
    },
    
    // Processar offer (criar answer)
    async processOffer(offer) {
        if (!this.state.peerConnection) {
            console.error('[CallManager] PeerConnection não existe para processar offer');
            return;
        }
        
        try {
            console.log('[CallManager] Processando offer...');
            
            // Decodificar Base64 se necessário
            let sdp = offer.sdp;
            // Verificar simples se parece Base64 (sem espaços, == no final, etc) ou se contém "v=0"
            if (!offer.sdp.includes('v=0')) {
                 try { sdp = atob(offer.sdp); } catch(e) { console.log('SDP não era base64'); }
            }
            
            const offerData = new RTCSessionDescription({
                type: offer.type,
                sdp: sdp
            });
            
            await this.state.peerConnection.setRemoteDescription(offerData);
            console.log('[CallManager] Remote description setada');
            
            // Criar answer
            const answer = await this.state.peerConnection.createAnswer();
            await this.state.peerConnection.setLocalDescription(answer);
            console.log('[CallManager] Local description setada');
            
            // Enviar answer Base64
            const answerData = {
                type: answer.type,
                sdp: btoa(answer.sdp)
            };
            this.sendSignal('answer', answerData);
            console.log('[CallManager] Answer enviado (Base64)');
            
            // Processar ICE candidates pendentes
            await this.processIceCandidateQueue();
            
        } catch (error) {
            console.error('[CallManager] Erro ao processar offer:', error);
            console.error('[CallManager] Offer recebido:', JSON.stringify(offer).substring(0, 200));
            this.showToast('Erro na conexão');
        }
    },
    
    async startCall(type) {
        // Verificar se dispositivos estão disponíveis
        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            const hasAudio = devices.some(d => d.kind === 'audioinput');
            const hasVideo = devices.some(d => d.kind === 'videoinput');
            
            if (!hasAudio) {
                this.showToast('Nenhum microfone encontrado');
                return;
            }
            if (type === 'video' && !hasVideo) {
                this.showToast('Nenhuma câmera encontrada');
                return;
            }
        } catch (e) {
            console.warn('[CallManager] Erro ao verificar dispositivos:', e);
        }
        
        this.state.callType = type;
        this.state.isInitiator = true;
        this.state.connectionState = 'calling';
        
        this.elements.outgoingModal.classList.remove('hidden');
        SoundManager.startCalling();
        
        // Iniciar timeout
        this.startCallTimeout();
        
        try {
            const constraints = {
                audio: { echoCancellation: true, noiseSuppression: true },
                video: type === 'video' ? { 
                    facingMode: 'user',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                } : false
            };
            this.state.localStream = await navigator.mediaDevices.getUserMedia(constraints);
            this.elements.localVideo.srcObject = this.state.localStream;
            
            console.log('[CallManager] 📹 Stream local obtido:', {
                audio: this.state.localStream.getAudioTracks().length,
                video: this.state.localStream.getVideoTracks().length
            });
            
            // Iniciar Peer
            this.createPeerConnection();
            
            // Adicionar tracks ao PeerConnection
            this.state.localStream.getTracks().forEach(track => {
                const sender = this.state.peerConnection.addTrack(track, this.state.localStream);
                console.log('[CallManager] ➕ Track local adicionado:', {
                    kind: track.kind,
                    enabled: track.enabled,
                    readyState: track.readyState
                });
            });
            
            const offer = await this.state.peerConnection.createOffer();
            await this.state.peerConnection.setLocalDescription(offer);
            
            console.log('[CallManager] 📤 Offer criado com', 
                offer.sdp.match(/m=(audio|video)/g)?.length || 0, 
                'media lines'
            );
            
            // Serializar offer com Base64 para proteger SDP
            const offerData = {
                type: offer.type,
                sdp: btoa(offer.sdp) // Codificar Base64
            };
            
            this.sendSignal('call-start', { type: type });
            this.sendSignal('offer', offerData);
            console.log('[CallManager] ✓ Offer enviado (Base64)');
            
        } catch (error) {
            console.error('[CallManager] Erro ao iniciar chamada:', error);
            this.clearCallTimeout();
            
            // Mensagens de erro específicas
            if (error.name === 'NotAllowedError') {
                this.showToast('Permissão de mídia negada. Verifique as configurações do navegador.');
            } else if (error.name === 'NotFoundError') {
                this.showToast('Dispositivo de mídia não encontrado.');
            } else {
                this.showToast('Erro ao acessar dispositivos.');
            }
            this.resetCall();
        }
    },
    
    async acceptCall() {
        SoundManager.stop();
        if (navigator.vibrate) navigator.vibrate(0);
        
        // Notificar quem ligou que aceitamos
        this.sendSignal('call-accept');
        
        this.elements.incomingModal.classList.add('hidden');
        this.state.connectionState = 'connecting';
        this.showCallInterface(); 
        
        try {
            const constraints = {
                audio: { echoCancellation: true, noiseSuppression: true },
                video: this.state.callType === 'video' ? { 
                    facingMode: 'user',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                } : false
            };
            
            this.state.localStream = await navigator.mediaDevices.getUserMedia(constraints);
            this.elements.localVideo.srcObject = this.state.localStream;
            
            console.log('[CallManager] 📹 Stream local obtido (accept):', {
                audio: this.state.localStream.getAudioTracks().length,
                video: this.state.localStream.getVideoTracks().length
            });
            
            // Se já tem peer (de tentativas anteriores), fecha
            if (this.state.peerConnection) {
                this.state.peerConnection.close();
                this.state.peerConnection = null;
            }
            
            this.createPeerConnection();
            console.log('[CallManager] ✓ PeerConnection criado');
            
            // Adicionar tracks ao PeerConnection
            this.state.localStream.getTracks().forEach(track => {
                this.state.peerConnection.addTrack(track, this.state.localStream);
                console.log('[CallManager] ➕ Track local adicionado:', {
                    kind: track.kind,
                    enabled: track.enabled,
                    readyState: track.readyState
                });
            });
            
            // Se tem offer guardado, processar agora
            if (this.pendingOffer) {
                console.log('[CallManager] 📥 Processando offer pendente...');
                await this.processOffer(this.pendingOffer);
                this.pendingOffer = null;
            } else {
                console.log('[CallManager] ⏳ Sem offer pendente, aguardando...');
            }
            
        } catch (error) {
            console.error('[CallManager] Erro ao aceitar:', error);
            this.showToast('Erro ao acessar dispositivos');
            this.endCall(false);
        }
    },
    
    async handleAnswer(answer) {
        console.log('[CallManager] Answer (sinal técnico) recebido!');
        
        // Garantir que UI esteja correta (caso handleCallAccepted não tenha disparado por algum motivo)
        this.clearCallTimeout();
        this.elements.outgoingModal.classList.add('hidden');
        SoundManager.stop();
        this.showCallInterface();
        
        this.state.connectionState = 'connected';
        
        if (!this.state.peerConnection) {
            console.error('[CallManager] PeerConnection não existe para processar answer');
            return;
        }
        
        try {
            // Decodificar Base64 se necessário
            let sdp = answer.sdp;
            if (!answer.sdp.includes('v=0')) {
                 try { sdp = atob(answer.sdp); } catch(e) { console.log('SDP (answer) não era base64'); }
            }
            
            const answerData = new RTCSessionDescription({
                type: answer.type,
                sdp: sdp
            });

            await this.state.peerConnection.setRemoteDescription(answerData);
            console.log('[CallManager] Conexão estabelecida! (Remote description setada)');
            
            // Processar ICE candidates que chegaram antes
            await this.processIceCandidateQueue();
        } catch (e) { 
            console.error('[CallManager] Erro ao processar answer:', e);
            console.error('[CallManager] Answer recebido:', JSON.stringify(answer).substring(0, 200));
            this.showToast('Erro na conexão da chamada');
        }
    },
    
    // Rejeitar
    rejectCall() {
        SoundManager.stop();
        if (navigator.vibrate) navigator.vibrate(0);
        this.elements.incomingModal.classList.add('hidden');
        this.sendSignal('call-reject');
    },
    
    // Encerrar
    endCall(notifyRemote = true) {
        SoundManager.playEndCall();
        if (notifyRemote) this.sendSignal('call-end');
        this.resetCall();
    },
    
    handleCallEnded(type) {
        SoundManager.playEndCall();
        if (navigator.vibrate) navigator.vibrate(0);
        
        let msg = 'Chamada encerrada';
        if (type === 'call-reject') msg = 'Chamada recusada';
        if (type === 'call-cancel') msg = 'Chamada cancelada';
        
        this.showToast(msg);
        this.resetCall();
    },
    
    resetCall() {
        console.log('[CallManager] 🧹 Limpando chamada...');
        SoundManager.stop();
        this.clearCallTimeout();
        this.clearConnectionTimeout();
        this.stopHeartbeat();
        
        // Parar tracks
        if (this.state.localStream) {
            this.state.localStream.getTracks().forEach(track => {
                track.stop();
                console.log('[CallManager] ⏹ Track parado:', track.kind);
            });
        }
        
        if (this.state.peerConnection) {
            this.state.peerConnection.close();
            console.log('[CallManager] 🔌 PeerConnection fechada');
        }
        
        // Limpar vídeos
        if (this.elements.localVideo) this.elements.localVideo.srcObject = null;
        if (this.elements.remoteVideo) this.elements.remoteVideo.srcObject = null;
        
        // Esconder UI
        this.elements.callInterface?.classList.add('hidden');
        this.elements.minimizedCall?.classList.add('hidden');
        this.elements.incomingModal?.classList.add('hidden');
        this.elements.outgoingModal?.classList.add('hidden');
        
        // Resetar Variáveis
        this.state.localStream = null;
        this.state.remoteStream = null;
        this.state.peerConnection = null;
        this.state.isCallActive = false;
        this.state.isScreenSharing = false;
        this.state.isAudioMuted = false;
        this.state.isVideoOff = false;
        this.state.connectionState = 'idle';
        this.state.iceCandidateQueue = [];
        this.state.reconnectAttempts = 0;
        this.state.isReconnecting = false;
        this.state.consecutiveFailures = 0;
        this.state.bytesReceived = 0;
        this.state.lastBytesReceived = 0;
        this.state.lastHeartbeat = null;
        this.pendingOffer = null;
        
        if (this.state.timerInterval) clearInterval(this.state.timerInterval);
        if (this.state.statsInterval) clearInterval(this.state.statsInterval);
        this.state.timerInterval = null;
        this.state.statsInterval = null;
        
        console.log('[CallManager] ✓ Chamada limpa');
    },
    
    // Compartilhar Tela
    async toggleScreenShare() {
        if (!this.state.isCallActive) return;
        
        if (this.state.isScreenSharing) {
            // Parar tela, voltar câmera
            try {
                const constraints = { video: { facingMode: 'user' } };
                const newStream = await navigator.mediaDevices.getUserMedia(constraints);
                this.replaceVideoTrack(newStream.getVideoTracks()[0]);
                
                this.state.isScreenSharing = false;
                this.updateScreenBtnUI(false);
                
            } catch (e) {
                console.error('Erro voltar camera:', e);
            }
        } else {
            // Compartilhar tela
            try {
                const screenStream = await navigator.mediaDevices.getDisplayMedia({ video: true });
                const screenTrack = screenStream.getVideoTracks()[0];
                
                // Se usuário clicar em "Parar compartihamento" do navegador
                screenTrack.onended = () => {
                    if (this.state.isScreenSharing) this.toggleScreenShare();
                };
                
                this.replaceVideoTrack(screenTrack);
                this.state.isScreenSharing = true;
                this.updateScreenBtnUI(true);
                
            } catch (e) {
                console.error('Erro share screen:', e); // Usuario cancelou
            }
        }
    },
    
    replaceVideoTrack(newTrack) {
        if (!this.state.peerConnection || !this.state.localStream) return;
        
        const sender = this.state.peerConnection.getSenders().find(s => s.track && s.track.kind === 'video');
        if (sender) {
            sender.replaceTrack(newTrack);
            const oldTrack = this.state.localStream.getVideoTracks()[0];
            if (oldTrack) this.state.localStream.removeTrack(oldTrack);
            this.state.localStream.addTrack(newTrack);
            if (this.elements.localVideo) {
                this.elements.localVideo.srcObject = this.state.localStream;
            }
        }
    },
    
    updateScreenBtnUI(isSharing) {
         if (isSharing) {
             document.getElementById('screen-off-icon').classList.add('hidden');
             document.getElementById('screen-on-icon').classList.remove('hidden');
             this.elements.screenBtn.classList.add('bg-white/30');
         } else {
             document.getElementById('screen-off-icon').classList.remove('hidden');
             document.getElementById('screen-on-icon').classList.add('hidden');
             this.elements.screenBtn.classList.remove('bg-white/30');
         }
    },

    // Peer Connection Enterprise - Máxima Estabilidade
    createPeerConnection() {
        // Criar conexão com configurações otimizadas
        const rtcConfig = {
            iceServers: this.config.iceServers,
            iceCandidatePoolSize: this.config.iceCandidatePoolSize,
            iceTransportPolicy: this.config.iceTransportPolicy,
            bundlePolicy: this.config.bundlePolicy,
            rtcpMuxPolicy: this.config.rtcpMuxPolicy
        };
        
        this.state.peerConnection = new RTCPeerConnection(rtcConfig);
        this.state.isCallActive = true;
        this.startTimer();
        this.startStats();
        this.startHeartbeat();
        this.startConnectionTimeout();
        
        console.log('[CallManager] 🔗 PeerConnection criada com config otimizada');
        
        // NOTA: NÃO adicionar transceivers manualmente aqui!
        // O addTrack() cria transceivers automaticamente com a direção correta (sendrecv)
        // Criar manualmente ANTES dos tracks causa conflitos de direção
        
        // ICE candidate - serializar corretamente e mostrar tipo
        this.state.peerConnection.onicecandidate = (event) => {
            if (event.candidate) {
                const candidateStr = event.candidate.candidate;
                
                // Identificar tipo de candidate
                let candidateType = 'unknown';
                if (candidateStr.includes('typ host')) candidateType = '🏠 host (local)';
                else if (candidateStr.includes('typ srflx')) candidateType = '🌐 srflx (STUN)';
                else if (candidateStr.includes('typ relay')) candidateType = '🔄 relay (TURN)';
                else if (candidateStr.includes('typ prflx')) candidateType = '🔀 prflx (peer-reflexive)';
                
                // Serializar candidate como objeto simples
                const candidateData = {
                    candidate: event.candidate.candidate,
                    sdpMid: event.candidate.sdpMid,
                    sdpMLineIndex: event.candidate.sdpMLineIndex
                };
                
                console.log(`[CallManager] 🧊 ICE candidate [${candidateType}]:`, candidateStr.substring(0, 60) + '...');
                this.sendSignal('ice-candidate', candidateData);
            }
        };
        
        // ICE gathering state
        this.state.peerConnection.onicegatheringstatechange = () => {
            const state = this.state.peerConnection.iceGatheringState;
            console.log('[CallManager] 🔍 ICE Gathering State:', state);
            
            if (state === 'complete') {
                console.log('[CallManager] ✓ Todos os ICE candidates coletados');
            }
        };
        
        // Remote track - com logging detalhado
        this.state.peerConnection.ontrack = (event) => {
            const track = event.track;
            const stream = event.streams[0];
            
            console.log('[CallManager] 🎵 Track remoto recebido:', {
                kind: track.kind,
                id: track.id,
                enabled: track.enabled,
                muted: track.muted,
                readyState: track.readyState
            });
            
            // Verificar se track está ativo
            if (track.readyState !== 'live') {
                console.warn('[CallManager] ⚠️ Track remoto NÃO está live:', track.readyState);
            }
            
            if (!track.enabled) {
                console.warn('[CallManager] ⚠️ Track remoto está DESABILITADO');
            }
            
            this.state.remoteStream = stream;
            
            // Garantir que vídeo remoto está associado ao stream
            if (this.elements.remoteVideo) {
                this.elements.remoteVideo.srcObject = stream;
                console.log('[CallManager] ✓ Stream remoto associado ao vídeo element');
                
                // Forçar play (alguns browsers exigem)
                this.elements.remoteVideo.play().catch(e => {
                    console.warn('[CallManager] Erro ao dar play no vídeo remoto:', e.name);
                });
            }
            
            if (this.elements.minimizedVideo) {
                this.elements.minimizedVideo.srcObject = stream;
            }
            
            // Limpar timeout de conexão quando receber track
            this.clearConnectionTimeout();
        };
        
        // ICE connection state (mais confiável para detectar problemas)
        this.state.peerConnection.oniceconnectionstatechange = async () => {
            const state = this.state.peerConnection.iceConnectionState;
            console.log('[CallManager] 🌐 ICE Connection State:', state);
            
            if (state === 'connected' || state === 'completed') {
                this.state.connectionState = 'connected';
                this.state.reconnectAttempts = 0;
                this.state.isReconnecting = false;
                this.clearConnectionTimeout();
                console.log('[CallManager] ✓✓✓ Conexão estabelecida com sucesso!');
                
                // Mostrar qual tipo de candidate foi usado
                this.logSelectedCandidatePair();
                this.showToast('Conectado!', 2000);
                
            } else if (state === 'disconnected') {
                this.state.connectionState = 'reconnecting';
                console.log('[CallManager] ⚠ Desconectado, tentando reconectar...');
                
                if (!this.state.isReconnecting) {
                    this.showToast('Reconectando...');
                    this.attemptReconnect();
                }
                
            } else if (state === 'failed') {
                console.error('[CallManager] ✗ Conexão falhou!');
                
                // Diagnóstico: mostrar candidates disponíveis
                await this.diagnoseConnectionFailure();
                
                if (this.state.reconnectAttempts < this.config.maxReconnectAttempts) {
                    this.attemptReconnect();
                } else {
                    this.showToast('Conexão perdida. Tente novamente.');
                    setTimeout(() => this.endCall(true), 2000);
                }
                
            } else if (state === 'checking') {
                console.log('[CallManager] 🔄 Negociando conexão...');
            }
        };
        
        // Connection state (backup)
        this.state.peerConnection.onconnectionstatechange = () => {
            const state = this.state.peerConnection.connectionState;
            console.log('[CallManager] 🔌 Connection State:', state);
            
            if (state === 'connected') {
                this.state.connectionState = 'connected';
            } else if (state === 'failed') {
                console.error('[CallManager] ✗ Connection State falhou');
            }
        };
        
        // Signaling state (para debug)
        this.state.peerConnection.onsignalingstatechange = () => {
            const state = this.state.peerConnection.signalingState;
            console.log('[CallManager] 📶 Signaling State:', state);
        };
    },
    
    // Timeout para estabelecer conexão
    startConnectionTimeout() {
        this.clearConnectionTimeout();
        this.state.connectionTimeoutId = setTimeout(() => {
            if (this.state.connectionState !== 'connected') {
                console.error('[CallManager] ⏱ Timeout ao estabelecer conexão');
                this.showToast('Não foi possível conectar. Tentando novamente...');
                this.attemptReconnect();
            }
        }, this.config.connectionTimeout);
    },
    
    clearConnectionTimeout() {
        if (this.state.connectionTimeoutId) {
            clearTimeout(this.state.connectionTimeoutId);
            this.state.connectionTimeoutId = null;
        }
    },
    
    // Heartbeat para detectar conexões "mortas"
    startHeartbeat() {
        this.stopHeartbeat();
        
        this.state.heartbeatInterval = setInterval(async () => {
            if (!this.state.peerConnection || !this.state.isCallActive) return;
            
            try {
                // Obter estatísticas de bytes recebidos
                const stats = await this.state.peerConnection.getStats();
                let currentBytesReceived = 0;
                
                stats.forEach(report => {
                    if (report.type === 'inbound-rtp' && report.bytesReceived) {
                        currentBytesReceived += report.bytesReceived;
                    }
                });
                
                // Se não recebeu nenhum byte novo, conexão pode estar morta
                if (currentBytesReceived > 0) {
                    if (currentBytesReceived === this.state.lastBytesReceived) {
                        const timeSinceLastData = Date.now() - (this.state.lastHeartbeat || Date.now());
                        
                        if (timeSinceLastData > this.config.heartbeatTimeout) {
                            console.warn('[CallManager] 💀 Conexão parece morta (sem dados)', timeSinceLastData + 'ms');
                            this.showToast('Conexão instável, reconectando...');
                            this.attemptReconnect();
                        }
                    } else {
                        // Recebendo dados, atualizar heartbeat
                        this.state.lastHeartbeat = Date.now();
                        this.state.lastBytesReceived = currentBytesReceived;
                    }
                }
                
            } catch (e) {
                console.error('[CallManager] Erro no heartbeat:', e);
            }
        }, this.config.heartbeatInterval);
    },
    
    stopHeartbeat() {
        if (this.state.heartbeatInterval) {
            clearInterval(this.state.heartbeatInterval);
            this.state.heartbeatInterval = null;
        }
    },
    
    // Diagnóstico: Mostrar par de candidates selecionado
    async logSelectedCandidatePair() {
        try {
            const stats = await this.state.peerConnection.getStats();
            stats.forEach(report => {
                if (report.type === 'candidate-pair' && report.nominated && report.state === 'succeeded') {
                    console.log('[CallManager] 🎯 Par de candidates SELECIONADO:', {
                        local: report.localCandidateId,
                        remote: report.remoteCandidateId,
                        state: report.state,
                        bytesReceived: report.bytesReceived,
                        bytesSent: report.bytesSent
                    });
                    
                    // Mostrar detalhes dos candidates locais e remotos
                    stats.forEach(r => {
                        if (r.id === report.localCandidateId) {
                            let type = '';
                            if (r.candidateType === 'host') type = '🏠 host (local)';
                            else if (r.candidateType === 'srflx') type = '🌐 srflx (STUN)';
                            else if (r.candidateType === 'relay') type = '🔄 relay (TURN)';
                            console.log(`[CallManager] └─ Local: ${type} - ${r.ip || r.address}:${r.port}`);
                        }
                        if (r.id === report.remoteCandidateId) {
                            let type = '';
                            if (r.candidateType === 'host') type = '🏠 host (local)';
                            else if (r.candidateType === 'srflx') type = '🌐 srflx (STUN)';
                            else if (r.candidateType === 'relay') type = '🔄 relay (TURN)';
                            console.log(`[CallManager] └─ Remote: ${type} - ${r.ip || r.address}:${r.port}`);
                        }
                    });
                }
            });
        } catch (e) {
            console.error('[CallManager] Erro ao obter par de candidates:', e);
        }
    },
    
    // Diagnóstico: Quando conexão falha
    async diagnoseConnectionFailure() {
        console.log('[CallManager] 🔍 === DIAGNÓSTICO DE FALHA DE CONEXÃO ===');
        
        try {
            const stats = await this.state.peerConnection.getStats();
            const candidates = { host: 0, srflx: 0, relay: 0 };
            
            stats.forEach(report => {
                if (report.type === 'local-candidate') {
                    if (report.candidateType === 'host') candidates.host++;
                    else if (report.candidateType === 'srflx') candidates.srflx++;
                    else if (report.candidateType === 'relay') candidates.relay++;
                }
            });
            
            console.log('[CallManager] 📊 Candidates locais disponíveis:', candidates);
            
            if (candidates.relay === 0) {
                console.warn('[CallManager] ⚠️ NENHUM candidate TURN (relay) gerado!');
                console.warn('[CallManager] → Possível problema: Servidores TURN indisponíveis ou bloqueados');
                console.warn('[CallManager] → Solução: Verificar firewall ou usar servidor TURN próprio');
            }
            
            if (candidates.srflx === 0 && candidates.relay === 0) {
                console.error('[CallManager] ✗✗ Apenas candidates locais (host) disponíveis!');
                console.error('[CallManager] → Isso indica NAT simétrico ou firewall muito restritivo');
                console.error('[CallManager] → É IMPOSSÍVEL conectar sem TURN neste cenário');
            }
            
        } catch (e) {
            console.error('[CallManager] Erro no diagnóstico:', e);
        }
        
        console.log('[CallManager] 🔍 === FIM DO DIAGNÓSTICO ===');
    },
    
    // Tentativa de reconexão com Exponential Backoff
    async attemptReconnect() {
        // Evitar múltiplas reconexões simultâneas
        if (this.state.isReconnecting) {
            console.log('[CallManager] ↻ Reconexão já em andamento, ignorando...');
            return;
        }
        
        if (this.state.reconnectAttempts >= this.config.maxReconnectAttempts) {
            console.error('[CallManager] ✗✗✗ Máximo de tentativas de reconexão atingido');
            this.showToast('Não foi possível restabelecer a conexão');
            setTimeout(() => this.endCall(true), 2000);
            return;
        }
        
        this.state.isReconnecting = true;
        this.state.reconnectAttempts++;
        
        // Exponential backoff
        const delay = this.config.reconnectDelay * Math.pow(
            this.config.reconnectBackoffMultiplier, 
            this.state.reconnectAttempts - 1
        );
        
        console.log(`[CallManager] 🔄 Tentativa de reconexão ${this.state.reconnectAttempts}/${this.config.maxReconnectAttempts} em ${delay}ms`);
        
        await this.sleep(delay);
        
        // ICE restart (apenas se for o initiator)
        if (this.state.isInitiator && this.state.peerConnection) {
            try {
                console.log('[CallManager] 🔄 Iniciando ICE restart...');
                const offer = await this.state.peerConnection.createOffer({ iceRestart: true });
                await this.state.peerConnection.setLocalDescription(offer);
                const offerData = { type: offer.type, sdp: btoa(offer.sdp) };
                
                const success = await this.sendSignal('offer', offerData);
                
                if (success) {
                    console.log('[CallManager] ✓ ICE restart iniciado com sucesso');
                } else {
                    console.error('[CallManager] ✗ Falha ao enviar offer de ICE restart');
                    this.state.isReconnecting = false;
                    // Tentar novamente
                    setTimeout(() => this.attemptReconnect(), 1000);
                }
                
            } catch (e) {
                console.error('[CallManager] ✗ Erro ao tentar ICE restart:', e);
                this.state.isReconnecting = false;
                // Tentar novamente
                setTimeout(() => this.attemptReconnect(), 1000);
            }
        } else {
            // Se não é o initiator, apenas aguardar o outro lado
            console.log('[CallManager] ⏳ Aguardando reconnect do initiator...');
            this.state.isReconnecting = false;
        }
    },
    
    // Monitorar Qualidade
    startStats() {
        this.state.statsInterval = setInterval(() => {
            if (!this.state.peerConnection) return;
            
            const state = this.state.peerConnection.iceConnectionState;
            const indicator = this.elements.qualityIndicator;
            
            if (state === 'connected' || state === 'completed') {
                indicator.className = 'w-2 h-2 bg-green-400 rounded-full animate-pulse';
            } else if (state === 'checking') {
                indicator.className = 'w-2 h-2 bg-yellow-400 rounded-full animate-pulse';
            } else {
                indicator.className = 'w-2 h-2 bg-red-400 rounded-full animate-pulse';
            }
        }, 2000);
    },

    // UI Helpers (manter toggleMic, toggleCamera, flips...)
    showCallInterface() {
        this.elements.callInterface.classList.remove('hidden');
        if (this.state.callType === 'voice') {
            this.elements.cameraBtn.classList.add('hidden');
            this.elements.flipBtn.classList.add('hidden');
            this.elements.screenBtn.classList.add('hidden');
            this.elements.localVideo.classList.add('hidden');
        } else {
            this.elements.cameraBtn.classList.remove('hidden');
            this.elements.flipBtn.classList.remove('hidden');
            this.elements.screenBtn.classList.remove('hidden');
            this.elements.localVideo.classList.remove('hidden');
        }
    },
    
    toggleMic() {
        if (!this.state.localStream) return;
        const track = this.state.localStream.getAudioTracks()[0];
        if (track) {
            track.enabled = !track.enabled;
            document.getElementById('mic-on-icon').classList.toggle('hidden');
            document.getElementById('mic-off-icon').classList.toggle('hidden');
            this.elements.micBtn.classList.toggle('bg-red-500'); // Fica vermelho se mutado? Geralmente sim
             // Correção Visual: Se enabled=false (mudo), mostrar vermelho
             if (track.enabled) {
                 this.elements.micBtn.classList.remove('bg-red-500');
             } else {
                 this.elements.micBtn.classList.add('bg-red-500');
             }
        }
    },
    
    toggleCamera() {
        if (!this.state.localStream) return;
        const track = this.state.localStream.getVideoTracks()[0];
        if (track) {
            track.enabled = !track.enabled;
             document.getElementById('camera-on-icon').classList.toggle('hidden');
            document.getElementById('camera-off-icon').classList.toggle('hidden');
             if (track.enabled) {
                 this.elements.cameraBtn.classList.remove('bg-red-500');
             } else {
                 this.elements.cameraBtn.classList.add('bg-red-500');
             }
        }
    },
    
    toggleMinimize() {
        this.elements.callInterface.classList.toggle('hidden');
        this.elements.minimizedCall.classList.toggle('hidden');
    },
    
    maximizeCall() {
        this.elements.callInterface.classList.remove('hidden');
        this.elements.minimizedCall.classList.add('hidden');
    },
    
    startTimer() {
        this.state.startTime = Date.now();
        this.state.timerInterval = setInterval(() => {
            const delta = Date.now() - this.state.startTime;
            const m = Math.floor(delta / 60000).toString().padStart(2, '0');
            const s = Math.floor((delta % 60000) / 1000).toString().padStart(2, '0');
            this.elements.callDuration.textContent = `${m}:${s}`;
            document.getElementById('minimized-duration').textContent = `${m}:${s}`;
        }, 1000);
    },
     
    async flipCamera() {
        if (!this.state.localStream || !this.state.peerConnection) return;
        
        this.state.isFrontCamera = !this.state.isFrontCamera;
        
        try {
            // Parar apenas o track de vídeo, manter áudio
            const oldVideoTrack = this.state.localStream.getVideoTracks()[0];
            if (oldVideoTrack) oldVideoTrack.stop();
            
            // Obter nova stream apenas de vídeo
            const constraints = { 
                video: { facingMode: this.state.isFrontCamera ? 'user' : 'environment' } 
            };
            const newVideoStream = await navigator.mediaDevices.getUserMedia(constraints);
            const newVideoTrack = newVideoStream.getVideoTracks()[0];
            
            // Substituir track no PeerConnection
            const sender = this.state.peerConnection.getSenders().find(s => s.track && s.track.kind === 'video');
            if (sender) {
                await sender.replaceTrack(newVideoTrack);
            }
            
            // Atualizar localStream (remover track antigo, adicionar novo)
            if (oldVideoTrack) this.state.localStream.removeTrack(oldVideoTrack);
            this.state.localStream.addTrack(newVideoTrack);
            
            // Atualizar vídeo local
            this.elements.localVideo.srcObject = this.state.localStream;
            
            console.log('[CallManager] Câmera alternada para:', this.state.isFrontCamera ? 'frontal' : 'traseira');
        } catch (error) {
            console.error('[CallManager] Erro ao alternar câmera:', error);
            // Reverter estado se falhar
            this.state.isFrontCamera = !this.state.isFrontCamera;
        }
    }
};

// Utils Globais
window.maximizeCall = () => CallManager.maximizeCall();
window.endCall = () => CallManager.endCall();


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

