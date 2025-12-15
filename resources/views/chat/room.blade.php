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
                            <p class="text-blue-100 text-sm flex items-center space-x-2">
                                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                                <span>Você: {{ session('chat_user_role', 'client') === 'client' ? 'Cliente' : 'Prestador' }}</span>
                                <span class="mx-1">•</span>
                                <span id="other-user-status" class="flex items-center">
                                    <span class="w-2 h-2 bg-gray-400 rounded-full mr-1"></span>
                                    <span id="other-user-name">{{ session('chat_user_role', 'client') === 'client' ? 'Prestador' : 'Cliente' }}</span>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="hidden sm:inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 text-white backdrop-blur-sm">
                            {{ $chatRoom->room_code }}
                        </span>
                        <!-- Botões de Chamada -->
                        <button id="call-video-btn" aria-label="Iniciar chamada de vídeo" class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-white/10 text-white hover:bg-white/20 transition-all backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-600" title="Chamada de vídeo">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </button>
                        <button id="call-audio-btn" aria-label="Iniciar chamada de voz" class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-white/10 text-white hover:bg-white/20 transition-all backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-600" title="Chamada de voz">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                            </svg>
                        </button>
                        <a href="{{ url('/') }}" aria-label="Sair do chat" class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-white/10 text-white hover:bg-white/20 transition-all backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Sair
                        </a>
                    </div>
                </div>
            </div>

            <!-- Messages Container -->
            <div id="chat-messages" role="log" aria-live="polite" aria-label="Mensagens do chat" class="h-[500px] overflow-y-auto p-6 bg-gradient-to-b from-white to-gray-50 scroll-smooth">
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
                                            <p class="text-sm break-words">{{ e($message->message) }}</p>
                                        @endif
                                        
                                        @if($message->file_path && $message->getSafeFilePath())
                                            @php
                                                $safePath = $message->getSafeFilePath();
                                                $fileExt = $message->getFileExtension();
                                            @endphp
                                            @if($message->isImage())
                                                <div class="mt-2">
                                                    <img src="{{ asset('storage/' . e($safePath)) }}" alt="Imagem anexada" class="max-w-xs rounded-lg cursor-pointer" loading="lazy" onclick="window.open('{{ asset('storage/' . e($safePath)) }}', '_blank')">
                                                </div>
                                            @else
                                                <div class="mt-2 p-2 bg-black/10 rounded-lg text-sm flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg> 
                                                    <a href="{{ asset('storage/' . e($safePath)) }}" target="_blank" rel="noopener noreferrer" class="hover:underline">Ver anexo ({{ strtoupper(e($fileExt)) }})</a>
                                                </div>
                                            @endif
                                        @endif
                                        
                                        <p class="text-xs mt-1.5 {{ $message->sender_type === 'client' ? 'text-blue-200' : 'text-gray-500' }}">
                                            {{ $message->getFormattedTime() }}
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
                        <input type="file" id="file-input" class="hidden" accept="image/*,.zip,.rar,.7z,.txt,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" aria-label="Selecionar arquivo para anexar">
                        <button type="button" id="attach-button" aria-label="Anexar arquivo" class="p-3 bg-gray-100 rounded-xl hover:bg-gray-200 transition-all hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" title="Anexar arquivo">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                        </button>
                        <button type="button" id="send-button" aria-label="Enviar mensagem" class="p-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all hover:scale-105 shadow-lg active:scale-95 transform duration-150 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" title="Enviar mensagem">
                            <svg id="send-icon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
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

<!-- Notificação de Chamada Recebida -->
<div id="incoming-call-notification" role="dialog" aria-labelledby="incoming-call-title" aria-modal="true" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4 animate-scale-in">
        <div class="text-center">
            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse" aria-hidden="true">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2" id="incoming-call-title">Chamada Recebida</h3>
            <p class="text-gray-600 mb-6" id="incoming-call-subtitle">Deseja aceitar esta chamada?</p>
            <div class="flex items-center justify-center space-x-4">
                <button id="reject-call-btn" aria-label="Recusar chamada" class="px-6 py-3 bg-red-500 text-white rounded-xl hover:bg-red-600 transition-all font-medium flex items-center space-x-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Recusar</span>
                </button>
                <button id="accept-call-btn" aria-label="Aceitar chamada" class="px-6 py-3 bg-green-500 text-white rounded-xl hover:bg-green-600 transition-all font-medium flex items-center space-x-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Aceitar</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Interface de Chamada (Overlay) -->
<div id="call-overlay" role="dialog" aria-labelledby="call-title" aria-modal="true" class="hidden fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center">
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
                <button id="toggle-video" aria-label="Ligar ou desligar câmera" class="p-3 bg-white bg-opacity-20 rounded-full hover:bg-opacity-30 transition-all text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-900">
                    <svg id="video-on-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <svg id="video-off-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </button>
                
                <!-- Toggle Áudio -->
                <button id="toggle-audio" aria-label="Mutar ou desmutar áudio" class="p-3 bg-white bg-opacity-20 rounded-full hover:bg-opacity-30 transition-all text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-900">
                    <svg id="audio-on-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                    </svg>
                    <svg id="audio-off-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
                    </svg>
                </button>
                
                <!-- Trocar Câmera -->
                <button id="switch-camera" aria-label="Trocar câmera" class="p-3 bg-white bg-opacity-20 rounded-full hover:bg-opacity-30 transition-all text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
                
                <!-- Minimizar -->
                <button id="minimize-call" aria-label="Minimizar chamada" class="p-3 bg-white bg-opacity-20 rounded-full hover:bg-opacity-30 transition-all text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                </button>
                
                <!-- Encerrar Chamada -->
                <button id="end-call" aria-label="Encerrar chamada" class="p-4 bg-red-600 rounded-full hover:bg-red-700 transition-all text-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes scale-in {
        from {
            opacity: 0;
            transform: scale(0.9);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    .animate-scale-in {
        animation: scale-in 0.3s ease-out;
    }
    
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
// Configuração e constantes
@php
    $chatConfig = [
        'lastMessageId' => $chatRoom->messages->max('id') ?? 0,
        'roomCode' => $chatRoom->room_code,
        'sendMessageUrl' => route('chat.send-message', $chatRoom->room_code),
        'senderType' => session('chat_user_role', 'client'),
    ];
@endphp
const DEBUG = @json(config('app.debug', false));
const CHAT_CONFIG = @json($chatConfig);

// Constantes
const CONFIG = {
    MAX_ECHO_ATTEMPTS: 50,
    ECHO_CHECK_INTERVAL: 100,
    MAX_MESSAGE_LENGTH: 1000,
    DEBOUNCE_DELAY: 300,
};

// Função helper para debug
function debugLog(...args) {
    if (DEBUG) {
        if (DEBUG) {
        console.log('[DEBUG]', ...args);
    }
    }
}

// Aguardar Echo estar disponível antes de iniciar
(function() {
    debugLog('Iniciando inicialização do chat...');
    debugLog('Verificando módulos Vite carregados...');
    
    // Aguardar o DOM estar pronto
    function waitForDOM(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);
        } else {
            callback();
        }
    }
    
    // Aguardar Echo estar disponível
    function waitForEcho(callback) {
        let attempts = 0;
        
        function checkEcho() {
            attempts++;
            debugLog(`Tentativa ${attempts}/${CONFIG.MAX_ECHO_ATTEMPTS}: Verificando se Echo está disponível...`);
            
            if (window.Echo && typeof window.Echo.channel === 'function') {
                debugLog('✅ Echo encontrado e funcional!');
                callback();
            } else if (attempts >= CONFIG.MAX_ECHO_ATTEMPTS) {
                console.error('❌ Timeout: Echo não foi carregado após', CONFIG.MAX_ECHO_ATTEMPTS * CONFIG.ECHO_CHECK_INTERVAL, 'ms');
                console.error('Verifique se o websocket.js está sendo importado corretamente em app.js');
                console.error('Verifique se as dependências (laravel-echo, pusher-js) estão instaladas');
                if (window.Echo) {
                    console.warn('⚠️ Tentando usar Echo parcialmente disponível...');
                    callback();
                }
            } else {
                setTimeout(checkEcho, CONFIG.ECHO_CHECK_INTERVAL);
            }
        }
        
        checkEcho();
    }
    
    // Iniciar quando DOM estiver pronto
    waitForDOM(function() {
        debugLog('DOM carregado, aguardando Echo...');
        waitForEcho(function() {
    
    debugLog('========================================');
    debugLog('INICIALIZANDO CHAT - DEBUG COMPLETO');
    debugLog('========================================');
    
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
    
    debugLog('Elementos DOM carregados:', {
        chatForm: !!elements.chatForm,
        messageInput: !!elements.messageInput,
        sendButton: !!elements.sendButton,
        chatMessages: !!elements.chatMessages
    });
    
    // State
    const state = {
        lastMessageId: CHAT_CONFIG.lastMessageId,
        selectedFile: null
    };
    
    debugLog('Estado inicial:', {
        lastMessageId: state.lastMessageId,
        selectedFile: state.selectedFile
    });
    
    // Config
    const config = {
        roomCode: CHAT_CONFIG.roomCode,
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || '',
        sendMessageUrl: CHAT_CONFIG.sendMessageUrl
    };
    
    debugLog('Configuração:', {
        roomCode: config.roomCode,
        sendMessageUrl: config.sendMessageUrl,
        csrfTokenLength: config.csrfToken.length
    });

    // ========== CHAT FUNCTIONS ==========
    
    // Auto-resize textarea
    elements.messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
        elements.charCount.textContent = this.value.length + '/' + CONFIG.MAX_MESSAGE_LENGTH;
    });

    // Função para enviar mensagem - requisição assíncrona em background
    async function sendMessage() {
        debugLog('========================================');
        debugLog('INICIANDO ENVIO DE MENSAGEM');
        debugLog('========================================');
        
        const message = elements.messageInput.value.trim();
        const hasMessage = !!message;
        const hasFile = !!state.selectedFile;
        
        debugLog('Validação de entrada:', {
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
            debugLog('❌ Envio cancelado: mensagem vazia e sem arquivo');
            return;
        }

        debugLog('Preparando FormData...');
        const formData = new FormData();
        formData.append('message', message);
        formData.append('_token', config.csrfToken);
        if (state.selectedFile) {
            formData.append('file', state.selectedFile);
            debugLog('Arquivo adicionado ao FormData:', {
                name: state.selectedFile.name,
                size: state.selectedFile.size,
                type: state.selectedFile.type
            });
        }
        
        debugLog('FormData preparado:', {
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
        debugLog('Botão de enviar desabilitado com animação de loading');

        try {
            debugLog('Enviando requisição HTTP...');
            debugLog('URL:', config.sendMessageUrl);
            debugLog('Método: POST');
            debugLog('Headers:', {
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
            debugLog('✅ Resposta recebida em', requestTime + 'ms');
            debugLog('Status HTTP:', res.status, res.statusText);
            debugLog('Headers da resposta:', Object.fromEntries(res.headers.entries()));
            
            const data = await res.json();
            debugLog('Dados da resposta:', data);
            
            if (data.success) {
                debugLog('✅ Mensagem enviada com sucesso!');
                debugLog('Limpando formulário...');
                
                // Limpar formulário apenas se sucesso
                elements.messageInput.value = '';
                elements.messageInput.style.height = 'auto';
                elements.charCount.textContent = '0/' + CONFIG.MAX_MESSAGE_LENGTH;
                state.selectedFile = null;
                elements.filePreview.classList.add('hidden');
                elements.fileInput.value = '';
                
                debugLog('Formulário limpo');
                debugLog('Aguardando broadcast via Reverb...');
            } else {
                console.error('❌ Erro ao enviar mensagem:', data);
                // TODO: Substituir alert por sistema de notificações
                alert('Erro ao enviar mensagem. Tente novamente.');
            }
        } catch (error) {
            console.error('❌ ERRO na requisição:', error);
            if (DEBUG) {
                console.error('Tipo do erro:', error.name);
                console.error('Mensagem do erro:', error.message);
                console.error('Stack trace:', error.stack);
            }
            // TODO: Substituir alert por sistema de notificações
            alert('Erro ao enviar mensagem. Verifique sua conexão e tente novamente.');
        } finally {
            // Reabilitar botão com animação suave
            debugLog('Reabilitando botão de enviar...');
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
            
            debugLog('========================================');
            debugLog('PROCESSO DE ENVIO FINALIZADO');
            debugLog('========================================');
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
        debugLog('Animação de clique no botão enviar');
        elements.sendButton.classList.add('animate-pulse');
        elements.sendIcon.classList.add('animate-bounce');
        
        setTimeout(() => {
            elements.sendButton.classList.remove('animate-pulse');
            elements.sendIcon.classList.remove('animate-bounce');
        }, CONFIG.DEBOUNCE_DELAY);
        
        sendMessage();
    });
    
    // Enter para enviar (Shift+Enter para nova linha)
    elements.messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            e.stopPropagation();
            
            // Animação visual ao pressionar Enter
            debugLog('Enter pressionado, animando botão...');
            elements.sendButton.classList.add('animate-pulse', 'ring-2', 'ring-blue-300');
            elements.sendIcon.classList.add('animate-bounce');
            
            setTimeout(() => {
                elements.sendButton.classList.remove('animate-pulse', 'ring-2', 'ring-blue-300');
                elements.sendIcon.classList.remove('animate-bounce');
            }, CONFIG.DEBOUNCE_DELAY);
            
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

    // Add message to chat (com escape XSS)
    function addMessageToChat(message) {
        debugLog('========================================');
        debugLog('ADICIONANDO MENSAGEM AO CHAT');
        debugLog('========================================');
        debugLog('Dados da mensagem:', {
            id: message.id,
            senderType: message.sender_type,
            message: message.message,
            filePath: message.file_path,
            createdAt: message.created_at
        });
        
        // Evitar mensagens duplicadas
        if (message.id <= state.lastMessageId) {
            debugLog('⚠️ Mensagem duplicada ignorada:', {
                messageId: message.id,
                lastMessageId: state.lastMessageId
            });
            return;
        }
        
        debugLog('Mensagem é nova, processando...');
        state.lastMessageId = message.id;
        debugLog('lastMessageId atualizado para:', state.lastMessageId);
        
        const messageDiv = document.createElement('div');
        messageDiv.className = 'mb-4 animate-fade-in';
        messageDiv.setAttribute('data-message-id', message.id);
        debugLog('Elemento DIV criado');
        
        const isClient = message.sender_type === 'client';
        debugLog('Tipo de remetente:', {
            senderType: message.sender_type,
            isClient: isClient
        });
        
        // Escape XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        let fileHtml = '';
        if (message.file_path) {
            debugLog('Processando arquivo anexo...');
            const fileExt = message.file_path.split('.').pop().toLowerCase();
            const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExt);
            const safePath = escapeHtml(message.file_path);
            debugLog('Informações do arquivo:', {
                filePath: message.file_path,
                extension: fileExt,
                isImage: isImage
            });
            
            if (isImage) {
                fileHtml = `<div class="mt-2"><img src="/storage/${safePath}" alt="Imagem anexada" class="max-w-xs rounded-lg cursor-pointer" loading="lazy" onclick="window.open('/storage/${safePath}', '_blank')"></div>`;
                debugLog('HTML de imagem gerado');
            } else {
                fileHtml = `<div class="mt-2 p-2 bg-black/10 rounded-lg text-sm flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg> 
                    <a href="/storage/${safePath}" target="_blank" rel="noopener noreferrer" class="hover:underline">Ver anexo (${escapeHtml(fileExt.toUpperCase())})</a>
                </div>`;
                debugLog('HTML de arquivo gerado');
            }
        } else {
            debugLog('Nenhum arquivo anexado');
        }
        
        const messageDate = new Date(message.created_at);
        const timeString = messageDate.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        debugLog('Data formatada:', {
            original: message.created_at,
            parsed: messageDate.toISOString(),
            formatted: timeString
        });
        
        // Escape mensagem para prevenir XSS
        const safeMessage = message.message ? escapeHtml(message.message) : '';
        
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
        
        debugLog('HTML da mensagem gerado, adicionando ao DOM...');
        elements.chatMessages.appendChild(messageDiv);
        debugLog('✅ Mensagem adicionada ao DOM');
        
        scrollToBottom();
        debugLog('Scroll executado');
        debugLog('========================================');
        debugLog('MENSAGEM ADICIONADA COM SUCESSO');
        debugLog('========================================');
    }

    function scrollToBottom() {
        const beforeScroll = elements.chatMessages.scrollTop;
        const scrollHeight = elements.chatMessages.scrollHeight;
        elements.chatMessages.scrollTop = scrollHeight;
        const afterScroll = elements.chatMessages.scrollTop;
        debugLog('Scroll executado:', {
            beforeScroll: beforeScroll,
            scrollHeight: scrollHeight,
            afterScroll: afterScroll,
            scrolled: afterScroll > beforeScroll
        });
    }

    // ========== REVERB WEBSOCKET ==========
    
    debugLog('========================================');
    debugLog('CONFIGURANDO CONEXÃO REVERB');
    debugLog('========================================');
    
    const roomCode = CHAT_CONFIG.roomCode;
    const channelName = `chat-room.${roomCode}`;
    
    debugLog('Configuração do canal:', {
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
        debugLog('Criando conexão com o canal...');
        
        // Forçar conexão antes de criar o canal
        if (window.Echo.connector && window.Echo.connector.pusher) {
            const pusher = window.Echo.connector.pusher;
            if (pusher.connection.state !== 'connected' && pusher.connection.state !== 'connecting') {
                debugLog('Forçando conexão WebSocket...');
                pusher.connect();
            }
        }
        
        const channel = window.Echo.channel(channelName);
        debugLog('✅ Canal criado:', channel);
        
        debugLog('Configurando listener para evento: .chat.message.sent');
        
        channel.listen('.chat.message.sent', (event) => {
            debugLog('========================================');
            debugLog('NOVA MENSAGEM RECEBIDA VIA REVERB');
            debugLog('========================================');
            debugLog('Evento completo:', event);
            debugLog('Timestamp:', new Date().toISOString());
            debugLog('Dados do evento:', {
                hasMessage: !!event.message,
                messageId: event.message?.id,
                senderType: event.message?.sender_type,
                messageText: event.message?.message,
                hasFile: !!event.message?.file_path,
                filePath: event.message?.file_path,
                createdAt: event.message?.created_at
            });
            
            if (event.message) {
                debugLog('Mensagem válida encontrada, processando...');
                debugLog('Estado antes de adicionar:', {
                    lastMessageId: state.lastMessageId,
                    newMessageId: event.message.id
                });
                
                addMessageToChat(event.message);
                
                debugLog('✅ Mensagem adicionada ao chat');
                debugLog('Estado após adicionar:', {
                    lastMessageId: state.lastMessageId
                });
            } else {
                if (DEBUG) {
                    console.warn('⚠️ Evento recebido mas sem campo "message"');
                    console.warn('Estrutura do evento:', Object.keys(event));
                }
            }
            
            debugLog('========================================');
            debugLog('PROCESSAMENTO DE MENSAGEM FINALIZADO');
            debugLog('========================================');
        });
        
        debugLog('✅ Listener configurado com sucesso');
        debugLog('Aguardando mensagens do servidor...');
        
        // Verificar status da conexão (Pusher.js)
        if (window.Echo.connector && window.Echo.connector.pusher) {
            const pusher = window.Echo.connector.pusher;
            const connection = pusher.connection;
            
            debugLog('Status da conexão WebSocket:', {
                state: connection.state,
                socket_id: connection.socket_id || 'não conectado',
                url: pusher.config?.wsHost || 'N/A'
            });
            
            // Verificar se já está conectado
            if (connection.state === 'connected') {
                debugLog('✅ WebSocket já está CONECTADO');
            } else if (connection.state === 'connecting') {
                debugLog('⏳ WebSocket está CONECTANDO...');
            } else {
                debugLog('⚠️ WebSocket não está conectado. Estado:', connection.state);
            }
            
            // Listeners de eventos do Pusher (se ainda não foram adicionados)
            if (!pusher._connectionListenersAdded) {
                connection.bind('connected', () => {
                    debugLog('✅ WebSocket CONECTADO via Pusher');
                });
                
                connection.bind('disconnected', () => {
                    if (DEBUG) {
                        console.warn('⚠️ WebSocket DESCONECTADO');
                    }
                });
                
                connection.bind('error', (error) => {
                    console.error('❌ Erro na conexão WebSocket:', error);
                });
                
                connection.bind('state_change', (states) => {
                    debugLog('Estado da conexão mudou:', states.previous, '->', states.current);
                });
                
                pusher._connectionListenersAdded = true;
            }
        } else {
            if (DEBUG) {
                console.warn('⚠️ Pusher não encontrado no Echo.connector');
                debugLog('Estrutura do Echo.connector:', {
                    hasConnector: !!window.Echo.connector,
                    connectorKeys: window.Echo.connector ? Object.keys(window.Echo.connector) : []
                });
            }
        }
        
    } catch (error) {
        console.error('❌ ERRO ao configurar Reverb:', error);
        if (DEBUG) {
            console.error('Tipo do erro:', error.name);
            console.error('Mensagem do erro:', error.message);
            console.error('Stack trace:', error.stack);
        }
    }
    
    debugLog('========================================');
    debugLog('CONFIGURAÇÃO REVERB FINALIZADA');
    debugLog('========================================');

    // Initial scroll
    debugLog('Executando scroll inicial...');
    scrollToBottom();
    
    debugLog('========================================');
    debugLog('INICIALIZAÇÃO DO CHAT COMPLETA');
    debugLog('========================================');
    debugLog('Sistema pronto para enviar e receber mensagens');
    debugLog('Canal Reverb:', channelName);
    debugLog('========================================');
    
        }); // Fecha waitForEcho
    }); // Fecha waitForDOM
})(); // Fecha função anônima auto-executável

// ============================================
// WEBSOCKET + WEBRTC - SISTEMA DE CHAMADAS
// ============================================
(function() {
    const roomCode = CHAT_CONFIG.roomCode;
    const senderType = CHAT_CONFIG.senderType;
    
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
            debugLog('[Call] Stream remoto recebido');
            remoteVideo.srcObject = stream;
        };
        
        // Callback único consolidado (removido duplicação)
        webrtcManager.onTransmissionModeChanged = (mode) => {
            debugLog('[Call] Modo de transmissão:', mode);
            let modeIndicator = document.getElementById('transmission-mode-indicator');
            if (!modeIndicator) {
                modeIndicator = document.createElement('div');
                modeIndicator.id = 'transmission-mode-indicator';
                modeIndicator.className = 'absolute top-4 left-4 px-3 py-1 rounded-lg text-xs font-medium z-10';
                callMaximized.querySelector('.relative')?.appendChild(modeIndicator) || 
                callMaximized.querySelector('.flex-1')?.appendChild(modeIndicator);
            }
            modeIndicator.classList.remove('hidden');
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
            debugLog('[Call] Câmera trocada:', isFront ? 'frente' : 'trás');
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
        
        debugLog('[Call] WebRTC Manager inicializado');
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
            // TODO: Substituir alert por sistema de notificações
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
            // TODO: Substituir alert por sistema de notificações
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
    
    // Encerrar chamada (com confirmação)
    endCallBtn?.addEventListener('click', () => {
        if (confirm('Deseja realmente encerrar a chamada?')) {
            webrtcManager?.endCall();
        }
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
    
    // Elementos de notificação
    const incomingCallNotification = document.getElementById('incoming-call-notification');
    const acceptCallBtn = document.getElementById('accept-call-btn');
    const rejectCallBtn = document.getElementById('reject-call-btn');
    const incomingCallTitle = document.getElementById('incoming-call-title');
    const incomingCallSubtitle = document.getElementById('incoming-call-subtitle');
    const otherUserStatus = document.getElementById('other-user-status');
    const otherUserName = document.getElementById('other-user-name');
    
    let pendingOffer = null;
    
    // Aceitar chamada
    acceptCallBtn?.addEventListener('click', async () => {
        incomingCallNotification.classList.add('hidden');
        
        if (pendingOffer) {
            try {
                if (!webrtcManager) initWebRTC();
                
                await webrtcManager.acceptCall(pendingOffer);
                showCallInterface();
                localVideo.srcObject = webrtcManager.localStream;
                startCallTimer();
                
                // Enviar ação de aceitar via WebSocket
                webrtcManager.sendSignal('call_action', {
                    action: 'accepted',
                    timestamp: Date.now()
                });
            } catch (error) {
                console.error('[Call] Erro ao aceitar chamada:', error);
                alert('Erro ao aceitar chamada. Tente novamente.');
            }
            pendingOffer = null;
        }
    });
    
    // Recusar chamada
    rejectCallBtn?.addEventListener('click', () => {
        incomingCallNotification.classList.add('hidden');
        
        if (pendingOffer) {
            // Enviar ação de recusar via WebSocket
            if (webrtcManager) {
                webrtcManager.sendSignal('call_action', {
                    action: 'rejected',
                    timestamp: Date.now()
                });
            } else {
                // Se não tem manager ainda, criar temporariamente para enviar sinal
                const tempManager = new WebRTCManager(roomCode, senderType);
                tempManager.sendSignal('call_action', {
                    action: 'rejected',
                    timestamp: Date.now()
                });
            }
            pendingOffer = null;
        }
    });
    
    // Escutar ofertas de chamada via WebSocket (consolidado)
    if (window.Echo) {
        const channel = window.Echo.channel(`chat-room.${roomCode}`);
        
        // Listener único consolidado para todos os eventos
        channel.listen('.call.signal', (event) => {
            try {
                const { type, payload, sender_type } = event.data;
                
                // Ignorar sinais do próprio usuário
                if (sender_type === senderType) return;
                
                // Processar offer de chamada
                if (type === 'offer') {
                    pendingOffer = payload;
                    incomingCallTitle.textContent = 'Chamada Recebida';
                    incomingCallSubtitle.textContent = sender_type === 'client' ? 'Cliente está chamando...' : 'Prestador está chamando...';
                    incomingCallNotification.classList.remove('hidden');
                }
                
                // Processar ações de chamada
                if (type === 'call_action') {
                    if (payload.action === 'accepted') {
                        debugLog('[Call] Chamada aceita pelo outro usuário');
                    } else if (payload.action === 'rejected') {
                        debugLog('[Call] Chamada recusada pelo outro usuário');
                        if (callOverlay && !callOverlay.classList.contains('hidden')) {
                            endCall();
                        }
                        // TODO: Substituir alert por sistema de notificações
                        alert('Chamada recusada pelo outro usuário');
                    } else if (payload.action === 'video_toggled') {
                        debugLog('[Call] Vídeo alterado pelo outro usuário:', payload.enabled);
                    } else if (payload.action === 'audio_toggled') {
                        debugLog('[Call] Áudio alterado pelo outro usuário:', payload.enabled);
                    } else if (payload.action === 'camera_switched') {
                        debugLog('[Call] Câmera trocada pelo outro usuário');
                    }
                }
                
                // Processar eventos de presença (status online)
                if (type === 'user_presence' && sender_type !== senderType) {
                    const status = payload.status;
                    const statusIndicator = otherUserStatus?.querySelector('.w-2');
                    if (statusIndicator) {
                        if (status === 'online') {
                            statusIndicator.classList.remove('bg-gray-400');
                            statusIndicator.classList.add('bg-green-400', 'animate-pulse');
                        } else {
                            statusIndicator.classList.remove('bg-green-400', 'animate-pulse');
                            statusIndicator.classList.add('bg-gray-400');
                        }
                    }
                }
            } catch (error) {
                console.error('[Call] Erro ao processar sinal:', error);
            }
        });
    }
    
    // Enviar status de presença quando entrar na sala
    function sendPresence(status) {
        if (!window.Echo) return;
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        fetch(`/chat/${roomCode}/call-signal`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                type: 'user_presence',
                payload: { status: status },
                sender_type: senderType
            })
        }).catch(error => {
            console.error('[Presence] Erro ao enviar status:', error);
        });
    }
    
    // Enviar presença online ao carregar
    if (window.Echo) {
        sendPresence('online');
        initWebRTC();
        
        // Enviar presença offline ao sair
        window.addEventListener('beforeunload', () => {
            sendPresence('offline');
        });
    } else {
        // Aguardar Echo (usando Promise com timeout)
        let attempts = 0;
        const maxAttempts = CONFIG.MAX_ECHO_ATTEMPTS;
        
        const checkEcho = setInterval(() => {
            attempts++;
            if (window.Echo) {
                clearInterval(checkEcho);
                sendPresence('online');
                initWebRTC();
            } else if (attempts >= maxAttempts) {
                clearInterval(checkEcho);
                console.error('Echo não disponível após', maxAttempts * CONFIG.ECHO_CHECK_INTERVAL, 'ms');
            }
        }, CONFIG.ECHO_CHECK_INTERVAL);
    }
})();
</script>

@section('scripts')
<script src="{{ asset('js/webrtc-manager.js') }}"></script>
@endsection