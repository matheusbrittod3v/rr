@extends('layouts.app')

@section('title', 'Chat - Renttool')

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
                            <p class="text-blue-100 text-sm flex items-center">
                                <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                                Online
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="hidden sm:inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 text-white backdrop-blur-sm">
                            {{ $chatRoom->room_code }}
                        </span>
                        <!-- Botões de Chamada -->
                        <button id="call-video-btn" class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-white/10 text-white hover:bg-white/20 transition-all backdrop-blur-sm" title="Chamada de vídeo">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </button>
                        <button id="call-audio-btn" class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-white/10 text-white hover:bg-white/20 transition-all backdrop-blur-sm" title="Chamada de voz">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                            </svg>
                        </button>
                        <a href="{{ url('/') }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-white/10 text-white hover:bg-white/20 transition-all backdrop-blur-sm">
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
                @foreach($chatRoom->messages as $message)
                    <div class="mb-4 animate-fade-in" data-message-id="{{ $message->id }}">
                        <div class="flex {{ $message->sender_type === 'client' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-xs sm:max-w-md lg:max-w-lg">
                                <div class="flex items-end gap-2 {{ $message->sender_type === 'client' ? 'flex-row-reverse' : 'flex-row' }}">
                                    <div class="px-4 py-3 rounded-2xl shadow-sm {{ 
                                        $message->sender_type === 'client' ? 'bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-br-sm' : 
                                        'bg-white text-gray-800 border border-gray-200 rounded-bl-sm'
                                    }}">
                                        @if($message->message)
                                            <p class="text-sm break-words">{{ $message->message }}</p>
                                        @endif
                                        
                                        @if($message->file_path)
                                            @php
                                                $fileExt = strtolower(pathinfo($message->file_path, PATHINFO_EXTENSION));
                                                $isImage = in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                            @endphp
                                            @if($isImage)
                                                <div class="mt-2">
                                                    <img src="{{ asset('storage/' . $message->file_path) }}" alt="Imagem" class="max-w-xs rounded-lg cursor-pointer" onclick="window.open('{{ asset('storage/' . $message->file_path) }}', '_blank')">
                                                </div>
                                            @else
                                                <div class="mt-2 p-2 bg-black/10 rounded-lg text-sm flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg> 
                                                    <a href="{{ asset('storage/' . $message->file_path) }}" target="_blank" class="hover:underline">Ver anexo ({{ strtoupper($fileExt) }})</a>
                                                </div>
                                            @endif
                                        @endif
                                        
                                        <p class="text-xs mt-1.5 {{ $message->sender_type === 'client' ? 'text-blue-100' : 'text-gray-500' }}">
                                            {{ $message->created_at->format('H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Message Input -->
            <div class="bg-white border-t border-gray-200 p-4">
                <form id="chat-form" class="flex items-end space-x-3" onsubmit="return false;">
                    @csrf
                    <div class="flex-1">
                        <textarea id="message-input" class="w-full px-4 py-3 text-gray-700 bg-gray-50 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none transition-all" placeholder="Digite sua mensagem..." rows="1" maxlength="1000"></textarea>
                        <div class="flex items-center justify-between mt-2 px-1">
                            <span class="text-xs text-gray-500">Pressione Enter para enviar</span>
                            <span id="char-count" class="text-xs text-gray-400">0/1000</span>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2 pb-8">
                        <input type="file" id="file-input" class="hidden" accept="image/*,.zip,.rar,.7z,.txt,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                        <button type="button" id="attach-button" class="p-3 bg-gray-100 rounded-xl hover:bg-gray-200 transition-all hover:scale-105" title="Anexar arquivo">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                        </button>
                        <button type="button" id="send-button" class="p-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all hover:scale-105 shadow-lg active:scale-95 transform duration-150" title="Enviar mensagem">
                            <svg id="send-icon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                        </button>
                    </div>
                </form>
                <div id="file-preview" class="hidden mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                             <span id="file-name" class="text-sm text-gray-700 font-medium"></span>
                        </div>
                        <button type="button" id="remove-file" class="text-red-500 hover:text-red-700">X</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Interface de Chamada (Overlay) -->
<div id="call-overlay" class="hidden fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center">
    <!-- Chamada Minimizada -->
    <div id="call-minimized" class="hidden fixed bottom-4 right-4 bg-white rounded-lg shadow-2xl p-4 w-64 z-60 cursor-pointer hover:scale-105 transition-transform">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Chamada em andamento</p>
                    <p class="text-xs text-gray-500" id="call-duration">00:00</p>
                </div>
            </div>
            <button id="maximize-call" class="text-gray-600 hover:text-gray-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Chamada Maximizada -->
    <div id="call-maximized" class="w-full h-full flex flex-col">
        <!-- Vídeo Remoto -->
        <div class="flex-1 relative bg-gray-900">
            <video id="remote-video" autoplay playsinline class="w-full h-full object-cover"></video>
            <!-- Vídeo Local (Picture-in-Picture) -->
            <div class="absolute bottom-4 right-4 w-48 h-36 bg-gray-800 rounded-lg overflow-hidden shadow-2xl">
                <video id="local-video" autoplay playsinline muted class="w-full h-full object-cover"></video>
            </div>
        </div>
        
        <!-- Controles da Chamada -->
        <div class="bg-gray-900 bg-opacity-75 px-6 py-4">
            <div class="flex items-center justify-center space-x-4">
                <!-- Toggle Câmera -->
                <button id="toggle-video" class="p-3 bg-white bg-opacity-20 rounded-full hover:bg-opacity-30 transition-all text-white">
                    <svg id="video-on-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <svg id="video-off-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </button>
                
                <!-- Toggle Áudio -->
                <button id="toggle-audio" class="p-3 bg-white bg-opacity-20 rounded-full hover:bg-opacity-30 transition-all text-white">
                    <svg id="audio-on-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                    </svg>
                    <svg id="audio-off-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
                    </svg>
                </button>
                
                <!-- Trocar Câmera -->
                <button id="switch-camera" class="p-3 bg-white bg-opacity-20 rounded-full hover:bg-opacity-30 transition-all text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
                
                <!-- Minimizar -->
                <button id="minimize-call" class="p-3 bg-white bg-opacity-20 rounded-full hover:bg-opacity-30 transition-all text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                </button>
                
                <!-- Encerrar Chamada -->
                <button id="end-call" class="p-4 bg-red-600 rounded-full hover:bg-red-700 transition-all text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
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
    
    @keyframes button-press {
        0% { transform: scale(1); }
        50% { transform: scale(0.95); }
        100% { transform: scale(1); }
    }
    
    @keyframes icon-bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
    
    #send-button:active {
        animation: button-press 0.2s ease-out;
    }
    
    #send-icon {
        transition: transform 0.2s ease-out;
    }
    
    #send-button:hover #send-icon {
        transform: translateY(-2px) scale(1.1);
    }
    
    #chat-messages::-webkit-scrollbar { width: 6px; }
    #chat-messages::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
    #chat-messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>

<script>
// Aguardar Echo estar disponível antes de iniciar
(function() {
    console.log('[DEBUG] Iniciando inicialização do chat...');
    console.log('[DEBUG] Verificando módulos Vite carregados...');
    
    // Aguardar o DOM estar pronto
    function waitForDOM(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);
        } else {
            callback();
        }
    }
    
    // Aguardar Echo estar disponível
    function waitForEcho(callback, maxAttempts = 50) {
        let attempts = 0;
        
        function checkEcho() {
            attempts++;
            console.log(`[DEBUG] Tentativa ${attempts}/${maxAttempts}: Verificando se Echo está disponível...`);
            console.log('[DEBUG] window.Echo:', window.Echo);
            console.log('[DEBUG] typeof window.Echo:', typeof window.Echo);
            console.log('[DEBUG] window.Pusher:', window.Pusher);
            
            if (window.Echo && typeof window.Echo.channel === 'function') {
                console.log('[DEBUG] ✅ Echo encontrado e funcional!', window.Echo);
                callback();
            } else if (attempts >= maxAttempts) {
                console.error('[DEBUG] ❌ Timeout: Echo não foi carregado após', maxAttempts * 100, 'ms');
                console.error('[DEBUG] Verifique se o websocket.js está sendo importado corretamente em app.js');
                console.error('[DEBUG] Verifique se as dependências (laravel-echo, pusher-js) estão instaladas');
                // Mesmo assim tentar continuar se algum Echo estiver disponível
                if (window.Echo) {
                    console.warn('[DEBUG] ⚠️ Tentando usar Echo parcialmente disponível...');
                    callback();
                }
            } else {
                setTimeout(checkEcho, 100);
            }
        }
        
        checkEcho();
    }
    
    // Iniciar quando DOM estiver pronto
    waitForDOM(function() {
        console.log('[DEBUG] DOM carregado, aguardando Echo...');
        waitForEcho(function() {
    
    console.log('[DEBUG] ========================================');
    console.log('[DEBUG] INICIALIZANDO CHAT - DEBUG COMPLETO');
    console.log('[DEBUG] ========================================');
    
    // UI Elements
    const elements = {
        chatForm: document.getElementById('chat-form'),
        messageInput: document.getElementById('message-input'),
        fileInput: document.getElementById('file-input'),
        attachButton: document.getElementById('attach-button'),
        sendButton: document.getElementById('send-button'),
        sendIcon: document.getElementById('send-icon'),
        chatMessages: document.getElementById('chat-messages'),
        filePreview: document.getElementById('file-preview'),
        fileName: document.getElementById('file-name'),
        removeFileBtn: document.getElementById('remove-file'),
        charCount: document.getElementById('char-count')
    };
    
    console.log('[DEBUG] Elementos DOM carregados:', {
        chatForm: !!elements.chatForm,
        messageInput: !!elements.messageInput,
        sendButton: !!elements.sendButton,
        chatMessages: !!elements.chatMessages
    });
    
    // State
    const state = {
        lastMessageId: {{ $chatRoom->messages->max('id') ?? 0 }},
        selectedFile: null
    };
    
    console.log('[DEBUG] Estado inicial:', {
        lastMessageId: state.lastMessageId,
        selectedFile: state.selectedFile
    });
    
    // Config
    const config = {
        roomCode: "{{ $chatRoom->room_code }}",
        csrfToken: "{{ csrf_token() }}",
        sendMessageUrl: "{{ route('chat.send-message', $chatRoom->room_code) }}"
    };
    
    console.log('[DEBUG] Configuração:', {
        roomCode: config.roomCode,
        sendMessageUrl: config.sendMessageUrl,
        csrfTokenLength: config.csrfToken.length
    });

    // ========== CHAT FUNCTIONS ==========
    
    // Auto-resize textarea
    elements.messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
        elements.charCount.textContent = this.value.length + '/1000';
    });

    // Função para enviar mensagem - requisição assíncrona em background
    async function sendMessage() {
        console.log('[DEBUG] ========================================');
        console.log('[DEBUG] INICIANDO ENVIO DE MENSAGEM');
        console.log('[DEBUG] ========================================');
        
        const message = elements.messageInput.value.trim();
        const hasMessage = !!message;
        const hasFile = !!state.selectedFile;
        
        console.log('[DEBUG] Validação de entrada:', {
            message: message,
            messageLength: message.length,
            hasMessage: hasMessage,
            hasFile: hasFile,
            selectedFile: state.selectedFile ? {
                name: state.selectedFile.name,
                size: state.selectedFile.size,
                type: state.selectedFile.type
            } : null
        });
        
        if (!message && !state.selectedFile) {
            console.log('[DEBUG] ❌ Envio cancelado: mensagem vazia e sem arquivo');
            return;
        }

        console.log('[DEBUG] Preparando FormData...');
        const formData = new FormData();
        formData.append('message', message);
        formData.append('_token', config.csrfToken);
        if (state.selectedFile) {
            formData.append('file', state.selectedFile);
            console.log('[DEBUG] Arquivo adicionado ao FormData:', {
                name: state.selectedFile.name,
                size: state.selectedFile.size,
                type: state.selectedFile.type
            });
        }
        
        console.log('[DEBUG] FormData preparado:', {
            hasMessage: formData.has('message'),
            hasToken: formData.has('_token'),
            hasFile: formData.has('file')
        });

        // Desabilitar botão de enviar durante o envio com animação
        const sendButton = elements.sendButton;
        const sendIcon = elements.sendIcon;
        const originalIconHTML = sendIcon ? sendIcon.outerHTML : '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>';
        
        sendButton.disabled = true;
        sendButton.classList.add('opacity-50', 'cursor-not-allowed', 'animate-pulse');
        if (sendIcon) {
            sendIcon.style.display = 'none';
        }
        sendButton.innerHTML = '<svg class="animate-spin h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
        console.log('[DEBUG] Botão de enviar desabilitado com animação de loading');

        try {
            console.log('[DEBUG] Enviando requisição HTTP...');
            console.log('[DEBUG] URL:', config.sendMessageUrl);
            console.log('[DEBUG] Método: POST');
            console.log('[DEBUG] Headers:', {
                'X-Requested-With': 'XMLHttpRequest'
            });
            
            const startTime = Date.now();
            
            // Requisição assíncrona em background (não bloqueia a UI)
            const res = await fetch(config.sendMessageUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const requestTime = Date.now() - startTime;
            console.log('[DEBUG] ✅ Resposta recebida em', requestTime + 'ms');
            console.log('[DEBUG] Status HTTP:', res.status, res.statusText);
            console.log('[DEBUG] Headers da resposta:', Object.fromEntries(res.headers.entries()));
            
            const data = await res.json();
            console.log('[DEBUG] Dados da resposta:', data);
            
            if (data.success) {
                console.log('[DEBUG] ✅ Mensagem enviada com sucesso!');
                console.log('[DEBUG] Limpando formulário...');
                
                // Limpar formulário apenas se sucesso
                elements.messageInput.value = '';
                elements.messageInput.style.height = 'auto';
                elements.charCount.textContent = '0/1000';
                state.selectedFile = null;
                elements.filePreview.classList.add('hidden');
                elements.fileInput.value = '';
                
                console.log('[DEBUG] Formulário limpo');
                console.log('[DEBUG] Aguardando broadcast via Reverb...');
            } else {
                console.error('[DEBUG] ❌ Erro ao enviar mensagem:', data);
                alert('Erro ao enviar mensagem. Tente novamente.');
            }
        } catch (error) {
            console.error('[DEBUG] ❌ ERRO na requisição:', error);
            console.error('[DEBUG] Tipo do erro:', error.name);
            console.error('[DEBUG] Mensagem do erro:', error.message);
            console.error('[DEBUG] Stack trace:', error.stack);
            alert('Erro ao enviar mensagem. Verifique sua conexão e tente novamente.');
        } finally {
            // Reabilitar botão com animação suave
            console.log('[DEBUG] Reabilitando botão de enviar...');
            sendButton.disabled = false;
            sendButton.classList.remove('opacity-50', 'cursor-not-allowed', 'animate-pulse');
            
            // Restaurar ícone original com animação
            sendButton.innerHTML = originalIconHTML;
            const restoredIcon = document.getElementById('send-icon');
            if (restoredIcon) {
                restoredIcon.classList.add('animate-ping');
                setTimeout(() => {
                    restoredIcon.classList.remove('animate-ping');
                }, 500);
            }
            
            // Atualizar referência do ícone
            elements.sendIcon = document.getElementById('send-icon');
            
            console.log('[DEBUG] ========================================');
            console.log('[DEBUG] PROCESSO DE ENVIO FINALIZADO');
            console.log('[DEBUG] ========================================');
        }
    }
    
    // Prevenir submit padrão do formulário
    elements.chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        sendMessage();
        return false;
    });
    
    // Botão de enviar com animação
    elements.sendButton.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Animação de clique
        console.log('[DEBUG] Animação de clique no botão enviar');
        elements.sendButton.classList.add('animate-pulse');
        elements.sendIcon.classList.add('animate-bounce');
        
        setTimeout(() => {
            elements.sendButton.classList.remove('animate-pulse');
            elements.sendIcon.classList.remove('animate-bounce');
        }, 300);
        
        sendMessage();
    });
    
    // Enter para enviar (Shift+Enter para nova linha)
    elements.messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            e.stopPropagation();
            
            // Animação visual ao pressionar Enter
            console.log('[DEBUG] Enter pressionado, animando botão...');
            elements.sendButton.classList.add('animate-pulse', 'ring-2', 'ring-blue-300');
            elements.sendIcon.classList.add('animate-bounce');
            
            setTimeout(() => {
                elements.sendButton.classList.remove('animate-pulse', 'ring-2', 'ring-blue-300');
                elements.sendIcon.classList.remove('animate-bounce');
            }, 300);
            
            sendMessage();
        }
    });

    // File handling
    elements.attachButton.addEventListener('click', () => elements.fileInput.click());
    
    elements.fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            state.selectedFile = file;
            elements.fileName.textContent = file.name;
            elements.filePreview.classList.remove('hidden');
        }
    });

    elements.removeFileBtn.addEventListener('click', function() {
        state.selectedFile = null;
        elements.filePreview.classList.add('hidden');
        elements.fileInput.value = '';
    });

    // Add message to chat
    function addMessageToChat(message) {
        console.log('[DEBUG] ========================================');
        console.log('[DEBUG] ADICIONANDO MENSAGEM AO CHAT');
        console.log('[DEBUG] ========================================');
        console.log('[DEBUG] Dados da mensagem:', {
            id: message.id,
            senderType: message.sender_type,
            message: message.message,
            filePath: message.file_path,
            createdAt: message.created_at
        });
        
        // Evitar mensagens duplicadas
        if (message.id <= state.lastMessageId) {
            console.log('[DEBUG] ⚠️ Mensagem duplicada ignorada:', {
                messageId: message.id,
                lastMessageId: state.lastMessageId
            });
            return;
        }
        
        console.log('[DEBUG] Mensagem é nova, processando...');
        state.lastMessageId = message.id;
        console.log('[DEBUG] lastMessageId atualizado para:', state.lastMessageId);
        
        const messageDiv = document.createElement('div');
        messageDiv.className = 'mb-4 animate-fade-in';
        messageDiv.setAttribute('data-message-id', message.id);
        console.log('[DEBUG] Elemento DIV criado');
        
        const isClient = message.sender_type === 'client';
        console.log('[DEBUG] Tipo de remetente:', {
            senderType: message.sender_type,
            isClient: isClient
        });
        
        let fileHtml = '';
        if (message.file_path) {
            console.log('[DEBUG] Processando arquivo anexo...');
            const fileExt = message.file_path.split('.').pop().toLowerCase();
            const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExt);
            console.log('[DEBUG] Informações do arquivo:', {
                filePath: message.file_path,
                extension: fileExt,
                isImage: isImage
            });
            
            if (isImage) {
                fileHtml = `<div class="mt-2"><img src="/storage/${message.file_path}" alt="Imagem" class="max-w-xs rounded-lg cursor-pointer" onclick="window.open('/storage/${message.file_path}', '_blank')"></div>`;
                console.log('[DEBUG] HTML de imagem gerado');
            } else {
                fileHtml = `<div class="mt-2 p-2 bg-black/10 rounded-lg text-sm flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg> 
                    <a href="/storage/${message.file_path}" target="_blank" class="hover:underline">Ver anexo (${fileExt.toUpperCase()})</a>
                </div>`;
                console.log('[DEBUG] HTML de arquivo gerado');
            }
        } else {
            console.log('[DEBUG] Nenhum arquivo anexado');
        }
        
        const messageDate = new Date(message.created_at);
        const timeString = messageDate.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        console.log('[DEBUG] Data formatada:', {
            original: message.created_at,
            parsed: messageDate.toISOString(),
            formatted: timeString
        });
        
        messageDiv.innerHTML = `
            <div class="flex ${isClient ? 'justify-end' : 'justify-start'}">
                <div class="max-w-xs sm:max-w-md lg:max-w-lg">
                    <div class="flex items-end gap-2 ${isClient ? 'flex-row-reverse' : 'flex-row'}">
                        <div class="px-4 py-3 rounded-2xl shadow-sm ${
                            isClient ? 'bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-br-sm' : 
                            'bg-white text-gray-800 border border-gray-200 rounded-bl-sm'
                        }">
                            ${message.message ? `<p class="text-sm break-words">${message.message}</p>` : ''}
                            ${fileHtml}
                            <p class="text-xs mt-1.5 ${isClient ? 'text-blue-100' : 'text-gray-500'}">
                                ${timeString}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        console.log('[DEBUG] HTML da mensagem gerado, adicionando ao DOM...');
        elements.chatMessages.appendChild(messageDiv);
        console.log('[DEBUG] ✅ Mensagem adicionada ao DOM');
        
        scrollToBottom();
        console.log('[DEBUG] Scroll executado');
        console.log('[DEBUG] ========================================');
        console.log('[DEBUG] MENSAGEM ADICIONADA COM SUCESSO');
        console.log('[DEBUG] ========================================');
    }

    function scrollToBottom() {
        const beforeScroll = elements.chatMessages.scrollTop;
        const scrollHeight = elements.chatMessages.scrollHeight;
        elements.chatMessages.scrollTop = scrollHeight;
        const afterScroll = elements.chatMessages.scrollTop;
        console.log('[DEBUG] Scroll executado:', {
            beforeScroll: beforeScroll,
            scrollHeight: scrollHeight,
            afterScroll: afterScroll,
            scrolled: afterScroll > beforeScroll
        });
    }

    // ========== REVERB WEBSOCKET ==========
    
    console.log('[DEBUG] ========================================');
    console.log('[DEBUG] CONFIGURANDO CONEXÃO REVERB');
    console.log('[DEBUG] ========================================');
    
    const roomCode = "{{ $chatRoom->room_code }}";
    const channelName = `chat-room.${roomCode}`;
    
    console.log('[DEBUG] Configuração do canal:', {
        roomCode: roomCode,
        channelName: channelName,
        echoAvailable: !!window.Echo,
        echoType: typeof window.Echo,
        reverbConfig: window.Echo ? {
            wsHost: window.Echo.connector?.options?.wsHost || 'não configurado',
            wsPort: window.Echo.connector?.options?.wsPort || 'não configurado',
            broadcaster: window.Echo.connector?.options?.broadcaster || 'não configurado'
        } : 'Echo não disponível'
    });
    
    try {
        console.log('[DEBUG] Criando conexão com o canal...');
        const channel = window.Echo.channel(channelName);
        console.log('[DEBUG] ✅ Canal criado:', channel);
        
        console.log('[DEBUG] Configurando listener para evento: .chat.message.sent');
        
        channel.listen('.chat.message.sent', (event) => {
            console.log('[DEBUG] ========================================');
            console.log('[DEBUG] NOVA MENSAGEM RECEBIDA VIA REVERB');
            console.log('[DEBUG] ========================================');
            console.log('[DEBUG] Evento completo:', event);
            console.log('[DEBUG] Timestamp:', new Date().toISOString());
            console.log('[DEBUG] Dados do evento:', {
                hasMessage: !!event.message,
                messageId: event.message?.id,
                senderType: event.message?.sender_type,
                messageText: event.message?.message,
                hasFile: !!event.message?.file_path,
                filePath: event.message?.file_path,
                createdAt: event.message?.created_at
            });
            
            if (event.message) {
                console.log('[DEBUG] Mensagem válida encontrada, processando...');
                console.log('[DEBUG] Estado antes de adicionar:', {
                    lastMessageId: state.lastMessageId,
                    newMessageId: event.message.id
                });
                
                addMessageToChat(event.message);
                
                console.log('[DEBUG] ✅ Mensagem adicionada ao chat');
                console.log('[DEBUG] Estado após adicionar:', {
                    lastMessageId: state.lastMessageId
                });
            } else {
                console.warn('[DEBUG] ⚠️ Evento recebido mas sem campo "message"');
                console.warn('[DEBUG] Estrutura do evento:', Object.keys(event));
            }
            
            console.log('[DEBUG] ========================================');
            console.log('[DEBUG] PROCESSAMENTO DE MENSAGEM FINALIZADO');
            console.log('[DEBUG] ========================================');
        });
        
        console.log('[DEBUG] ✅ Listener configurado com sucesso');
        console.log('[DEBUG] Aguardando mensagens do servidor...');
        
        // Verificar status da conexão
        if (window.Echo.connector && window.Echo.connector.socket) {
            const socket = window.Echo.connector.socket;
            console.log('[DEBUG] Status da conexão WebSocket:', {
                readyState: socket.readyState,
                readyStateText: socket.readyState === 1 ? 'OPEN' : socket.readyState === 0 ? 'CONNECTING' : 'CLOSED',
                url: socket.url || 'N/A'
            });
            
            // Listeners de eventos do socket
            socket.onopen = function() {
                console.log('[DEBUG] ✅ WebSocket CONECTADO');
            };
            
            socket.onclose = function(event) {
                console.warn('[DEBUG] ⚠️ WebSocket FECHADO:', {
                    code: event.code,
                    reason: event.reason,
                    wasClean: event.wasClean
                });
            };
            
            socket.onerror = function(error) {
                console.error('[DEBUG] ❌ Erro no WebSocket:', error);
            };
            
            socket.onmessage = function(event) {
                console.log('[DEBUG] Mensagem raw recebida do WebSocket:', event.data);
            };
        } else {
            console.warn('[DEBUG] ⚠️ Socket não encontrado no Echo.connector');
        }
        
    } catch (error) {
        console.error('[DEBUG] ❌ ERRO ao configurar Reverb:', error);
        console.error('[DEBUG] Tipo do erro:', error.name);
        console.error('[DEBUG] Mensagem do erro:', error.message);
        console.error('[DEBUG] Stack trace:', error.stack);
    }
    
    console.log('[DEBUG] ========================================');
    console.log('[DEBUG] CONFIGURAÇÃO REVERB FINALIZADA');
    console.log('[DEBUG] ========================================');

    // Initial scroll
    console.log('[DEBUG] Executando scroll inicial...');
    scrollToBottom();
    
    console.log('[DEBUG] ========================================');
    console.log('[DEBUG] INICIALIZAÇÃO DO CHAT COMPLETA');
    console.log('[DEBUG] ========================================');
    console.log('[DEBUG] Sistema pronto para enviar e receber mensagens');
    console.log('[DEBUG] Canal Reverb:', channelName);
    console.log('[DEBUG] ========================================');
    
        }); // Fecha waitForEcho
    }); // Fecha waitForDOM
})(); // Fecha função anônima auto-executável

// ============================================
// WEBSOCKET + WEBRTC - SISTEMA DE CHAMADAS
// ============================================
(function() {
    const roomCode = "{{ $chatRoom->room_code }}";
    const senderType = "{{ session('chat_user_role', 'client') }}";
    
    let webrtcManager = null;
    let callStartTime = null;
    let callDurationInterval = null;
    
    // Elementos da UI
    const callOverlay = document.getElementById('call-overlay');
    const callMinimized = document.getElementById('call-minimized');
    const callMaximized = document.getElementById('call-maximized');
    const localVideo = document.getElementById('local-video');
    const remoteVideo = document.getElementById('remote-video');
    const toggleVideoBtn = document.getElementById('toggle-video');
    const toggleAudioBtn = document.getElementById('toggle-audio');
    const switchCameraBtn = document.getElementById('switch-camera');
    const minimizeCallBtn = document.getElementById('minimize-call');
    const maximizeCallBtn = document.getElementById('maximize-call');
    const endCallBtn = document.getElementById('end-call');
    const callVideoBtn = document.getElementById('call-video-btn');
    const callAudioBtn = document.getElementById('call-audio-btn');
    const callDuration = document.getElementById('call-duration');
    
    // Ícones
    const videoOnIcon = document.getElementById('video-on-icon');
    const videoOffIcon = document.getElementById('video-off-icon');
    const audioOnIcon = document.getElementById('audio-on-icon');
    const audioOffIcon = document.getElementById('audio-off-icon');
    
    // Inicializar WebRTC Manager quando Echo estiver pronto
    function initWebRTC() {
        if (!window.WebRTCManager) {
            console.error('[Call] WebRTCManager não carregado');
            return;
        }
        
        webrtcManager = new WebRTCManager(roomCode, senderType);
        
        // Configurar callbacks
        webrtcManager.onRemoteStream = (stream) => {
            console.log('[Call] Stream remoto recebido');
            remoteVideo.srcObject = stream;
        };
        
        webrtcManager.onTransmissionModeChanged = (mode) => {
            console.log('[Call] Modo de transmissão:', mode);
            // Criar ou atualizar indicador de modo
            let modeIndicator = document.getElementById('transmission-mode-indicator');
            if (!modeIndicator) {
                modeIndicator = document.createElement('div');
                modeIndicator.id = 'transmission-mode-indicator';
                modeIndicator.className = 'absolute top-4 left-4 px-3 py-1 rounded-lg text-xs font-medium z-10';
                callMaximized.querySelector('.relative')?.appendChild(modeIndicator) || 
                callMaximized.querySelector('.flex-1')?.appendChild(modeIndicator);
            }
            modeIndicator.textContent = mode === 'webrtc' ? 'WebRTC' : 'WebSocket (Fallback)';
            modeIndicator.className = mode === 'webrtc' 
                ? 'absolute top-4 left-4 px-3 py-1 bg-green-500 text-white rounded-lg text-xs font-medium z-10 shadow-lg'
                : 'absolute top-4 left-4 px-3 py-1 bg-yellow-500 text-white rounded-lg text-xs font-medium z-10 shadow-lg animate-pulse';
        };
        
        webrtcManager.onVideoToggled = (enabled) => {
            videoOnIcon.classList.toggle('hidden', !enabled);
            videoOffIcon.classList.toggle('hidden', enabled);
        };
        
        webrtcManager.onAudioToggled = (enabled) => {
            audioOnIcon.classList.toggle('hidden', !enabled);
            audioOffIcon.classList.toggle('hidden', enabled);
        };
        
        webrtcManager.onCameraSwitched = (isFront) => {
            console.log('[Call] Câmera trocada:', isFront ? 'frente' : 'trás');
        };
        
        webrtcManager.onMinimizeToggled = (minimized) => {
            if (minimized) {
                callMaximized.classList.add('hidden');
                callMinimized.classList.remove('hidden');
            } else {
                callMaximized.classList.remove('hidden');
                callMinimized.classList.add('hidden');
            }
        };
        
        webrtcManager.onCallEnded = () => {
            endCall();
        };
        
        webrtcManager.onTransmissionModeChanged = (mode) => {
            console.log('[Call] Modo de transmissão:', mode);
            const modeIndicator = document.getElementById('transmission-mode-indicator');
            if (modeIndicator) {
                modeIndicator.classList.remove('hidden');
                modeIndicator.textContent = mode === 'webrtc' ? 'WebRTC' : 'WebSocket (Fallback)';
                modeIndicator.className = mode === 'webrtc' 
                    ? 'absolute top-4 left-4 px-3 py-1 bg-green-500 text-white rounded-lg text-xs font-medium z-10 shadow-lg'
                    : 'absolute top-4 left-4 px-3 py-1 bg-yellow-500 text-white rounded-lg text-xs font-medium z-10 shadow-lg animate-pulse';
            }
        };
        
        console.log('[Call] WebRTC Manager inicializado');
    }
    
    // Iniciar chamada de vídeo
    callVideoBtn?.addEventListener('click', async () => {
        try {
            if (!webrtcManager) initWebRTC();
            
            await webrtcManager.startCall(true);
            showCallInterface();
            localVideo.srcObject = webrtcManager.localStream;
            startCallTimer();
        } catch (error) {
            console.error('[Call] Erro ao iniciar chamada de vídeo:', error);
            alert('Erro ao iniciar chamada. Verifique as permissões de câmera e microfone.');
        }
    });
    
    // Iniciar chamada de voz
    callAudioBtn?.addEventListener('click', async () => {
        try {
            if (!webrtcManager) initWebRTC();
            
            await webrtcManager.startCall(false);
            showCallInterface();
            localVideo.srcObject = webrtcManager.localStream;
            startCallTimer();
        } catch (error) {
            console.error('[Call] Erro ao iniciar chamada de voz:', error);
            alert('Erro ao iniciar chamada. Verifique as permissões de microfone.');
        }
    });
    
    // Toggle vídeo
    toggleVideoBtn?.addEventListener('click', () => {
        webrtcManager?.toggleVideo();
    });
    
    // Toggle áudio
    toggleAudioBtn?.addEventListener('click', () => {
        webrtcManager?.toggleAudio();
    });
    
    // Trocar câmera
    switchCameraBtn?.addEventListener('click', () => {
        webrtcManager?.switchCamera();
    });
    
    // Minimizar chamada
    minimizeCallBtn?.addEventListener('click', () => {
        webrtcManager?.toggleMinimize();
    });
    
    // Maximizar chamada
    maximizeCallBtn?.addEventListener('click', () => {
        webrtcManager?.toggleMinimize();
    });
    
    // Encerrar chamada
    endCallBtn?.addEventListener('click', () => {
        webrtcManager?.endCall();
    });
    
    // Mostrar interface de chamada
    function showCallInterface() {
        callOverlay.classList.remove('hidden');
        callMaximized.classList.remove('hidden');
        callMinimized.classList.add('hidden');
    }
    
    // Encerrar chamada
    function endCall() {
        callOverlay.classList.add('hidden');
        callMaximized.classList.add('hidden');
        callMinimized.classList.add('hidden');
        
        if (localVideo.srcObject) {
            localVideo.srcObject = null;
        }
        if (remoteVideo.srcObject) {
            remoteVideo.srcObject = null;
        }
        
        stopCallTimer();
        webrtcManager = null;
    }
    
    // Timer de duração da chamada
    function startCallTimer() {
        callStartTime = Date.now();
        callDurationInterval = setInterval(() => {
            const elapsed = Math.floor((Date.now() - callStartTime) / 1000);
            const minutes = Math.floor(elapsed / 60);
            const seconds = elapsed % 60;
            callDuration.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }, 1000);
    }
    
    function stopCallTimer() {
        if (callDurationInterval) {
            clearInterval(callDurationInterval);
            callDurationInterval = null;
        }
        callStartTime = null;
        callDuration.textContent = '00:00';
    }
    
    // Escutar ofertas de chamada via WebSocket
    if (window.Echo) {
        const channel = window.Echo.channel(`chat-room.${roomCode}`);
        
        channel.listen('.call.signal', (event) => {
            const { type, payload, sender_type } = event.data;
            
            // Ignorar sinais do próprio usuário
            if (sender_type === senderType) return;
            
            // Se receber offer, aceitar automaticamente
            if (type === 'offer' && !webrtcManager) {
                initWebRTC();
                webrtcManager.acceptCall(payload).then(() => {
                    showCallInterface();
                    localVideo.srcObject = webrtcManager.localStream;
                    startCallTimer();
                }).catch(error => {
                    console.error('[Call] Erro ao aceitar chamada:', error);
                });
            }
        });
    }
    
    // Inicializar quando Echo estiver pronto
    if (window.Echo) {
        initWebRTC();
    } else {
        // Aguardar Echo
        const checkEcho = setInterval(() => {
            if (window.Echo) {
                clearInterval(checkEcho);
                initWebRTC();
            }
        }, 100);
    }
})();
</script>

@section('scripts')
<script src="{{ asset('js/webrtc-manager.js') }}"></script>
@endsection