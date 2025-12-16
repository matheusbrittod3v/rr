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
            privateChannel = publicChannel; // Manter a variável para compatibilidade
            
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
    // Configuração
    config: {
        iceServers: [
            { urls: 'stun:stun.l.google.com:19302' },
            { urls: 'stun:stun1.l.google.com:19302' },
            { urls: 'stun:stun2.l.google.com:19302' }
        ]
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
        isFrontCamera: true
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
    
    // Sinalização
    async sendSignal(type, data = null) {
        try {
            await fetch(`${CHAT_CONFIG.apiBaseUrl}/${CHAT_CONFIG.roomCode}/call-signal`, {
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
        } catch (error) {
            console.error('[CallManager] Erro ao enviar sinal:', error);
        }
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
        }
    },
    
    // Iniciar Chamada (Outgoing)
    async startCall(type) {
        this.state.callType = type;
        this.state.isInitiator = true;
        
        // UI Outgoing
        this.elements.outgoingModal.classList.remove('hidden');
        SoundManager.startCalling();
        
        // Enviar sinal
        await this.sendSignal('call-start', { type: type });
        
        // Obter media antecipadamente
        try {
            const constraints = {
                audio: true,
                video: type === 'video' ? { facingMode: 'user' } : false
            };
            this.state.localStream = await navigator.mediaDevices.getUserMedia(constraints);
        } catch (error) {
            console.error('Erro media:', error);
            alert('Erro ao acessar mídia. Verifique permissões.');
            this.cancelCall();
        }
    },
    
    // Cancelar (Quem ligou desiste)
    cancelCall() {
        this.sendSignal('call-cancel');
        this.resetCall();
    },
    
    // Receber Chamada (Incoming)
    handleIncomingCall(data) {
        if (this.state.isCallActive) return; // Busy
        
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
    
    // Aceitar Chamada
    async acceptCall() {
        SoundManager.stop();
        if (navigator.vibrate) navigator.vibrate(0); // Parar vibração
        
        this.elements.incomingModal.classList.add('hidden');
        this.elements.outgoingModal.classList.add('hidden');
        this.showCallInterface();
        this.state.isInitiator = false;
        
        try {
            // Obter media se não tiver (quem recebe)
            if (!this.state.localStream) {
                const constraints = {
                    audio: true,
                    video: this.state.callType === 'video' ? { facingMode: 'user' } : false
                };
                this.state.localStream = await navigator.mediaDevices.getUserMedia(constraints);
            }
            
            this.elements.localVideo.srcObject = this.state.localStream;
            
            this.createPeerConnection();
            
            this.state.localStream.getTracks().forEach(track => {
                this.state.peerConnection.addTrack(track, this.state.localStream);
            });
            
            // Quem recebe aguarda offer
            
        } catch (error) {
            console.error('Erro accept:', error);
            this.endCall(false);
        }
    },
    
    // Lógica quando o outro lado aceita e envia Offer (quem ligou)
    // O fluxo é: Liga -> call-start -> Recebe offer?
    // Não, WebRTC normal: Initiator cria Offer. 
    // Correção: Initiator (quem ligou) deve criar Offer ASSIM que o outro atender?
    // Ou Initiator cria Offer e manda junto com call-start?
    // Melhor approach: Initiator cria Peer e Offer imediatamente e manda.
    // O outro lado recebe offer, mostra modal. Se aceitar, processa offer e manda answer.
    
    // REVISÃO DO HANDLE OFFER PARA SUPORTAR O BOTAO DE ACEITAR
    // Se receber Offer e modal estiver visivel, apenas guardamos o Offer.
    pendingOffer: null,
    
    async handleOffer(offer) {
        // Se ainda estamos no modal, guardar offer
        if (!this.elements.incomingModal.classList.contains('hidden')) {
            this.pendingOffer = offer;
            return;
        }
        
        // Se já aceitou (interface visivel), processar
        await this.processOffer(offer);
    },
    
    async processOffer(offer) {
         if (!this.state.peerConnection) return;
         try {
            await this.state.peerConnection.setRemoteDescription(new RTCSessionDescription(offer));
            const answer = await this.state.peerConnection.createAnswer();
            await this.state.peerConnection.setLocalDescription(answer);
            this.sendSignal('answer', answer);
        } catch (e) {
            console.error('Erro offer:', e);
        }
    },
    
    // Sobrescrevendo a logica de aceitar para processar offer pendente
    async acceptCallCorrected() {
        // ... (logica de UI e media igual acima) ...
        // INJETAR ISSO NO FIM DO ACCEPTCALL ORIGINAL:
        
        // Se sou quem recebeu, agora configuro meu Peer
        // E verifico se tem offer pendente
        if (this.pendingOffer) {
            this.createPeerConnection();
             this.state.localStream.getTracks().forEach(track => {
                this.state.peerConnection.addTrack(track, this.state.localStream);
            });
            await this.processOffer(this.pendingOffer);
            this.pendingOffer = null;
        }
    },
    
    // VAMOS REESCREVER O STARTCALL PARA INICIAR O PEER IMEDIATAMENTE
    async startCallRevisited(type) {
        this.state.callType = type;
        this.state.isInitiator = true;
        
        this.elements.outgoingModal.classList.remove('hidden');
        SoundManager.startCalling();
        
        try {
            const constraints = {
                audio: true,
                video: type === 'video' ? { facingMode: 'user' } : false
            };
            this.state.localStream = await navigator.mediaDevices.getUserMedia(constraints);
            this.elements.localVideo.srcObject = this.state.localStream;
            
            // Criar Peer e Offer JÁ
            this.createPeerConnection();
            this.state.localStream.getTracks().forEach(track => {
                this.state.peerConnection.addTrack(track, this.state.localStream);
            });
            
            const offer = await this.state.peerConnection.createOffer();
            await this.state.peerConnection.setLocalDescription(offer);
            
            // Mandar Sinais
            await this.sendSignal('call-start', { type: type });
            await this.sendSignal('offer', offer);
            
            // Agora espera Answer...
            
        } catch (e) {
            console.error('Erro start:', e);
            this.cancelCall();
        }
    },
    
    // ... Implementação real substituindo os métodos ...
    
    // ---------- MÉTODOS REAIS (Substitua os anteriores) -----------
    
    async startCall(type) {
        this.state.callType = type;
        this.state.isInitiator = true;
        
        this.elements.outgoingModal.classList.remove('hidden');
        SoundManager.startCalling();
        
        try {
            const constraints = {
                audio: true,
                video: type === 'video' ? { facingMode: 'user' } : false
            };
            this.state.localStream = await navigator.mediaDevices.getUserMedia(constraints);
            this.elements.localVideo.srcObject = this.state.localStream;
            
            // Iniciar Peer
            this.createPeerConnection();
            this.state.localStream.getTracks().forEach(track => {
                this.state.peerConnection.addTrack(track, this.state.localStream);
            });
            
            const offer = await this.state.peerConnection.createOffer();
            await this.state.peerConnection.setLocalDescription(offer);
            
            this.sendSignal('call-start', { type: type });
            this.sendSignal('offer', offer);
            
        } catch (error) {
            console.error('[CallManager] Erro ao iniciar chamada:', error);
            alert('Erro ao acessar dispositivos.');
            this.cancelCall();
        }
    },
    
    async acceptCall() {
        SoundManager.stop();
        if (navigator.vibrate) navigator.vibrate(0);
        
        this.elements.incomingModal.classList.add('hidden');
        this.showCallInterface(); 
        
        try {
            const constraints = {
                audio: true,
                video: this.state.callType === 'video' ? { facingMode: 'user' } : false
            };
            
            this.state.localStream = await navigator.mediaDevices.getUserMedia(constraints);
            this.elements.localVideo.srcObject = this.state.localStream;
            
            // Se já tem peer (de tentativas anteriores), fecha
            if (this.state.peerConnection) this.state.peerConnection.close();
            
            this.createPeerConnection();
            
            this.state.localStream.getTracks().forEach(track => {
                this.state.peerConnection.addTrack(track, this.state.localStream);
            });
            
            // Se tem offer guardado
            if (this.pendingOffer) {
                await this.processOffer(this.pendingOffer);
                this.pendingOffer = null;
            }
            
        } catch (error) {
            console.error('[CallManager] Erro ao aceitar:', error);
            this.endCall(false);
        }
    },
    
    async handleAnswer(answer) {
        // Se receber resposta, esconde modal de chamando e mostra interface
        this.elements.outgoingModal.classList.add('hidden');
        SoundManager.stop();
        this.showCallInterface();
        
        if (!this.state.peerConnection) return;
        try {
            await this.state.peerConnection.setRemoteDescription(new RTCSessionDescription(answer));
        } catch (e) { console.error(e); }
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
        
        // Toast simples
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-gray-800 text-white px-6 py-3 rounded-xl shadow-2xl z-50 animate-fade-in';
        toast.textContent = msg;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
        
        this.resetCall();
    },
    
    resetCall() {
         SoundManager.stop();
         
        // Parar tracks
        if (this.state.localStream) {
            this.state.localStream.getTracks().forEach(track => track.stop());
        }
        
        if (this.state.peerConnection) {
            this.state.peerConnection.close();
        }
        
        // Limpar vídeos
        this.elements.localVideo.srcObject = null;
        this.elements.remoteVideo.srcObject = null;
        
        // Esconder UI
        this.elements.callInterface.classList.add('hidden');
        this.elements.minimizedCall.classList.add('hidden');
        this.elements.incomingModal.classList.add('hidden');
        this.elements.outgoingModal.classList.add('hidden');
        
        // Resetar Variaveis
        this.state.localStream = null;
        this.state.peerConnection = null;
        this.state.isCallActive = false;
        this.state.isScreenSharing = false;
        this.pendingOffer = null;
        
        if (this.state.timerInterval) clearInterval(this.state.timerInterval);
        if (this.state.statsInterval) clearInterval(this.state.statsInterval);
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
        if (!this.state.peerConnection) return;
        
        const sender = this.state.peerConnection.getSenders().find(s => s.track.kind === 'video');
        if (sender) {
            sender.replaceTrack(newTrack);
            this.state.localStream.removeTrack(this.state.localStream.getVideoTracks()[0]);
            this.state.localStream.addTrack(newTrack);
            this.elements.localVideo.srcObject = this.state.localStream;
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

    // Peer Connection & Utils (Mantido similar, com adição de Stats)
    createPeerConnection() {
        this.state.peerConnection = new RTCPeerConnection(this.config);
        this.state.isCallActive = true;
        this.startTimer();
        this.startStats(); // Monitorar qualidade
        
        this.state.peerConnection.onicecandidate = (event) => {
            if (event.candidate) this.sendSignal('ice-candidate', event.candidate);
        };
        
        this.state.peerConnection.ontrack = (event) => {
            this.state.remoteStream = event.streams[0];
            this.elements.remoteVideo.srcObject = this.state.remoteStream;
            this.elements.minimizedVideo.srcObject = this.state.remoteStream;
        };
        
        this.state.peerConnection.onconnectionstatechange = () => {
            const state = this.state.peerConnection.connectionState;
            console.log('[Connection State]', state);
            if (state === 'disconnected' || state === 'failed') this.endCall(false);
        };
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
        // ... (manter lógica anterior ou adaptar se necessário) ...
        // Simplificado para brevidade, assumindo lógica anterior funcional
         this.state.isFrontCamera = !this.state.isFrontCamera;
        const constraints = { audio: true, video: { facingMode: this.state.isFrontCamera ? 'user' : 'environment' } };
        try {
            this.state.localStream.getTracks().forEach(t => t.stop());
            const newStream = await navigator.mediaDevices.getUserMedia(constraints);
            this.state.localStream = newStream;
            this.elements.localVideo.srcObject = newStream;
            const videoTrack = newStream.getVideoTracks()[0];
            const sender = this.state.peerConnection.getSenders().find(s => s.track.kind === 'video');
            if (sender) sender.replaceTrack(videoTrack);
        } catch (e) {
            console.error(e);
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

