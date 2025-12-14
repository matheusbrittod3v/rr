@extends('layouts.app')

@section('title', 'Chat - Renttool')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-4 sm:py-8 relative">
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
                        <!-- Call Buttons -->
                        <button id="voice-call-btn" class="p-2 rounded-full bg-white/10 hover:bg-white/20 text-white transition-all backdrop-blur-sm" title="Chamada de Voz">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </button>
                        <button id="video-call-btn" class="p-2 rounded-full bg-white/10 hover:bg-white/20 text-white transition-all backdrop-blur-sm" title="Chamada de Vídeo">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </button>

                        <span class="hidden sm:inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 text-white backdrop-blur-sm ml-2">
                            {{ $chatRoom->room_code }}
                        </span>
                        <a href="{{ url('/') }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-white/10 text-white hover:bg-white/20 transition-all backdrop-blur-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Sair
                        </a>
                    </div>
                </div>
            </div>

            <!-- Participants Info & Messages (Same as before) -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Client -->
                    <div class="flex items-center space-x-3 bg-white rounded-xl p-3 shadow-sm">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-bold text-sm">C</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-gray-900">Cliente</p>
                            @if($client)
                                <p class="text-xs text-gray-600 truncate">{{ $client['email'] }}</p>
                                <p class="text-xs text-gray-500">{{ $client['whatsapp'] }}</p>
                            @else
                                <p class="text-xs text-gray-500">Informações não disponíveis</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Provider -->
                    <div class="flex items-center space-x-3 bg-white rounded-xl p-3 shadow-sm">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-bold text-sm">P</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-gray-900">Prestador</p>
                            @if($provider)
                                <p class="text-xs text-gray-600 truncate">{{ $provider['name'] }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $provider['email'] }}</p>
                            @else
                                <p class="text-xs text-gray-500">Aguardando atribuição</p>
                            @endif
                        </div>
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
                                    @if($message->sender_type === 'admin')
                                        <div class="w-6 h-6 rounded-full bg-purple-600 flex items-center justify-center text-xs text-white" title="Admin">A</div>
                                    @endif

                                    <div class="px-4 py-3 rounded-2xl shadow-sm {{ 
                                        $message->sender_type === 'client' ? 'bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-br-sm' : 
                                        ($message->sender_type === 'admin' ? 'bg-purple-100 text-purple-900 border border-purple-200 rounded-bl-sm' : 
                                        'bg-white text-gray-800 border border-gray-200 rounded-bl-sm') 
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
                                        
                                        <p class="text-xs mt-1.5 {{ $message->sender_type === 'client' ? 'text-blue-100' : ($message->sender_type === 'admin' ? 'text-purple-400' : 'text-gray-500') }}">
                                            @if($message->sender_type === 'admin') <strong>Admin</strong> • @endif
                                            {{ $message->created_at->format('H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="bg-white border-t border-gray-200 p-4">
                <form id="chat-form" class="flex items-end space-x-3">
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
                        <button type="submit" id="send-button" class="p-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all hover:scale-105 shadow-lg" title="Enviar mensagem">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
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

    <!-- Incoming Call Modal -->
    <div id="incoming-call-modal" class="hidden fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-2xl p-8 max-w-sm w-full text-center shadow-2xl animate-fade-in border-4 border-blue-500/30">
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Chamada Recebida</h3>
            <p class="text-gray-500 mb-8">Alguém deseja iniciar uma chamada de vídeo/voz com você.</p>
            
            <div class="flex space-x-4 justify-center">
                <button id="reject-call-btn" class="px-6 py-3 bg-red-100 text-red-600 rounded-xl font-semibold hover:bg-red-200 transition-colors flex-1">
                    Recusar
                </button>
                <button id="accept-call-btn" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition-colors flex-1 shadow-lg shadow-blue-500/30">
                    Aceitar
                </button>
            </div>
        </div>
    </div>

    <!-- Video Call Overlay -->
    <div id="call-overlay" class="hidden fixed inset-0 z-50 bg-gray-900 flex items-center justify-center p-4">
        <div class="relative w-full h-full max-w-5xl bg-black rounded-2xl overflow-hidden shadow-2xl">
            <!-- Remote Video -->
            <video id="remote-video" autoplay playsinline class="w-full h-full object-cover"></video>
            
            <!-- Local Video (PIP) -->
            <div class="absolute top-4 right-4 w-32 h-48 sm:w-48 sm:h-64 bg-gray-800 rounded-xl overflow-hidden shadow-lg border-2 border-white/20">
                <video id="local-video" autoplay playsinline muted class="w-full h-full object-cover transform scale-x-[-1]"></video>
            </div>

            <!-- Status Message -->
            <div id="call-status" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-white bg-black/50 px-6 py-3 rounded-full backdrop-blur-md hidden text-center pointer-events-none">
                <div class="animate-pulse mb-2 font-semibold">Conectando...</div>
                <div class="text-xs opacity-70">Aguardando resposta...</div>
            </div>

            <!-- Call Timer -->
            <div id="call-timer" class="absolute top-4 left-4 bg-black/50 px-4 py-2 rounded-full backdrop-blur-md text-white text-sm font-semibold hidden">
                <span id="timer-display">00:00</span>
            </div>

            <!-- Call Controls -->
            <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex items-center space-x-4 p-4 rounded-2xl bg-black/20 backdrop-blur-md">
                <button id="toggle-mic-btn" class="p-4 rounded-full bg-white/10 hover:bg-white/20 text-white transition-all">
                    <svg id="mic-on-icon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                    </svg>
                    <svg id="mic-off-icon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M15 10.586V6a3 3 0 00-3-3H9" />
                    </svg>
                </button>
                <button id="toggle-video-btn" class="p-4 rounded-full bg-white/10 hover:bg-white/20 text-white transition-all">
                     <svg id="video-on-icon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                     </svg>
                     <svg id="video-off-icon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                     </svg>
                </button>
                <button id="switch-camera-btn" class="p-4 rounded-full bg-white/10 hover:bg-white/20 text-white transition-all" title="Trocar câmera">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
                <button id="switch-to-voice-btn" class="p-4 rounded-full bg-white/10 hover:bg-white/20 text-white transition-all hidden" title="Trocar para voz">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                </button>
                <button id="minimize-call-btn" class="p-4 rounded-full bg-white/10 hover:bg-white/20 text-white transition-all" title="Minimizar">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                </button>
                <button id="hangup-btn" class="p-4 rounded-full bg-red-600 hover:bg-red-700 text-white shadow-lg transform hover:scale-110 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Minimized Call Widget -->
    <div id="minimized-call-widget" class="hidden fixed bottom-4 right-4 z-50 bg-gray-900 rounded-xl overflow-hidden shadow-2xl border-2 border-blue-500/50 cursor-pointer" style="width: 200px; height: 150px;">
        <video id="minimized-remote-video" autoplay playsinline class="w-full h-full object-cover"></video>
        <div class="absolute top-2 right-2 flex space-x-1">
            <button id="unmute-minimized" class="p-1.5 rounded-full bg-black/50 text-white text-xs">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.617.793L4.383 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.383l4-3.617a1 1 0 011.617.793zM14.657 2.929a1 1 0 011.414 0A9.972 9.972 0 0119 10a9.972 9.972 0 01-2.929 7.071 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 10c0-2.21-.894-4.208-2.343-5.657a1 1 0 010-1.414zm-2.829 2.828a1 1 0 011.415 0A5.983 5.983 0 0115 10a5.984 5.984 0 01-1.757 4.243 1 1 0 01-1.415-1.415A3.984 3.984 0 0013 10a3.983 3.983 0 00-1.172-2.828 1 1 0 010-1.415z"/></svg>
            </button>
        </div>
        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-2">
            <p class="text-white text-xs font-semibold text-center">Chamada em andamento</p>
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
    #chat-messages::-webkit-scrollbar { width: 6px; }
    #chat-messages::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
    #chat-messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    #minimized-call-widget {
        transition: all 0.3s ease;
    }
    #minimized-call-widget:hover {
        transform: scale(1.05);
        border-color: rgba(59, 130, 246, 0.8);
    }
    #call-overlay video {
        background: #000;
    }
    @media (max-width: 640px) {
        #minimized-call-widget {
            width: 150px;
            height: 112px;
        }
    }
</style>

{{-- Inicializar Echo via Vite --}}
@vite(['resources/js/app.js'])

<script>
// Aguardar Echo estar disponível antes de iniciar
(function() {
    // Aguardar até que window.Echo esteja disponível
    function waitForEcho(callback) {
        if (window.Echo) {
            callback();
        } else {
            setTimeout(() => waitForEcho(callback), 100);
        }
    }
    
    waitForEcho(function() {
    
    // UI Elements
    const elements = {
        chatForm: document.getElementById('chat-form'),
        messageInput: document.getElementById('message-input'),
        fileInput: document.getElementById('file-input'),
        attachButton: document.getElementById('attach-button'),
        sendButton: document.getElementById('send-button'),
        chatMessages: document.getElementById('chat-messages'),
        filePreview: document.getElementById('file-preview'),
        fileName: document.getElementById('file-name'),
        removeFileBtn: document.getElementById('remove-file'),
        charCount: document.getElementById('char-count'),
        voiceCallBtn: document.getElementById('voice-call-btn'),
        videoCallBtn: document.getElementById('video-call-btn'),
        callOverlay: document.getElementById('call-overlay'),
        incomingCallModal: document.getElementById('incoming-call-modal'),
        acceptCallBtn: document.getElementById('accept-call-btn'),
        rejectCallBtn: document.getElementById('reject-call-btn'),
        remoteVideo: document.getElementById('remote-video'),
        localVideo: document.getElementById('local-video'),
        minimizedRemoteVideo: document.getElementById('minimized-remote-video'),
        minimizedCallWidget: document.getElementById('minimized-call-widget'),
        callStatus: document.getElementById('call-status'),
        toggleMicBtn: document.getElementById('toggle-mic-btn'),
        toggleVideoBtn: document.getElementById('toggle-video-btn'),
        switchCameraBtn: document.getElementById('switch-camera-btn'),
        switchToVoiceBtn: document.getElementById('switch-to-voice-btn'),
        minimizeCallBtn: document.getElementById('minimize-call-btn'),
        hangupBtn: document.getElementById('hangup-btn'),
        micOnIcon: document.getElementById('mic-on-icon'),
        micOffIcon: document.getElementById('mic-off-icon'),
        videoOnIcon: document.getElementById('video-on-icon'),
        videoOffIcon: document.getElementById('video-off-icon'),
        callTimer: document.getElementById('call-timer'),
        timerDisplay: document.getElementById('timer-display')
    };
    
    // State
    const state = {
        inCall: false,
        isCallMinimized: false,
        isVideoCall: false,
        isMuted: false,
        isVideoEnabled: true,
        lastMessageId: {{ $chatRoom->messages->max('id') ?? 0 }},
        lastSignalId: 0,
        localStream: null,
        remoteStream: null,
        peerConnection: null,
        currentCamera: 'user', // 'user' (front) or 'environment' (back)
        selectedFile: null,
        callStartTime: null,
        callTimerInterval: null,
        trackMonitorInterval: null, // Monitor tracks to ensure they stay active
        processedSignalIds: new Set(), // Track processed signals to avoid duplicates
        isAcceptingCall: false, // Prevent multiple accept attempts
        connectionTimeout: null, // Timeout to detect stuck connections
        answerReceived: false, // Track if answer was received
        pendingCandidates: [] // Queue for ICE candidates received before remote description
    };
    
    // Detect mobile device
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || 
                     (navigator.maxTouchPoints && navigator.maxTouchPoints > 2 && /MacIntel/.test(navigator.platform));
    
    console.log('Device type:', isMobile ? 'Mobile' : 'Desktop');
    
    // WebRTC Config with STUN and TURN servers for better connectivity
    // TURN servers are essential for NAT traversal in complex network scenarios
    const rtcConfig = {
        iceServers: [
            // STUN servers (for discovery)
            { urls: 'stun:stun.l.google.com:19302' },
            { urls: 'stun:stun1.l.google.com:19302' },
            { urls: 'stun:stun2.l.google.com:19302' },
            { urls: 'stun:stun.stunprotocol.org:3478' },
            // Public TURN servers (for relay when direct connection fails)
            // Note: These are public servers with limited bandwidth, but help with NAT traversal
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
            }
        ],
        iceCandidatePoolSize: isMobile ? 20 : 10, // More candidates for mobile
        iceTransportPolicy: 'all', // Use both relay and host candidates (important!)
        bundlePolicy: 'max-bundle', // Better for mobile
        rtcpMuxPolicy: 'require' // Required for mobile compatibility
    };
    
    // Config
    const config = {
        roomCode: "{{ $chatRoom->room_code }}",
        csrfToken: "{{ csrf_token() }}",
        sendMessageUrl: "{{ route('chat.send-message', $chatRoom->room_code) }}",
        getMessagesUrl: "{{ route('chat.get-messages', $chatRoom->room_code) }}",
        signalsUrl: "{{ route('chat.signals', $chatRoom->room_code) }}",
        sendSignalUrl: "{{ route('chat.signal', $chatRoom->room_code) }}"
    };

    // ========== CHAT FUNCTIONS ==========
    
    // Auto-resize textarea
    elements.messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
        elements.charCount.textContent = this.value.length + '/1000';
    });

    // Send message
    elements.chatForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const message = elements.messageInput.value.trim();
        if (!message && !state.selectedFile) return;

        const formData = new FormData();
        formData.append('message', message);
        formData.append('_token', config.csrfToken);
        if (state.selectedFile) {
            formData.append('file', state.selectedFile);
        }

        try {
            const res = await fetch(config.sendMessageUrl, {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                elements.messageInput.value = '';
                elements.messageInput.style.height = 'auto';
                elements.charCount.textContent = '0/1000';
                state.selectedFile = null;
                elements.filePreview.classList.add('hidden');
                elements.fileInput.value = '';
            }
        } catch (error) {
            console.error('Error sending message:', error);
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
        const messageDiv = document.createElement('div');
        messageDiv.className = 'mb-4 animate-fade-in';
        messageDiv.setAttribute('data-message-id', message.id);
        
        const isClient = message.sender_type === 'client';
        const isAdmin = message.sender_type === 'admin';
        
        let fileHtml = '';
        if (message.file_path) {
            const fileExt = message.file_path.split('.').pop().toLowerCase();
            const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExt);
            
            if (isImage) {
                fileHtml = `<div class="mt-2"><img src="/storage/${message.file_path}" alt="Imagem" class="max-w-xs rounded-lg cursor-pointer" onclick="window.open('/storage/${message.file_path}', '_blank')"></div>`;
            } else {
                fileHtml = `<div class="mt-2 p-2 bg-black/10 rounded-lg text-sm flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg> 
                    <a href="/storage/${message.file_path}" target="_blank" class="hover:underline">Ver anexo (${fileExt.toUpperCase()})</a>
                </div>`;
            }
        }
        
        messageDiv.innerHTML = `
            <div class="flex ${isClient ? 'justify-end' : 'justify-start'}">
                <div class="max-w-xs sm:max-w-md lg:max-w-lg">
                    <div class="flex items-end gap-2 ${isClient ? 'flex-row-reverse' : 'flex-row'}">
                        ${isAdmin ? '<div class="w-6 h-6 rounded-full bg-purple-600 flex items-center justify-center text-xs text-white" title="Admin">A</div>' : ''}
                        <div class="px-4 py-3 rounded-2xl shadow-sm ${
                            isClient ? 'bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-br-sm' : 
                            (isAdmin ? 'bg-purple-100 text-purple-900 border border-purple-200 rounded-bl-sm' : 
                            'bg-white text-gray-800 border border-gray-200 rounded-bl-sm')
                        }">
                            ${message.message ? `<p class="text-sm break-words">${message.message}</p>` : ''}
                            ${fileHtml}
                            <p class="text-xs mt-1.5 ${isClient ? 'text-blue-100' : (isAdmin ? 'text-purple-400' : 'text-gray-500')}">
                                ${isAdmin ? '<strong>Admin</strong> • ' : ''}
                                ${new Date(message.created_at).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        elements.chatMessages.appendChild(messageDiv);
        scrollToBottom();
    }

    function scrollToBottom() {
        elements.chatMessages.scrollTop = elements.chatMessages.scrollHeight;
    }

    // ========== WEBRTC FUNCTIONS ==========

    async function createPeerConnection() {
        state.peerConnection = new RTCPeerConnection(rtcConfig);
        
        // Add local stream tracks
        if (state.localStream) {
            // Get available tracks
            const availableAudioTracks = state.localStream.getAudioTracks();
            const availableVideoTracks = state.localStream.getVideoTracks();
            
            // Determine which tracks to add based on call type and availability
            let tracksToAdd = [];
            
            if (state.isVideoCall) {
                // Video call: add whatever we have (audio, video, or both)
                tracksToAdd = [...availableAudioTracks, ...availableVideoTracks];
                console.log('Video call - adding all available tracks');
            } else {
                // Voice call: prefer audio, but if no audio, use video (better than nothing)
                if (availableAudioTracks.length > 0) {
                    tracksToAdd = availableAudioTracks;
                    console.log('Voice call - adding audio tracks only');
                } else if (availableVideoTracks.length > 0) {
                    // No audio available, but we have video - use it (user might have only camera)
                    tracksToAdd = availableVideoTracks;
                    console.log('Voice call - no audio available, using video tracks instead');
                }
            }
            
            console.log('Adding local tracks to peer connection:', {
                totalTracks: state.localStream.getTracks().length,
                videoTracks: availableVideoTracks.length,
                audioTracks: availableAudioTracks.length,
                tracksToAdd: tracksToAdd.length,
                isVideoCall: state.isVideoCall,
                tracksToAddTypes: tracksToAdd.map(t => t.kind)
            });
            
            if (tracksToAdd.length === 0) {
                console.warn('No tracks available to add to peer connection!');
            }
            
            tracksToAdd.forEach(track => {
                console.log(`Adding local ${track.kind} track:`, {
                    enabled: track.enabled,
                    readyState: track.readyState,
                    muted: track.muted
                });
                const sender = state.peerConnection.addTrack(track, state.localStream);
                console.log(`Track added, sender:`, {
                    track: sender.track?.kind,
                    transport: sender.transport?.state
                });
            });
            
            // If it's a voice call but we have video tracks in stream (and we're not using them), stop them
            if (!state.isVideoCall && availableVideoTracks.length > 0 && availableAudioTracks.length > 0) {
                console.log('Voice call detected - stopping unused video tracks from local stream');
                availableVideoTracks.forEach(track => {
                    track.stop();
                    console.log('Stopped video track for voice call');
                });
            }
            
            // Ensure transceivers are configured for sendrecv (bidirectional)
            const transceivers = state.peerConnection.getTransceivers();
            transceivers.forEach(transceiver => {
                if (transceiver.direction !== 'sendrecv') {
                    console.log(`Setting transceiver direction to sendrecv for ${transceiver.receiver.track?.kind || 'unknown'}`);
                    transceiver.direction = 'sendrecv';
                }
            });
        } else {
            console.warn('No local stream available when creating peer connection');
        }
        
        // Handle remote stream - improved for mobile compatibility
        state.peerConnection.ontrack = (event) => {
            console.log('Received remote track:', {
                kind: event.track.kind,
                id: event.track.id,
                streams: event.streams.length,
                trackEnabled: event.track.enabled,
                trackReadyState: event.track.readyState,
                transceiver: event.transceiver?.currentDirection,
                receiver: event.receiver?.track?.kind
            });
            
            // Always ensure we have a remote stream
            if (!state.remoteStream) {
                state.remoteStream = new MediaStream();
                console.log('Created new remote stream');
            }
            
            // Add track to our remote stream (always add, even if it exists in event.streams)
            // This ensures we capture all tracks even if they arrive separately
            const existingTrack = state.remoteStream.getTracks().find(t => 
                t.id === event.track.id || 
                (t.kind === event.track.kind && t.readyState === 'live')
            );
            
            if (!existingTrack) {
                state.remoteStream.addTrack(event.track);
                console.log(`Added ${event.track.kind} track to remote stream. Total tracks:`, {
                    video: state.remoteStream.getVideoTracks().length,
                    audio: state.remoteStream.getAudioTracks().length
                });
            } else {
                console.log(`Track ${event.track.kind} already in stream, skipping duplicate`);
            }
            
            // Also use stream from event if available (for compatibility)
            let eventStream = null;
            if (event.streams && event.streams.length > 0) {
                eventStream = event.streams[0];
                // Add any tracks from event stream that we don't have yet
                eventStream.getTracks().forEach(track => {
                    const exists = state.remoteStream.getTracks().some(t => t.id === track.id);
                    if (!exists) {
                        state.remoteStream.addTrack(track);
                        console.log(`Added track from event stream: ${track.kind}`);
                    }
                });
            }
            
            // Assign to video elements - ensure stream is always updated
            const remoteStream = state.remoteStream;
            if (remoteStream && remoteStream.getTracks().length > 0) {
                console.log('Assigning remote stream to video elements...', {
                    videoTracks: remoteStream.getVideoTracks().length,
                    audioTracks: remoteStream.getAudioTracks().length,
                    totalTracks: remoteStream.getTracks().length
                });
                
                // Configure video elements for mobile compatibility
                elements.remoteVideo.autoplay = true;
                elements.remoteVideo.playsInline = true;
                elements.remoteVideo.muted = false; // CRITICAL: Ensure audio plays
                elements.remoteVideo.setAttribute('playsinline', 'true');
                elements.remoteVideo.setAttribute('webkit-playsinline', 'true');
                
                elements.minimizedRemoteVideo.autoplay = true;
                elements.minimizedRemoteVideo.playsInline = true;
                elements.minimizedRemoteVideo.muted = false;
                elements.minimizedRemoteVideo.setAttribute('playsinline', 'true');
                elements.minimizedRemoteVideo.setAttribute('webkit-playsinline', 'true');
                
                // Always update srcObject to ensure latest tracks are used
                // This is important when tracks arrive separately (common in mobile)
                elements.remoteVideo.srcObject = remoteStream;
                elements.minimizedRemoteVideo.srcObject = remoteStream;
                
                console.log('Stream assigned to video elements');
                
                // Force play with retries - critical for mobile
                const playDelay = isMobile ? 500 : 200;
                const maxRetries = isMobile ? 8 : 5;
                
                const tryPlayVideo = (videoElement, elementName, retryCount = 0) => {
                    // Check if element has tracks
                    const srcObject = videoElement.srcObject;
                    if (srcObject) {
                        const hasVideoTracks = srcObject.getVideoTracks().length > 0;
                        const hasAudioTracks = srcObject.getAudioTracks().length > 0;
                        
                        if (!hasVideoTracks && !hasAudioTracks) {
                            console.warn(`${elementName}: No tracks in stream yet, waiting...`);
                            if (retryCount < maxRetries) {
                                setTimeout(() => tryPlayVideo(videoElement, elementName, retryCount + 1), playDelay);
                            }
                            return;
                        }
                        
                        // Verify tracks are live/ready
                        const tracks = srcObject.getTracks();
                        const liveTracks = tracks.filter(t => t.readyState === 'live' || t.readyState === 'ended');
                        if (liveTracks.length === 0 && retryCount < maxRetries) {
                            console.log(`${elementName}: Waiting for tracks to become live...`);
                            setTimeout(() => tryPlayVideo(videoElement, elementName, retryCount + 1), playDelay);
                            return;
                        }
                    }
                    
                    // Try to play
                    if (videoElement.paused || videoElement.readyState < 2) {
                        videoElement.play().then(() => {
                            console.log(`${elementName} started playing successfully`, {
                                paused: videoElement.paused,
                                readyState: videoElement.readyState,
                                muted: videoElement.muted,
                                hasVideo: srcObject?.getVideoTracks().length > 0,
                                hasAudio: srcObject?.getAudioTracks().length > 0
                            });
                            
                            // For audio tracks, ensure volume is not zero
                            if (videoElement.volume === 0) {
                                videoElement.volume = 1.0;
                                console.log(`${elementName}: Volume was 0, set to 1.0`);
                            }
                        }).catch(err => {
                            // Ignore AbortError (interrupted by new load)
                            if (err.name !== 'AbortError' && retryCount < maxRetries) {
                                console.warn(`${elementName} play failed (attempt ${retryCount + 1}/${maxRetries}):`, err.name, err.message);
                                setTimeout(() => {
                                    tryPlayVideo(videoElement, elementName, retryCount + 1);
                                }, playDelay * (retryCount + 1));
                            } else if (err.name !== 'AbortError') {
                                console.error(`Error playing ${elementName} after ${maxRetries} retries:`, err);
                            }
                        });
                    } else {
                        console.log(`${elementName} is already playing`);
                    }
                };
                
                // Start trying to play after a short delay
                setTimeout(() => {
                    tryPlayVideo(elements.remoteVideo, 'Remote video');
                    tryPlayVideo(elements.minimizedRemoteVideo, 'Minimized remote video');
                }, playDelay);
                
                // Add event listeners to detect when video starts and update stream if needed
                if (!elements.remoteVideo.hasAttribute('data-listener-added')) {
                    elements.remoteVideo.setAttribute('data-listener-added', 'true');
                    
                    elements.remoteVideo.addEventListener('playing', () => {
                        console.log('Remote video is now playing', {
                            hasVideo: remoteStream.getVideoTracks().length > 0,
                            hasAudio: remoteStream.getAudioTracks().length > 0,
                            muted: elements.remoteVideo.muted
                        });
                    });
                    
                    elements.remoteVideo.addEventListener('loadedmetadata', () => {
                        console.log('Remote video metadata loaded');
                        // Ensure we have the latest stream
                        if (state.remoteStream && elements.remoteVideo.srcObject !== state.remoteStream) {
                            elements.remoteVideo.srcObject = state.remoteStream;
                        }
                        // Try to play when metadata is loaded
                        if (elements.remoteVideo.paused) {
                            elements.remoteVideo.play().catch(err => {
                                if (err.name !== 'AbortError') {
                                    console.warn('Play on loadedmetadata failed:', err);
                                }
                            });
                        }
                    });
                    
                    // Listen for track additions to stream
                    remoteStream.addEventListener('addtrack', (event) => {
                        console.log('Track added to remote stream:', event.track.kind);
                        // Update video element srcObject to ensure it has the new track
                        if (elements.remoteVideo.srcObject !== remoteStream) {
                            elements.remoteVideo.srcObject = remoteStream;
                        }
                        if (elements.minimizedRemoteVideo.srcObject !== remoteStream) {
                            elements.minimizedRemoteVideo.srcObject = remoteStream;
                        }
                    });
                }
                
                // Log track states
                remoteStream.getTracks().forEach(track => {
                    console.log(`Remote ${track.kind} track:`, {
                        enabled: track.enabled,
                        readyState: track.readyState,
                        muted: track.muted
                    });
                });
            }
            
            // Start timer when we receive first track
            if (state.inCall && !state.callStartTime) {
                console.log('First remote track received, starting timer');
                startCallTimer();
            }
        };
        
        // Handle ICE candidates
        state.peerConnection.onicecandidate = (event) => {
            if (event.candidate) {
                console.log('Sending ICE candidate:', {
                    candidate: event.candidate.candidate,
                    sdpMLineIndex: event.candidate.sdpMLineIndex
                });
                sendSignal('candidate', event.candidate);
            } else {
                console.log('ICE candidate gathering completed');
            }
        };
        
        // Handle connection state
        state.peerConnection.onconnectionstatechange = () => {
            const connectionState = state.peerConnection.connectionState;
            console.log('Connection state changed:', connectionState);
            
            if (connectionState === 'connected') {
                console.log('Peer connection connected!');
                
                // Clear connection timeout
                if (state.connectionTimeout) {
                    clearTimeout(state.connectionTimeout);
                    state.connectionTimeout = null;
                }
                
                updateCallStatus('Conectado');
                
                // Verify transceivers and ensure they're configured correctly
                const transceivers = state.peerConnection.getTransceivers();
                console.log('Transceivers after connection:', transceivers.map(t => ({
                    kind: t.receiver.track?.kind || t.sender.track?.kind,
                    direction: t.direction,
                    currentDirection: t.currentDirection,
                    receiverTrack: t.receiver.track ? {
                        kind: t.receiver.track.kind,
                        enabled: t.receiver.track.enabled,
                        readyState: t.receiver.track.readyState
                    } : null
                })));
                
                // Collect all receiver tracks and ensure they're in the remote stream
                transceivers.forEach(transceiver => {
                    if (transceiver.receiver && transceiver.receiver.track) {
                        const track = transceiver.receiver.track;
                        if (!state.remoteStream) {
                            state.remoteStream = new MediaStream();
                        }
                        
                        // Check if track is already in stream
                        const exists = state.remoteStream.getTracks().some(t => t.id === track.id);
                        if (!exists) {
                            state.remoteStream.addTrack(track);
                            console.log(`Added ${track.kind} track from transceiver to remote stream`);
                        }
                    }
                });
                
                // Check and update remote stream
                if (state.remoteStream) {
                    const videoTracks = state.remoteStream.getVideoTracks();
                    const audioTracks = state.remoteStream.getAudioTracks();
                    
                    console.log('Remote stream state:', {
                        videoTracks: videoTracks.length,
                        audioTracks: audioTracks.length,
                        videoTracksEnabled: videoTracks.filter(t => t.enabled).length,
                        audioTracksEnabled: audioTracks.filter(t => t.enabled).length,
                        videoTracksLive: videoTracks.filter(t => t.readyState === 'live').length,
                        audioTracksLive: audioTracks.filter(t => t.readyState === 'live').length
                    });
                    
                    // Ensure video elements have the latest stream
                    if (elements.remoteVideo.srcObject !== state.remoteStream) {
                        elements.remoteVideo.srcObject = state.remoteStream;
                    }
                    if (elements.minimizedRemoteVideo.srcObject !== state.remoteStream) {
                        elements.minimizedRemoteVideo.srcObject = state.remoteStream;
                    }
                    
                    // Ensure audio plays - critical for mobile
                    elements.remoteVideo.muted = false;
                    elements.remoteVideo.volume = 1.0;
                    if (elements.remoteVideo.paused) {
                        elements.remoteVideo.play().catch(err => {
                            console.warn('Auto-play failed, user interaction may be required:', err);
                        });
                    }
                    
                    // Log track details for debugging
                    audioTracks.forEach((track, index) => {
                        console.log(`Audio track ${index}:`, {
                            id: track.id,
                            enabled: track.enabled,
                            readyState: track.readyState,
                            muted: track.muted,
                            settings: track.getSettings()
                        });
                    });
                } else {
                    console.warn('No remote stream available after connection - this may indicate a problem');
                }
                
                startCallTimer();
                
                // Monitor tracks to ensure they stay active
                if (!state.trackMonitorInterval) {
                    state.trackMonitorInterval = setInterval(() => {
                        if (!state.peerConnection || !state.inCall) {
                            if (state.trackMonitorInterval) {
                                clearInterval(state.trackMonitorInterval);
                                state.trackMonitorInterval = null;
                            }
                            return;
                        }
                        
                        // Check remote stream tracks
                        if (state.remoteStream) {
                            const tracks = state.remoteStream.getTracks();
                            const activeTracks = tracks.filter(t => 
                                t.readyState === 'live' && t.enabled
                            );
                            
                            // If we have active tracks but connection says failed, try to keep it going
                            if (activeTracks.length > 0 && 
                                state.peerConnection.connectionState === 'failed') {
                                console.log('Tracks are active despite failed connection state - attempting recovery');
                                try {
                                    state.peerConnection.restartIce();
                                } catch (err) {
                                    console.warn('Error restarting ICE in monitor:', err);
                                }
                            }
                            
                            // Log track status periodically
                            if (tracks.length > 0) {
                                console.log('Track monitor:', {
                                    totalTracks: tracks.length,
                                    activeTracks: activeTracks.length,
                                    connectionState: state.peerConnection.connectionState,
                                    iceState: state.peerConnection.iceConnectionState
                                });
                            }
                        }
                    }, 5000); // Check every 5 seconds
                }
                
                setTimeout(() => {
                    elements.callStatus.classList.add('hidden');
                }, 2000);
            } else if (connectionState === 'failed') {
                console.error('Connection failed', {
                    iceState: state.peerConnection.iceConnectionState,
                    signalingState: state.peerConnection.signalingState
                });
                
                // Check if we have active tracks despite connection failure
                const hasActiveTracks = state.remoteStream && 
                    state.remoteStream.getTracks().some(track => 
                        track.readyState === 'live' && track.enabled
                    );
                
                if (hasActiveTracks) {
                    console.log('Connection failed but tracks are active - attempting recovery');
                    // Don't show error immediately if tracks are working
                    updateCallStatus('Conexão instável - tentando manter...');
                }
                
                // Try to restart ICE if connection failed (may recover)
                if (state.peerConnection && state.inCall) {
                    console.log('Attempting to restart ICE...');
                    try {
                        state.peerConnection.restartIce();
                        if (!hasActiveTracks) {
                            updateCallStatus('Reconectando...');
                        }
                        
                        // Give it more time to recover if we have active tracks
                        const retryTimeout = hasActiveTracks ? 10000 : 5000;
                        setTimeout(() => {
                            if (state.peerConnection && state.peerConnection.connectionState === 'failed') {
                                // Check again if tracks are still active
                                const stillActive = state.remoteStream && 
                                    state.remoteStream.getTracks().some(track => 
                                        track.readyState === 'live' && track.enabled
                                    );
                                
                                if (stillActive) {
                                    console.warn('Connection state is failed but tracks remain active - continuing');
                                    updateCallStatus('Conexão mantida');
                                } else {
                                    console.error('Connection still failed after ICE restart - no active tracks');
                                    updateCallStatus('Conexão perdida - tente novamente');
                                }
                            }
                        }, retryTimeout);
                    } catch (restartError) {
                        console.error('Error restarting ICE:', restartError);
                        if (!hasActiveTracks) {
                            updateCallStatus('Conexão perdida');
                        }
                    }
                } else {
                    if (!hasActiveTracks) {
                        updateCallStatus('Conexão perdida');
                    }
                }
            } else if (connectionState === 'disconnected') {
                console.warn('Connection disconnected', {
                    iceState: state.peerConnection.iceConnectionState
                });
                
                // Disconnected can be temporary - wait a bit before showing error
                if (state.peerConnection && state.peerConnection.iceConnectionState === 'failed') {
                    // ICE failed - connection is really lost
                    updateCallStatus('Conexão perdida');
                } else {
                    // May reconnect - show status and wait
                    updateCallStatus('Reconectando...');
                    
                    // If still disconnected after a few seconds, try to restart ICE
                    setTimeout(() => {
                        if (state.peerConnection && 
                            state.peerConnection.connectionState === 'disconnected' &&
                            state.peerConnection.iceConnectionState !== 'failed' &&
                            state.inCall) {
                            console.log('Still disconnected, attempting ICE restart...');
                            try {
                                state.peerConnection.restartIce();
                            } catch (err) {
                                console.error('Error restarting ICE after disconnect:', err);
                            }
                        }
                    }, 3000);
                }
            } else if (connectionState === 'connecting') {
                updateCallStatus('Conectando...');
            }
        };
        
        // Also log ICE connection state
        state.peerConnection.oniceconnectionstatechange = () => {
            const iceState = state.peerConnection.iceConnectionState;
            console.log('ICE connection state:', iceState);
            
            if (iceState === 'connected' || iceState === 'completed') {
                console.log('ICE connection established');
                // Clear any error status
                if (state.peerConnection.connectionState === 'connected') {
                    updateCallStatus('Conectado');
                }
            } else if (iceState === 'failed') {
                console.error('ICE connection failed');
                
                // Check if we have active tracks - if so, connection might still work
                const hasActiveTracks = state.remoteStream && 
                    state.remoteStream.getTracks().some(track => 
                        track.readyState === 'live' && track.enabled
                    );
                
                console.log('ICE failed, checking stream status:', {
                    hasRemoteStream: !!state.remoteStream,
                    hasActiveTracks: hasActiveTracks,
                    trackStates: state.remoteStream ? 
                        state.remoteStream.getTracks().map(t => ({
                            kind: t.kind,
                            enabled: t.enabled,
                            readyState: t.readyState
                        })) : []
                });
                
                // If we have active tracks, try to keep them alive and restart ICE
                if (hasActiveTracks) {
                    console.log('ICE failed but tracks are active - attempting recovery...');
                    updateCallStatus('Reconectando...');
                }
                
                // Try to restart ICE
                if (state.peerConnection && state.inCall) {
                    console.log('ICE failed, attempting restart...');
                    try {
                        state.peerConnection.restartIce();
                        if (!hasActiveTracks) {
                            updateCallStatus('Reconectando...');
                        }
                        
                        // Give it more time to recover if we have tracks
                        const retryTimeout = hasActiveTracks ? 10000 : 5000;
                        setTimeout(() => {
                            if (state.peerConnection && 
                                state.peerConnection.iceConnectionState === 'failed') {
                                if (!hasActiveTracks) {
                                    console.error('ICE restart failed - no recovery possible');
                                    updateCallStatus('Falha na conexão ICE');
                                } else {
                                    // Even if ICE failed, if tracks are live, try to continue
                                    console.warn('ICE failed but tracks remain active - attempting to continue');
                                    updateCallStatus('Conexão instável - tentando manter...');
                                }
                            }
                        }, retryTimeout);
                    } catch (restartError) {
                        console.error('Error restarting ICE:', restartError);
                        if (!hasActiveTracks) {
                            updateCallStatus('Falha na conexão ICE');
                        }
                    }
                } else {
                    if (!hasActiveTracks) {
                        updateCallStatus('Falha na conexão ICE');
                    }
                }
            } else if (iceState === 'disconnected') {
                console.warn('ICE disconnected, may reconnect');
                
                // Check if tracks are still active during disconnect
                if (state.remoteStream) {
                    const activeTracks = state.remoteStream.getTracks().filter(t => 
                        t.readyState === 'live' && t.enabled
                    );
                    if (activeTracks.length > 0) {
                        console.log('ICE disconnected but tracks are still active - waiting for reconnect');
                    }
                }
                
                // Don't show error immediately, might reconnect
            } else if (iceState === 'checking') {
                console.log('ICE checking...');
                updateCallStatus('Conectando...');
            }
        };
    }

    async function getLocalStream(video = true) {
        // Try to get available devices first
        let availableDevices = { audio: false, video: false };
        
        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            availableDevices.audio = devices.some(d => d.kind === 'audioinput');
            availableDevices.video = devices.some(d => d.kind === 'videoinput');
            console.log('Available devices:', availableDevices);
        } catch (err) {
            console.warn('Could not enumerate devices, will try anyway:', err);
            // Assume both are available if we can't check
            availableDevices.audio = true;
            availableDevices.video = video;
        }
        
        // Build constraints based on what we want and what's available
        const audioConstraints = availableDevices.audio ? {
            echoCancellation: true,
            noiseSuppression: true,
            autoGainControl: true,
            // Mobile-specific audio settings
            ...(isMobile && {
                sampleRate: 48000,
                channelCount: 1
            })
        } : false;
        
        const videoConstraints = (video && availableDevices.video) ? (isMobile ? {
            facingMode: state.currentCamera,
            width: { ideal: 640, max: 1280 },
            height: { ideal: 480, max: 720 },
            frameRate: { ideal: 30, max: 30 },
            aspectRatio: { ideal: 16/9 }
        } : {
            facingMode: state.currentCamera,
            width: { ideal: 1280 },
            height: { ideal: 720 }
        }) : false;
        
        let constraints = {
            audio: audioConstraints,
            video: videoConstraints
        };
        
        console.log('Requesting media with constraints:', constraints);
        
        let stream = null;
        let audioError = null;
        let videoError = null;
        
        // Strategy: Try to get both, if that fails, try each separately
        try {
            // First try: get both if requested
            if (audioConstraints && videoConstraints) {
                try {
                    stream = await navigator.mediaDevices.getUserMedia(constraints);
                    console.log('Successfully obtained both audio and video');
                } catch (error) {
                    console.warn('Failed to get both, will try separately:', error);
                    // Will try separately below
                    if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
                        // Permission denied - try separately to see which one
                        audioError = error;
                        videoError = error;
                    } else if (error.name === 'NotFoundError') {
                        // Device not found - check which one
                        if (error.message && error.message.includes('video')) {
                            videoError = error;
                        } else if (error.message && error.message.includes('audio')) {
                            audioError = error;
                        } else {
                            audioError = error;
                            videoError = error;
                        }
                    } else {
                        audioError = error;
                        videoError = error;
                    }
                }
            }
            
            // If we don't have a stream yet, try to get what we can
            if (!stream) {
                // Try audio only if we need it and don't have it
                if (audioConstraints && !stream?.getAudioTracks().length) {
                    try {
                        const audioStream = await navigator.mediaDevices.getUserMedia({ audio: audioConstraints, video: false });
                        if (!stream) {
                            stream = audioStream;
                            console.log('Obtained audio only');
                        } else {
                            // Add audio tracks to existing stream
                            audioStream.getAudioTracks().forEach(track => {
                                stream.addTrack(track);
                            });
                            // Stop the temporary stream's tracks (they're now in our stream)
                            audioStream.getTracks().forEach(track => {
                                if (!stream.getTracks().includes(track)) {
                                    track.stop();
                                }
                            });
                            console.log('Added audio tracks to existing stream');
                        }
                    } catch (error) {
                        console.warn('Failed to get audio:', error);
                        audioError = error;
                    }
                }
                
                // Try video only if we need it and don't have it
                if (videoConstraints && !stream?.getVideoTracks().length) {
                    try {
                        // Try with simpler video constraints first
                        let videoConstraintsSimple = videoConstraints;
                        if (isMobile) {
                            // Try simpler constraints for mobile
                            try {
                                const videoStream = await navigator.mediaDevices.getUserMedia({ 
                                    audio: false, 
                                    video: videoConstraints 
                                });
                                if (!stream) {
                                    stream = videoStream;
                                    console.log('Obtained video only');
                                } else {
                                    // Add video tracks to existing stream
                                    videoStream.getVideoTracks().forEach(track => {
                                        stream.addTrack(track);
                                    });
                                    // Stop the temporary stream's tracks
                                    videoStream.getTracks().forEach(track => {
                                        if (!stream.getTracks().includes(track)) {
                                            track.stop();
                                        }
                                    });
                                    console.log('Added video tracks to existing stream');
                                }
                            } catch (error) {
                                // Try even simpler
                                console.warn('Video with constraints failed, trying simpler:', error);
                                const videoStream = await navigator.mediaDevices.getUserMedia({ 
                                    audio: false, 
                                    video: true 
                                });
                                if (!stream) {
                                    stream = videoStream;
                                    console.log('Obtained video only (simple)');
                                } else {
                                    videoStream.getVideoTracks().forEach(track => {
                                        stream.addTrack(track);
                                    });
                                    videoStream.getTracks().forEach(track => {
                                        if (!stream.getTracks().includes(track)) {
                                            track.stop();
                                        }
                                    });
                                    console.log('Added video tracks to existing stream (simple)');
                                }
                            }
                        } else {
                            const videoStream = await navigator.mediaDevices.getUserMedia({ 
                                audio: false, 
                                video: videoConstraints 
                            });
                            if (!stream) {
                                stream = videoStream;
                                console.log('Obtained video only');
                            } else {
                                videoStream.getVideoTracks().forEach(track => {
                                    stream.addTrack(track);
                                });
                                videoStream.getTracks().forEach(track => {
                                    if (!stream.getTracks().includes(track)) {
                                        track.stop();
                                    }
                                });
                                console.log('Added video tracks to existing stream');
                            }
                        }
                    } catch (error) {
                        console.warn('Failed to get video:', error);
                        videoError = error;
                    }
                }
            }
            
            // If we still don't have a stream, we failed completely
            if (!stream || (stream.getAudioTracks().length === 0 && stream.getVideoTracks().length === 0)) {
                throw new Error('Não foi possível obter nenhum dispositivo de mídia (câmera ou microfone)');
            }
            
            state.localStream = stream;
            elements.localVideo.srcObject = stream;
            
            // Update state based on what we actually got
            state.isVideoEnabled = stream.getVideoTracks().length > 0;
            
            // Log stream info
            const videoTracks = stream.getVideoTracks();
            const audioTracks = stream.getAudioTracks();
            console.log('Stream obtained:', {
                videoTracks: videoTracks.length,
                audioTracks: audioTracks.length,
                hasVideo: videoTracks.length > 0,
                hasAudio: audioTracks.length > 0,
                videoSettings: videoTracks[0]?.getSettings(),
                audioSettings: audioTracks[0]?.getSettings()
            });
            
            // Show warning if we couldn't get what was requested
            if (video && videoTracks.length === 0) {
                console.warn('Video was requested but not available - continuing with audio only');
            }
            if (audioTracks.length === 0) {
                console.warn('Audio was requested but not available - continuing with video only');
            }
            
            return stream;
        } catch (error) {
            console.error('Error accessing media devices:', error);
            
            // Build helpful error message
            const hasAudio = state.localStream?.getAudioTracks().length > 0;
            const hasVideo = state.localStream?.getVideoTracks().length > 0;
            
            let errorMessage = '';
            
            if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
                if (!hasAudio && !hasVideo) {
                    errorMessage = 'Permissão negada para câmera e microfone.\n\n';
                    errorMessage += 'Para aceitar a chamada, você precisa permitir o acesso:\n';
                    errorMessage += '1. Clique no ícone de bloqueio/câmera na barra de endereço\n';
                    errorMessage += '2. Permita o acesso à câmera e/ou microfone\n';
                    errorMessage += '3. Tente aceitar a chamada novamente';
                } else {
                    errorMessage = 'Permissão negada para ' + (!hasAudio ? 'microfone' : 'câmera') + '.\n';
                    errorMessage += 'A chamada continuará com ' + (hasAudio ? 'áudio' : 'vídeo') + ' apenas.';
                }
            } else if (error.name === 'NotFoundError' || error.name === 'DevicesNotFoundError') {
                if (!hasAudio && !hasVideo) {
                    errorMessage = 'Nenhum dispositivo de mídia encontrado.\n';
                    errorMessage += 'Certifique-se de que seu dispositivo possui câmera ou microfone conectados.';
                } else {
                    errorMessage = 'Dispositivo ' + (!hasAudio ? 'de microfone' : 'de câmera') + ' não encontrado.\n';
                    errorMessage += 'A chamada continuará com ' + (hasAudio ? 'áudio' : 'vídeo') + ' apenas.';
                }
            } else if (error.name === 'NotReadableError' || error.name === 'TrackStartError') {
                if (!hasAudio && !hasVideo) {
                    errorMessage = 'Câmera e microfone já estão em uso por outro aplicativo.\n';
                    errorMessage += 'Feche outros aplicativos e tente novamente.';
                } else {
                    errorMessage = (!hasAudio ? 'Microfone' : 'Câmera') + ' já está em uso.\n';
                    errorMessage += 'A chamada continuará com ' + (hasAudio ? 'áudio' : 'vídeo') + ' apenas.';
                }
            } else {
                if (!hasAudio && !hasVideo) {
                    errorMessage = 'Erro ao acessar dispositivos de mídia: ' + (error.message || error.name || 'Erro desconhecido');
                } else {
                    // If we have at least one, don't show error, just log it
                    console.warn('Partial success - continuing with available devices');
                    return state.localStream; // Return what we have
                }
            }
            
            // Only show alert if we have nothing
            if (!hasAudio && !hasVideo && errorMessage) {
                alert(errorMessage);
                throw error;
            } else if (errorMessage) {
                // If we have something, just log the warning
                console.warn(errorMessage);
                return state.localStream; // Return what we have
            }
            
            // If we have a stream with at least one track, return it
            if (state.localStream && (state.localStream.getAudioTracks().length > 0 || state.localStream.getVideoTracks().length > 0)) {
                return state.localStream;
            }
            
            throw error;
        }
    }

    async function startCall(isVideo = true) {
        try {
            // Don't start if already in call
            if (state.inCall || state.isAcceptingCall) {
                console.log('Cannot start call: already in call');
                return;
            }
            
            // Clear pending candidates from previous call
            state.pendingCandidates = [];
            
            state.isVideoCall = isVideo;
            await getLocalStream(isVideo);
            
            // Adjust call type based on what we actually got
            if (state.localStream) {
                const hasVideo = state.localStream.getVideoTracks().length > 0;
                const hasAudio = state.localStream.getAudioTracks().length > 0;
                
                // If we requested video but don't have it, adjust to voice call
                if (isVideo && !hasVideo && hasAudio) {
                    console.log('Video requested but not available - adjusting to voice call');
                    state.isVideoCall = false;
                }
                // If we requested video and have it, keep it as video call
                // If we requested voice and have audio, keep it as voice call
                // If we only have video (no audio), it's still a video call but without audio
                
                console.log('Call type adjusted:', {
                    requested: isVideo ? 'video' : 'voice',
                    actual: state.isVideoCall ? 'video' : 'voice',
                    hasVideo: hasVideo,
                    hasAudio: hasAudio
                });
            }
            
            await createPeerConnection();
            
            // Verify transceivers before creating offer
            const transceivers = state.peerConnection.getTransceivers();
            console.log('Transceivers before offer:', transceivers.map(t => ({
                kind: t.receiver.track?.kind || t.sender.track?.kind || 'unknown',
                direction: t.direction,
                currentDirection: t.currentDirection
            })));
            
            // Mobile may need offerToReceiveAudio/Video explicitly set
            const offerOptions = isMobile ? {
                offerToReceiveAudio: true,
                offerToReceiveVideo: state.isVideoCall
            } : {};
            
            const offer = await state.peerConnection.createOffer(offerOptions);
            console.log('Offer created:', {
                type: offer.type,
                hasSDP: !!offer.sdp,
                sdpLength: offer.sdp?.length,
                isMobile: isMobile
            });
            
            await state.peerConnection.setLocalDescription(offer);
            console.log('Local description set (offer)');
            
            // Verify transceivers after setting local description
            const transceiversAfter = state.peerConnection.getTransceivers();
            console.log('Transceivers after offer:', transceiversAfter.map(t => ({
                kind: t.receiver.track?.kind || t.sender.track?.kind || 'unknown',
                direction: t.direction,
                currentDirection: t.currentDirection
            })));
            
            sendSignal('offer', offer);
            
            // Reset answer received flag
            state.answerReceived = false;
            
            // Set timeout to detect if connection gets stuck
            state.connectionTimeout = setTimeout(() => {
                if (state.peerConnection) {
                    const connectionState = state.peerConnection.connectionState;
                    const iceState = state.peerConnection.iceConnectionState;
                    console.warn('Connection timeout check:', {
                        connectionState,
                        iceState,
                        answerReceived: state.answerReceived
                    });
                    
                    if (!state.answerReceived) {
                        console.warn('Answer not received after 30 seconds');
                        updateCallStatus('Aguardando resposta...');
                    } else if (connectionState !== 'connected' && connectionState !== 'connecting') {
                        console.error('Connection appears to be stuck, state:', connectionState);
                        updateCallStatus('Tentando reconectar...');
                    }
                }
            }, 30000); // 30 seconds timeout
            
            showCallOverlay();
            updateCallStatus('Chamando...');
        } catch (error) {
            console.error('Error starting call:', error);
            endCall(false);
            alert('Erro ao iniciar chamada. Verifique as permissões de câmera/microfone.');
        }
    }

    let pendingOffer = null;
    let pendingOfferSignalId = null;

    async function handleIncomingOffer(offer, signalId) {
        try {
            // Ignore if already in call or already accepting
            if (state.inCall || state.isAcceptingCall) {
                console.log('Ignoring offer: already in call or accepting');
                return;
            }
            
            // Ignore if we already have a pending offer
            if (pendingOffer) {
                console.log('Ignoring offer: already have pending offer');
                return;
            }
            
            // Log SDP structure for debugging
            if (offer && offer.sdp) {
                const hasNewlines = offer.sdp.includes('\n');
                const lineCount = offer.sdp.split('\n').length;
                console.log('Received offer SDP:', {
                    hasNewlines,
                    lineCount,
                    length: offer.sdp.length,
                    firstChars: offer.sdp.substring(0, 100)
                });
            }
            
            // Detect if this is a video call by analyzing SDP media lines
            const isVideo = offer.sdp ? isVideoCallSDP(offer.sdp) : false;
            state.isVideoCall = isVideo;
            
            console.log('Incoming call type detected:', {
                isVideo: isVideo,
                sdpLength: offer.sdp?.length
            });
            
            pendingOffer = offer;
            pendingOfferSignalId = signalId;
            
            showIncomingCallModal();
        } catch (error) {
            console.error('Error handling offer:', error);
        }
    }

    async function acceptIncomingCall() {
        try {
            // Prevent multiple accept attempts
            if (state.isAcceptingCall || !pendingOffer) {
                console.log('Cannot accept: already accepting or no pending offer');
                return;
            }
            
            // Validate pending offer
            if (!pendingOffer.type || !pendingOffer.sdp) {
                console.error('Invalid pending offer:', pendingOffer);
                pendingOffer = null;
                pendingOfferSignalId = null;
                elements.incomingCallModal.classList.add('hidden');
                alert('Erro: oferta de chamada inválida.');
                return;
            }
            
            state.isAcceptingCall = true;
            
            // Clear pending candidates from previous call attempts
            state.pendingCandidates = [];
            
            // Detect if this is a video call by analyzing SDP media lines
            const isVideo = pendingOffer.sdp ? isVideoCallSDP(pendingOffer.sdp) : false;
            state.isVideoCall = isVideo;
            
            console.log('Detected call type:', {
                isVideo: isVideo,
                sdpLength: pendingOffer.sdp?.length,
                hasVideoMediaLine: pendingOffer.sdp?.includes('m=video')
            });
            
            // Show a loading message in the modal while requesting permissions
            const modalContent = elements.incomingCallModal.querySelector('p');
            const originalText = modalContent?.textContent || '';
            if (modalContent) {
                if (isVideo) {
                    modalContent.textContent = 'Solicitando permissões de câmera e microfone...';
                } else {
                    modalContent.textContent = 'Solicitando permissão de microfone...';
                }
            }
            
            // Mark this signal as processed
            if (pendingOfferSignalId) {
                state.processedSignalIds.add(pendingOfferSignalId);
            }
            
            // Clean SDP before using
            let cleanedSDP = cleanSDP(pendingOffer.sdp);
            if (!cleanedSDP) {
                // Fallback to original if cleaning fails
                console.warn('SDP cleaning failed, using original');
                cleanedSDP = String(pendingOffer.sdp).replace(/\r\n/g, '\n').replace(/\r/g, '\n');
            }
            
            // Create a version without problematic ssrc/msid lines as backup
            const sdpWithoutProblematicLines = cleanedSDP.split('\n')
                .filter(line => {
                    // Remove lines that have ssrc and msid but look problematic
                    if (line.includes('ssrc:') && line.includes('msid:')) {
                        // Check if line can be parsed correctly
                        const match = line.match(/a=ssrc:\d+\s+msid:[a-f0-9\-]+\s+[a-f0-9\-]+/i);
                        return match !== null; // Keep only if it matches the pattern
                    }
                    return true; // Keep all other lines
                })
                .join('\n');
            
            // Validate SDP format before using
            if (!cleanedSDP.includes('v=') || !cleanedSDP.includes('m=')) {
                console.error('SDP missing required fields:', {
                    hasVersion: cleanedSDP.includes('v='),
                    hasMedia: cleanedSDP.includes('m='),
                    originalLength: pendingOffer.sdp ? pendingOffer.sdp.length : 0,
                    cleanedLength: cleanedSDP.length
                });
                throw new Error('SDP format invalid: missing required fields');
            }
            
            const cleanOffer = {
                type: pendingOffer.type,
                sdp: cleanedSDP
            };
            
            // Get local stream first - if this fails, don't proceed
            try {
                await getLocalStream(isVideo);
                // Close modal after successfully getting stream
                elements.incomingCallModal.classList.add('hidden');
            } catch (streamError) {
                // If stream fails (permissions, etc), clean up and return
                console.error('Failed to get local stream when accepting call:', streamError);
                
                // Restore original modal text
                const modalContent = elements.incomingCallModal.querySelector('p');
                if (modalContent) {
                    modalContent.textContent = 'Alguém deseja iniciar uma chamada de vídeo/voz com você.';
                }
                
                state.isAcceptingCall = false;
                pendingOffer = null;
                pendingOfferSignalId = null;
                
                // Don't close modal yet - let user see the error and try again
                // The error message is already shown by getLocalStream
                return;
            }
            
            // Now create peer connection
            try {
                await createPeerConnection();
            } catch (pcError) {
                console.error('Failed to create peer connection:', pcError);
                // Clean up stream if peer connection fails
                if (state.localStream) {
                    state.localStream.getTracks().forEach(track => track.stop());
                    state.localStream = null;
                }
                state.isAcceptingCall = false;
                pendingOffer = null;
                pendingOfferSignalId = null;
                elements.incomingCallModal.classList.add('hidden');
                alert('Erro ao criar conexão. Tente novamente.');
                return;
            }
            
            // Create RTCSessionDescription with cleaned SDP
            // Try to set remote description with error handling
            try {
                const sessionDescription = new RTCSessionDescription(cleanOffer);
                
                // Log problematic lines for debugging
                const sdpLines = cleanedSDP.split('\n');
                const problematicLines = sdpLines.filter(line => 
                    line.includes('ssrc:') && line.includes('msid:')
                );
                if (problematicLines.length > 0) {
                    console.log('Found ssrc/msid lines:', problematicLines);
                }
                
                await state.peerConnection.setRemoteDescription(sessionDescription);
            } catch (sdpError) {
                console.error('SDP Error details:', {
                    error: sdpError.message,
                    sdpType: cleanOffer.type,
                    sdpLength: cleanedSDP.length,
                    problematicLines: cleanedSDP.split('\n').filter(line => 
                        line.includes('ssrc:') && line.includes('msid:')
                    ).slice(0, 3)
                });
                
                // Try with SDP without problematic lines
                if (sdpWithoutProblematicLines !== cleanedSDP && sdpWithoutProblematicLines.length > 0) {
                    console.log('Attempting with SDP without problematic ssrc/msid lines...');
                    try {
                        const cleanOfferNoProblematic = {
                            type: pendingOffer.type,
                            sdp: sdpWithoutProblematicLines
                        };
                        await state.peerConnection.setRemoteDescription(new RTCSessionDescription(cleanOfferNoProblematic));
                        console.log('Success with SDP without problematic lines');
                        // Update cleanedSDP for subsequent use
                        cleanedSDP = sdpWithoutProblematicLines;
                    } catch (noProblematicError) {
                        console.warn('SDP without problematic lines also failed, trying original...');
                        
                        // Try with original SDP as last resort
                        if (pendingOffer.sdp !== cleanedSDP) {
                            try {
                                const originalOffer = {
                                    type: pendingOffer.type,
                                    sdp: String(pendingOffer.sdp).replace(/\r\n/g, '\n').replace(/\r/g, '\n')
                                };
                                await state.peerConnection.setRemoteDescription(new RTCSessionDescription(originalOffer));
                                console.log('Success with original SDP');
                            } catch (originalError) {
                                console.error('All SDP attempts failed:', {
                                    cleaned: sdpError.message,
                                    noProblematic: noProblematicError.message,
                                    original: originalError.message
                                });
                                throw sdpError; // Throw the original error
                            }
                        } else {
                            throw sdpError;
                        }
                    }
                } else if (pendingOffer.sdp !== cleanedSDP) {
                    // Try with original SDP as last resort
                    console.log('Attempting with original SDP...');
                    try {
                        const originalOffer = {
                            type: pendingOffer.type,
                            sdp: String(pendingOffer.sdp).replace(/\r\n/g, '\n').replace(/\r/g, '\n')
                        };
                        await state.peerConnection.setRemoteDescription(new RTCSessionDescription(originalOffer));
                        console.log('Success with original SDP');
                    } catch (originalError) {
                        console.error('Original SDP also failed:', originalError);
                        throw sdpError; // Throw the original error
                    }
                } else {
                    throw sdpError;
                }
            }
            
            // Process any pending ICE candidates now that remote description is set
            await processPendingCandidates();
            
            console.log('Creating answer...');
            
            // Ensure transceivers are configured correctly
            const transceivers = state.peerConnection.getTransceivers();
            console.log('Transceivers before answer:', transceivers.map(t => ({
                kind: t.receiver.track?.kind || 'unknown',
                direction: t.direction,
                currentDirection: t.currentDirection
            })));
            
            // Mobile may need offerToReceiveAudio/Video explicitly set
            const answerOptions = isMobile ? {
                offerToReceiveAudio: true,
                offerToReceiveVideo: state.isVideoCall
            } : {};
            
            const answer = await state.peerConnection.createAnswer(answerOptions);
            console.log('Answer created, setting local description...', {
                answerType: answer.type,
                hasSDP: !!answer.sdp,
                sdpLength: answer.sdp?.length,
                isMobile: isMobile
            });
            
            await state.peerConnection.setLocalDescription(answer);
            console.log('Local description set, sending answer signal...');
            
            // Verify transceivers after setting local description
            const transceiversAfter = state.peerConnection.getTransceivers();
            console.log('Transceivers after answer:', transceiversAfter.map(t => ({
                kind: t.receiver.track?.kind || t.sender.track?.kind || 'unknown',
                direction: t.direction,
                currentDirection: t.currentDirection,
                hasReceiverTrack: !!t.receiver.track,
                hasSenderTrack: !!t.sender.track
            })));
            
            // Ensure transceivers can receive media (important for voice-only calls)
            transceiversAfter.forEach(transceiver => {
                // For voice calls, ensure we can receive audio
                if (!state.isVideoCall && transceiver.sender.track?.kind === 'audio') {
                    // Already configured by addTrack, but ensure receiver is ready
                    if (!transceiver.receiver.track && transceiver.direction === 'sendonly') {
                        console.warn('Audio transceiver is sendonly, may need adjustment');
                    }
                }
                // Ensure direction allows receiving
                if (transceiver.direction === 'sendonly') {
                    console.warn(`Transceiver ${transceiver.sender.track?.kind || 'unknown'} is sendonly, may not receive media`);
                }
            });
            
            sendSignal('answer', answer);
            console.log('Answer signal sent');
            
            // Reset answer received flag
            state.answerReceived = false;
            
            // Set timeout to detect if connection gets stuck
            state.connectionTimeout = setTimeout(() => {
                if (state.peerConnection) {
                    const connectionState = state.peerConnection.connectionState;
                    const iceState = state.peerConnection.iceConnectionState;
                    console.warn('Connection timeout check:', {
                        connectionState,
                        iceState,
                        answerReceived: state.answerReceived
                    });
                    
                    if (connectionState !== 'connected' && connectionState !== 'connecting') {
                        console.error('Connection appears to be stuck, state:', connectionState);
                        updateCallStatus('Tentando reconectar...');
                    } else if (iceState === 'checking' || iceState === 'disconnected') {
                        console.warn('ICE connection seems stuck, state:', iceState);
                    }
                }
            }, 30000); // 30 seconds timeout
            
            showCallOverlay();
            updateCallStatus('Conectando...');
            
            // Clear pending offer
            pendingOffer = null;
            pendingOfferSignalId = null;
            state.isAcceptingCall = false;
        } catch (error) {
            console.error('Error accepting call:', error);
            
            // Clean up state
            state.isAcceptingCall = false;
            pendingOffer = null;
            pendingOfferSignalId = null;
            state.pendingCandidates = [];
            
            // Clean up stream if it was created
            if (state.localStream) {
                state.localStream.getTracks().forEach(track => track.stop());
                state.localStream = null;
            }
            
            // Clean up peer connection if it was created
            if (state.peerConnection) {
                state.peerConnection.close();
                state.peerConnection = null;
            }
            
            // Hide modals
            elements.incomingCallModal.classList.add('hidden');
            elements.callOverlay.classList.add('hidden');
            
            // Only show alert if it's not a permission error (those are already shown by getLocalStream)
            if (!error.message || (!error.message.includes('permission') && !error.name?.includes('NotAllowed'))) {
                let errorMessage = 'Erro ao aceitar chamada.';
                if (error.message && error.message.includes('SDP')) {
                    errorMessage += ' Problema com a descrição da sessão. Tente iniciar uma nova chamada.';
                } else if (error.message) {
                    errorMessage += ' ' + error.message;
                }
                alert(errorMessage);
            }
        }
    }

    async function handleIncomingAnswer(answer) {
        try {
            if (!state.peerConnection) {
                console.warn('Received answer but no peer connection exists yet');
                return;
            }
            
            // Check if remote description is already set (avoid processing duplicate answers)
            if (state.peerConnection.remoteDescription) {
                const currentRemoteSDP = state.peerConnection.remoteDescription.sdp;
                const incomingSDP = answer?.sdp;
                
                // If it's the same SDP, ignore (duplicate)
                if (incomingSDP && currentRemoteSDP && 
                    currentRemoteSDP.substring(0, 100) === incomingSDP.substring(0, 100)) {
                    console.log('Ignoring duplicate answer - remote description already set');
                    return;
                }
                
                // If remote description is in stable state, we already processed an answer
                if (state.peerConnection.signalingState === 'stable') {
                    console.log('Ignoring answer - connection already has remote description in stable state');
                    return;
                }
            }
            
            console.log('Processing incoming answer...', {
                hasPeerConnection: !!state.peerConnection,
                answerType: answer?.type,
                hasSDP: !!answer?.sdp,
                connectionState: state.peerConnection?.connectionState,
                signalingState: state.peerConnection?.signalingState,
                hasRemoteDescription: !!state.peerConnection?.remoteDescription
            });
            
            // Validate and clean answer
            if (!answer || !answer.type || !answer.sdp) {
                console.error('Invalid answer payload:', answer);
                return;
            }
            
            // Clean SDP before using
            const cleanedSDP = cleanSDP(answer.sdp);
            if (!cleanedSDP) {
                throw new Error('Invalid SDP in answer');
            }
            
            const cleanAnswer = {
                type: answer.type,
                sdp: cleanedSDP
            };
            
            console.log('Setting remote description with answer...');
            const sessionDescription = new RTCSessionDescription(cleanAnswer);
            await state.peerConnection.setRemoteDescription(sessionDescription);
            
            // Process any pending ICE candidates now that remote description is set
            await processPendingCandidates();
            
            // Mark answer as received
            state.answerReceived = true;
            
            // Clear connection timeout if it exists
            if (state.connectionTimeout) {
                clearTimeout(state.connectionTimeout);
                state.connectionTimeout = null;
            }
            
            console.log('Answer processed successfully, connection state:', state.peerConnection.connectionState);
            updateCallStatus('Conectando...');
        } catch (error) {
            console.error('Error handling answer:', error);
            
            // If error is about wrong state, it's likely a duplicate answer - just ignore
            if (error.message && (error.message.includes('wrong state') || 
                                  error.message.includes('stable') ||
                                  error.message.includes('InvalidStateError'))) {
                console.warn('Answer error (likely duplicate):', error.message);
                // Don't end call, just return - connection might already be established
                return;
            }
            
            if (error.message && error.message.includes('SDP')) {
                console.error('SDP parsing error in answer:', error);
                // Only end call if we don't have a remote description yet
                if (!state.peerConnection.remoteDescription) {
                    endCall(false);
                    alert('Erro na conexão. A chamada será encerrada.');
                } else {
                    console.warn('SDP error but remote description exists - connection may still work');
                }
            }
        }
    }

    async function handleIncomingCandidate(candidate) {
        try {
            if (!state.peerConnection) {
                console.warn('Received candidate but no peer connection exists');
                return;
            }
            
            // Check if remote description is set
            // If not, queue the candidate to be processed later
            if (!state.peerConnection.remoteDescription) {
                console.log('Remote description not set yet, queuing candidate:', {
                    candidate: candidate.candidate?.substring(0, 50),
                    sdpMLineIndex: candidate.sdpMLineIndex
                });
                state.pendingCandidates.push(candidate);
                return;
            }
            
            // Check if candidate is for a media line that exists
            const transceivers = state.peerConnection.getTransceivers();
            const sdpMLineIndex = candidate.sdpMLineIndex;
            
            // If candidate is for a media line that doesn't exist, skip it
            if (sdpMLineIndex !== null && sdpMLineIndex >= transceivers.length) {
                console.warn('Skipping candidate for non-existent media line:', {
                    sdpMLineIndex,
                    availableTransceivers: transceivers.length
                });
                return;
            }
            
            console.log('Processing incoming ICE candidate:', {
                candidate: candidate.candidate?.substring(0, 50),
                sdpMLineIndex: candidate.sdpMLineIndex,
                sdpMid: candidate.sdpMid
            });
            
            // Mobile may need retry for adding candidates
            let retries = 0;
            const maxRetries = isMobile ? 3 : 1;
            
            while (retries <= maxRetries) {
                try {
                    await state.peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
                    console.log('ICE candidate added successfully');
                    break; // Success, exit retry loop
                } catch (addError) {
                    retries++;
                    if (retries > maxRetries) {
                        throw addError; // Re-throw if all retries failed
                    }
                    // Wait a bit before retry (mobile may need more time)
                    await new Promise(resolve => setTimeout(resolve, isMobile ? 100 : 50));
                }
            }
        } catch (error) {
            // Some candidates may fail (e.g., for different media lines), that's OK
            // Only log as error if it's not a common case
            if (error.message && (error.message.includes('sdpMLineIndex') || error.message.includes('InvalidStateError'))) {
                // If it's InvalidStateError and remote description is not set, queue it
                if (error.message.includes('InvalidStateError') && !state.peerConnection?.remoteDescription) {
                    console.log('Queueing candidate (remote description not set):', {
                        candidate: candidate.candidate?.substring(0, 50)
                    });
                    state.pendingCandidates.push(candidate);
                    return;
                }
                console.warn('Candidate skipped:', {
                    sdpMLineIndex: candidate.sdpMLineIndex,
                    error: error.message,
                    connectionState: state.peerConnection?.connectionState
                });
            } else {
                console.error('Error handling candidate:', error, candidate);
            }
        }
    }
    
    // Process queued candidates after remote description is set
    async function processPendingCandidates() {
        if (!state.peerConnection || !state.peerConnection.remoteDescription) {
            return;
        }
        
        if (state.pendingCandidates.length === 0) {
            return;
        }
        
        console.log(`Processing ${state.pendingCandidates.length} pending ICE candidates...`);
        
        const candidates = [...state.pendingCandidates];
        state.pendingCandidates = [];
        
        for (const candidate of candidates) {
            try {
                await state.peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
                console.log('Queued ICE candidate added successfully');
            } catch (error) {
                // Some candidates may still fail (e.g., duplicate or invalid), that's OK
                if (!error.message.includes('InvalidStateError')) {
                    console.warn('Error adding queued candidate:', error.message);
                }
            }
        }
    }

    // Helper function to detect if SDP is for video call
    function isVideoCallSDP(sdpString) {
        if (!sdpString || typeof sdpString !== 'string') {
            return false;
        }
        
        // Look for media description lines (m=video ...)
        // A video call will have at least one "m=video" line
        // A voice call will have only "m=audio" lines
        const lines = sdpString.split('\n');
        for (const line of lines) {
            const trimmed = line.trim();
            // Check for video media line: m=video <port> <proto> <fmt> ...
            if (trimmed.startsWith('m=video ')) {
                return true;
            }
        }
        
        // If no m=video found, it's a voice call
        return false;
    }
    
    // Helper function to clean and validate SDP
    function cleanSDP(sdpString) {
        if (!sdpString || typeof sdpString !== 'string') {
            return null;
        }
        
        // Normalize line endings
        let cleaned = sdpString.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
        
        // Check if SDP has no line breaks (all in one line)
        // SDP lines should start with a single letter followed by =
        const lineCount = cleaned.split('\n').filter(l => l.trim()).length;
        const hasNewlines = cleaned.includes('\n');
        const isSingleLine = !hasNewlines || (lineCount === 1 && cleaned.length > 100);
        const needsReconstruction = isSingleLine || (lineCount < 3 && cleaned.length > 100);
        
        if (needsReconstruction) {
            // Reconstruct SDP by splitting on SDP line patterns
            // SDP lines start with: v=, o=, s=, t=, a=, m=, c=, b=, etc.
            console.warn('SDP appears to be on single line or malformed, attempting to reconstruct...', {
                lineCount,
                length: cleaned.length,
                firstChars: cleaned.substring(0, 100),
                hasNewlines: cleaned.includes('\n')
            });
            
            // Reconstruct SDP by detecting line boundaries
            // SDP lines follow pattern: [single letter]=[value]
            // When concatenated: "v=0o=-" should become "v=0\no=-"
            // Pattern: [letter]=[value][letter]= where value doesn't contain newline
            
            // Simple and effective: find [letter]=[anything][letter]= and insert \n
            // The regex matches: ([a-z])=([value])([a-z])=
            // Where value is anything that doesn't contain newline and [letter]=
            // But be careful not to break c= lines (connection data)
            cleaned = cleaned.replace(/([a-z])=([^\n]*?)([a-z])=/gi, (match, p1, p2, p3) => {
                // p1 = first letter, p2 = value, p3 = next letter
                
                // Special case: if p1 is 'c' and p2 is very short (like "1"), 
                // it might be a broken c= line, don't split
                if (p1.toLowerCase() === 'c' && p2.length < 5 && /^\d+$/.test(p2)) {
                    // This looks like a broken c= line, don't split here
                    return match;
                }
                
                // Don't split if p2 contains IP address pattern and p1 is 'c'
                // (c= lines should have full connection data)
                if (p1.toLowerCase() === 'c' && /\d+\.\d+\.\d+\.\d+/.test(p2)) {
                    // This is a complete c= line, don't split
                    return match;
                }
                
                // Only insert newline if value is reasonable (not empty, not too long)
                if (p2.length > 0 && p2.length < 1000) {
                    return `${p1}=${p2}\n${p3}=`;
                }
                return match;
            });
            
            // Remove leading newlines
            cleaned = cleaned.replace(/^\n+/, '');
            // Normalize multiple newlines to single
            cleaned = cleaned.replace(/\n\n+/g, '\n');
            
            // Verify reconstruction worked
            const newLineCount = cleaned.split('\n').length;
            console.log('SDP reconstruction result:', {
                originalLines: lineCount,
                newLines: newLineCount,
                firstFewLines: cleaned.split('\n').slice(0, 5)
            });
        }
        
        // Remove any null bytes or invalid control characters (except \n)
        // Remove characters with codes 0-31 except \n (10) and \t (9)
        cleaned = cleaned.replace(/[\x00-\x08\x0B-\x1F\x7F]/g, '');
        
        // Remove any non-printable Unicode characters (but keep \n)
        cleaned = cleaned.replace(/[\u0000-\u0008\u000B-\u001F\u007F-\u009F]/g, '');
        
        // Split into lines and process each line
        const lines = cleaned.split('\n');
        const processedLines = [];
        
        for (let i = 0; i < lines.length; i++) {
            let line = lines[i].trim();
            
            // Skip empty lines
            if (!line) {
                continue;
            }
            
            // Fix lines that might have been broken incorrectly
            // SDP lines should start with a single letter followed by =
            // If a line doesn't start with a valid SDP type, it might be a continuation
            if (line.length > 0 && !/^[a-z]=/i.test(line) && processedLines.length > 0) {
                // This might be a continuation of the previous line
                const lastLine = processedLines[processedLines.length - 1];
                
                // Special case: if last line is a broken c= line (e.g., "c=1"), merge
                if (lastLine && lastLine.startsWith('c=')) {
                    const cValue = lastLine.substring(2).trim();
                    // If c= line is incomplete (just a number or very short), merge
                    if (/^\d+$/.test(cValue) || (cValue.length < 10 && !cValue.includes('IN'))) {
                        processedLines[processedLines.length - 1] = lastLine + ' ' + line;
                        continue;
                    }
                }
                
                // Check if previous line ends with space or if this looks like continuation
                if (lastLine && !lastLine.endsWith(' ') && !/^[a-z]=/i.test(line)) {
                    // Merge with previous line (remove extra spaces)
                    processedLines[processedLines.length - 1] = lastLine + ' ' + line;
                    continue;
                }
            }
            
            // Validate and fix connection lines (c= lines)
            // Format should be: c=IN IP4 <address> or c=IN IP6 <address>
            if (line.startsWith('c=')) {
                // Remove non-printable characters
                line = line.replace(/[^\x20-\x7E]/g, '');
                line = line.trim();
                
                // Check if line is malformed (e.g., "c=1" instead of "c=IN IP4 0.0.0.0")
                const cMatch = line.match(/^c=(.+)$/);
                if (cMatch) {
                    const cValue = cMatch[1].trim();
                    // If it's just a number or very short, it's likely malformed
                    if (/^\d+$/.test(cValue) || cValue.length < 5) {
                        console.warn('Malformed c= line detected, fixing:', line);
                        // Try to find a valid c= line nearby or use default
                        // Look for IP address pattern in the line or use default
                        const ipMatch = line.match(/(\d+\.\d+\.\d+\.\d+)/);
                        if (ipMatch) {
                            line = `c=IN IP4 ${ipMatch[1]}`;
                        } else {
                            // Use default connection data
                            line = 'c=IN IP4 0.0.0.0';
                        }
                    } else if (!cValue.includes('IN') && !cValue.includes('IP')) {
                        // Missing network type, try to fix
                        const ipMatch = cValue.match(/(\d+\.\d+\.\d+\.\d+)/);
                        if (ipMatch) {
                            line = `c=IN IP4 ${ipMatch[1]}`;
                        } else {
                            // Default fallback
                            line = 'c=IN IP4 0.0.0.0';
                        }
                    }
                }
            }
            
            // Normalize spaces in attribute lines (a= lines)
            if (line.startsWith('a=')) {
                // For a= lines, normalize multiple spaces to single space
                // Special handling for ssrc lines with msid
                if (line.includes('ssrc:') && line.includes('msid:')) {
                    // Format: a=ssrc:ID msid:stream-id track-id
                    // Remove all non-printable characters first (keep only printable ASCII)
                    line = line.replace(/[^\x20-\x7E]/g, '');
                    // Remove any tabs and normalize spaces to single space
                    line = line.replace(/[\t]+/g, ' ');
                    line = line.replace(/\s+/g, ' ').trim();
                    
                    // Remove any leading/trailing spaces from the entire line
                    line = line.trim();
                    
                    // Try to match and reconstruct the line
                    // Pattern: a=ssrc:number msid:uuid uuid
                    // More lenient regex to catch various formats
                    let ssrcMatch = line.match(/a=ssrc:(\d+)\s+msid:([a-f0-9\-]+)\s+([a-f0-9\-]+)/i);
                    
                    if (!ssrcMatch) {
                        // Try without the 'a=' prefix
                        ssrcMatch = line.match(/ssrc:(\d+)\s+msid:([a-f0-9\-]+)\s+([a-f0-9\-]+)/i);
                        if (ssrcMatch) {
                            line = `a=ssrc:${ssrcMatch[1]} msid:${ssrcMatch[2]} ${ssrcMatch[3]}`;
                        }
                    } else {
                        // Reconstruct with proper format - ensure single spaces
                        line = `a=ssrc:${ssrcMatch[1]} msid:${ssrcMatch[2]} ${ssrcMatch[3]}`;
                    }
                    
                    // If still can't parse, try to extract components manually
                    if (!ssrcMatch) {
                        const parts = line.split(/\s+/);
                        const ssrcPart = parts.find(p => p.startsWith('ssrc:'));
                        const msidPart = parts.find(p => p.startsWith('msid:'));
                        const trackPart = parts.find((p, i) => i > parts.indexOf(msidPart) && !p.includes(':'));
                        
                        if (ssrcPart && msidPart && trackPart) {
                            const ssrcId = ssrcPart.replace('ssrc:', '');
                            const msid = msidPart.replace('msid:', '');
                            line = `a=ssrc:${ssrcId} msid:${msid} ${trackPart}`;
                        } else {
                            // Last resort: try to create a minimal valid line
                            // Extract just ssrc if possible, or remove entirely
                            const ssrcOnly = line.match(/ssrc:(\d+)/);
                            if (ssrcOnly) {
                                // Create a minimal ssrc line without msid (less ideal but valid)
                                line = `a=ssrc:${ssrcOnly[1]}`;
                                console.warn('Simplified ssrc line (removed msid):', line);
                            } else {
                                // Remove the problematic line entirely (it's optional)
                                console.warn('Removing unparseable ssrc line:', line);
                                continue;
                            }
                        }
                    }
                } else {
                    // For other a= lines, just normalize spaces and remove non-printable chars
                    line = line.replace(/[^\x20-\x7E\n]/g, '');
                    line = line.replace(/\s+/g, ' ').trim();
                }
            } else {
                // For non-a= lines, also clean non-printable characters
                line = line.replace(/[^\x20-\x7E\n]/g, '');
                line = line.trim();
            }
            
            // Ensure proper line format
            // SDP lines should be: type=value or type=value with attributes
            if (line.length > 0) {
                processedLines.push(line);
            }
        }
        
        // Join lines back with proper line breaks
        cleaned = processedLines.join('\n');
        
        // Ensure it ends with newline (SDP standard)
        if (!cleaned.endsWith('\n')) {
            cleaned += '\n';
        }
        
        // Validate SDP structure
        const finalLines = cleaned.split('\n').filter(l => l.trim());
        if (finalLines.length < 3) {
            console.error('SDP has too few lines after processing:', finalLines.length);
            return null;
        }
        
        // Ensure it starts with v= (SDP version)
        if (!finalLines[0].trim().startsWith('v=')) {
            console.error('SDP does not start with version line. First line:', finalLines[0]);
            // Try to find and move v= line to the beginning
            const vLineIndex = finalLines.findIndex(l => l.trim().startsWith('v='));
            if (vLineIndex > 0) {
                const vLine = finalLines.splice(vLineIndex, 1)[0];
                finalLines.unshift(vLine);
                cleaned = finalLines.join('\n') + '\n';
                console.warn('Moved v= line to beginning');
            } else {
                console.error('No v= line found in SDP');
                return null;
            }
        }
        
        // Validate that we have required SDP lines
        const hasOrigin = finalLines.some(l => l.trim().startsWith('o='));
        const hasSession = finalLines.some(l => l.trim().startsWith('s='));
        const hasTiming = finalLines.some(l => l.trim().startsWith('t='));
        
        if (!hasOrigin || !hasTiming) {
            console.warn('SDP missing required lines:', {
                hasOrigin,
                hasSession,
                hasTiming
            });
        }
        
        return cleaned;
    }

    function sendSignal(type, payload) {
        // Ensure we only send type and sdp for SDP objects
        let cleanPayload = payload;
        if (payload && typeof payload === 'object' && 'type' in payload && 'sdp' in payload) {
            // It's an RTCSessionDescription, extract only type and sdp
            const cleanedSDP = cleanSDP(payload.sdp);
            if (!cleanedSDP) {
                console.error('Invalid SDP in payload:', payload);
                return;
            }
            
            cleanPayload = {
                type: payload.type,
                sdp: cleanedSDP
            };
        } else if (payload && typeof payload === 'object' && 'candidate' in payload) {
            // It's an RTCIceCandidate, send as is
            cleanPayload = payload;
        }
        
        // Enviar via fetch (o backend disparará o evento via Reverb)
        fetch(config.sendSignalUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': config.csrfToken
            },
            body: JSON.stringify({ type, payload: cleanPayload })
        }).catch(err => console.error('Error sending signal:', err));
    }

    function showCallOverlay() {
        state.inCall = true;
        elements.callOverlay.classList.remove('hidden');
        elements.incomingCallModal.classList.add('hidden');
        
        // Show/hide switch to voice button based on call type
        if (state.isVideoCall) {
            elements.switchToVoiceBtn.classList.remove('hidden');
            elements.switchCameraBtn.classList.remove('hidden');
        } else {
            elements.switchToVoiceBtn.classList.add('hidden');
            elements.switchCameraBtn.classList.add('hidden');
        }
    }

    function showIncomingCallModal() {
        elements.incomingCallModal.classList.remove('hidden');
    }

    function updateCallStatus(text) {
        elements.callStatus.querySelector('div').textContent = text;
        elements.callStatus.classList.remove('hidden');
    }

    function startCallTimer() {
        state.callStartTime = Date.now();
        elements.callTimer.classList.remove('hidden');
        
        state.callTimerInterval = setInterval(() => {
            if (!state.callStartTime) return;
            const elapsed = Math.floor((Date.now() - state.callStartTime) / 1000);
            const minutes = Math.floor(elapsed / 60);
            const seconds = elapsed % 60;
            elements.timerDisplay.textContent = 
                `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }, 1000);
    }

    function stopCallTimer() {
        if (state.callTimerInterval) {
            clearInterval(state.callTimerInterval);
            state.callTimerInterval = null;
        }
        state.callStartTime = null;
        elements.callTimer.classList.add('hidden');
        elements.timerDisplay.textContent = '00:00';
    }

    function endCall(sendHangup = true) {
        stopCallTimer();
        
        // Clear track monitor
        if (state.trackMonitorInterval) {
            clearInterval(state.trackMonitorInterval);
            state.trackMonitorInterval = null;
        }
        
        // Clear connection timeout
        if (state.connectionTimeout) {
            clearTimeout(state.connectionTimeout);
            state.connectionTimeout = null;
        }
        
        if (sendHangup && state.inCall) {
            sendSignal('hangup', {});
        }
        
        if (state.localStream) {
            state.localStream.getTracks().forEach(track => track.stop());
            state.localStream = null;
        }
        
        if (state.peerConnection) {
            state.peerConnection.close();
            state.peerConnection = null;
        }
        
        // Clear pending call state
        pendingOffer = null;
        pendingOfferSignalId = null;
        state.pendingCandidates = [];
        state.isAcceptingCall = false;
        state.inCall = false;
        state.isCallMinimized = false;
        state.isVideoCall = false;
        state.isVideoEnabled = true;
        state.isMuted = false;
        state.currentCamera = 'user';
        
        elements.callOverlay.classList.add('hidden');
        elements.incomingCallModal.classList.add('hidden');
        elements.minimizedCallWidget.classList.add('hidden');
        elements.remoteVideo.srcObject = null;
        elements.localVideo.srcObject = null;
        elements.minimizedRemoteVideo.srcObject = null;
        
        // Reset UI
        elements.micOnIcon.classList.remove('hidden');
        elements.micOffIcon.classList.add('hidden');
        elements.videoOnIcon.classList.remove('hidden');
        elements.videoOffIcon.classList.add('hidden');
        elements.localVideo.style.display = 'block';
    }

    // ========== CALL CONTROLS ==========

    elements.toggleMicBtn.addEventListener('click', function() {
        if (!state.localStream) return;
        
        state.isMuted = !state.isMuted;
        state.localStream.getAudioTracks().forEach(track => {
            track.enabled = !state.isMuted;
        });
        
        if (state.isMuted) {
            elements.micOnIcon.classList.add('hidden');
            elements.micOffIcon.classList.remove('hidden');
            this.classList.add('bg-red-500/50');
        } else {
            elements.micOnIcon.classList.remove('hidden');
            elements.micOffIcon.classList.add('hidden');
            this.classList.remove('bg-red-500/50');
        }
    });

    elements.toggleVideoBtn.addEventListener('click', async function() {
        if (!state.localStream) return;
        
        state.isVideoEnabled = !state.isVideoEnabled;
        state.localStream.getVideoTracks().forEach(track => {
            track.enabled = state.isVideoEnabled;
        });
        
        if (state.isVideoEnabled) {
            elements.videoOnIcon.classList.remove('hidden');
            elements.videoOffIcon.classList.add('hidden');
            elements.localVideo.style.display = 'block';
        } else {
            elements.videoOnIcon.classList.add('hidden');
            elements.videoOffIcon.classList.remove('hidden');
            elements.localVideo.style.display = 'none';
        }
    });

    elements.switchCameraBtn.addEventListener('click', async function() {
        if (!state.localStream || !state.isVideoCall || !state.isVideoEnabled) return;
        
        try {
            state.currentCamera = state.currentCamera === 'user' ? 'environment' : 'user';
            
            const videoTrack = state.localStream.getVideoTracks()[0];
            if (videoTrack) {
                // Mobile-optimized constraints for camera switch
                let videoConstraints = isMobile ? {
                    facingMode: state.currentCamera,
                    width: { ideal: 640, max: 1280 },
                    height: { ideal: 480, max: 720 },
                    frameRate: { ideal: 30, max: 30 }
                } : {
                    facingMode: state.currentCamera
                };
                
                let newStream;
                try {
                    newStream = await navigator.mediaDevices.getUserMedia({
                        video: videoConstraints,
                        audio: false
                    });
                } catch (error) {
                    // Fallback: try without specific constraints
                    if (error.name === 'OverconstrainedError' || error.name === 'NotReadableError') {
                        console.warn('Camera switch with constraints failed, trying simple...');
                        newStream = await navigator.mediaDevices.getUserMedia({
                            video: { facingMode: state.currentCamera },
                            audio: false
                        });
                    } else {
                        throw error;
                    }
                }
                
                const newVideoTrack = newStream.getVideoTracks()[0];
                const sender = state.peerConnection.getSenders().find(s => 
                    s.track && s.track.kind === 'video'
                );
                
                if (sender) {
                    await sender.replaceTrack(newVideoTrack);
                    console.log('Camera track replaced successfully');
                } else {
                    console.warn('No video sender found');
                }
                
                videoTrack.stop();
                state.localStream.removeTrack(videoTrack);
                state.localStream.addTrack(newVideoTrack);
                elements.localVideo.srcObject = state.localStream;
                
                // Stop unused audio track from new stream
                newStream.getAudioTracks().forEach(track => track.stop());
            }
        } catch (error) {
            console.error('Error switching camera:', error);
            // Revert camera state on error
            state.currentCamera = state.currentCamera === 'user' ? 'environment' : 'user';
            alert('Erro ao trocar câmera. Verifique se o dispositivo suporta múltiplas câmeras.');
        }
    });

    elements.switchToVoiceBtn.addEventListener('click', async function() {
        if (!state.localStream || !state.isVideoCall) return;
        
        // Stop video tracks
        state.localStream.getVideoTracks().forEach(track => {
            track.stop();
            state.localStream.removeTrack(track);
        });
        
        state.isVideoCall = false;
        state.isVideoEnabled = false;
        elements.localVideo.style.display = 'none';
        elements.videoOnIcon.classList.add('hidden');
        elements.videoOffIcon.classList.remove('hidden');
        
        // Update peer connection
        const sender = state.peerConnection.getSenders().find(s => 
            s.track && s.track.kind === 'video'
        );
        if (sender) {
            await sender.replaceTrack(null);
        }
    });

    elements.minimizeCallBtn.addEventListener('click', function() {
        state.isCallMinimized = true;
        elements.callOverlay.classList.add('hidden');
        elements.minimizedCallWidget.classList.remove('hidden');
    });

    elements.minimizedCallWidget.addEventListener('click', function() {
        state.isCallMinimized = false;
        elements.callOverlay.classList.remove('hidden');
        elements.minimizedCallWidget.classList.add('hidden');
    });

    elements.hangupBtn.addEventListener('click', () => endCall(true));
    elements.acceptCallBtn.addEventListener('click', () => {
        elements.incomingCallModal.classList.add('hidden');
        acceptIncomingCall();
    });
    elements.rejectCallBtn.addEventListener('click', () => {
        sendSignal('reject', {});
        // Mark pending offer signal as processed
        if (pendingOfferSignalId) {
            state.processedSignalIds.add(pendingOfferSignalId);
        }
        pendingOffer = null;
        pendingOfferSignalId = null;
        state.isAcceptingCall = false;
        elements.incomingCallModal.classList.add('hidden');
    });

    // ========== CALL BUTTONS ==========

    elements.voiceCallBtn.addEventListener('click', () => startCall(false));
    elements.videoCallBtn.addEventListener('click', () => startCall(true));

    // ========== REVERB WEBSOCKET (substitui polling) ==========
    
    // Configurar Echo para mensagens e sinais
    const roomCode = "{{ $chatRoom->room_code }}";
    const currentUserType = "{{ auth('admin')->check() ? 'admin' : 'client' }}";
    
    // Escutar mensagens do chat
    window.Echo.channel(`chat-room.${roomCode}`)
        .listen('.chat.message.sent', (event) => {
            console.log('Nova mensagem recebida via WebSocket:', event);
            
            // Evitar mensagens duplicadas
            if (event.message && event.message.id > state.lastMessageId) {
                addMessageToChat(event.message);
                state.lastMessageId = event.message.id;
            }
        })
        .listen('.call.signal', (event) => {
            console.log('Sinal WebRTC recebido via WebSocket:', event);
            
            if (!event.data) {
                console.error('Sinal sem dados:', event);
                return;
            }
            
            const signalData = event.data;
            
            // Evitar processar sinais enviados por nós mesmos
            if (signalData.sender_type === currentUserType) {
                console.log('Ignorando sinal enviado por nós mesmos');
                return;
            }
            
            // Processar sinal baseado no tipo
            switch(signalData.type) {
                case 'offer':
                    // Clean SDP antes de processar
                    if (signalData.payload && signalData.payload.sdp) {
                        const cleanedSDP = cleanSDP(signalData.payload.sdp);
                        if (!cleanedSDP) {
                            console.error('Invalid SDP in offer');
                            return;
                        }
                        signalData.payload.sdp = cleanedSDP;
                    }
                    handleIncomingOffer(signalData.payload, signalData.signal_id || null);
                    break;
                case 'answer':
                    // Clean SDP antes de processar
                    if (signalData.payload && signalData.payload.sdp) {
                        const cleanedSDP = cleanSDP(signalData.payload.sdp);
                        if (!cleanedSDP) {
                            console.error('Invalid SDP in answer');
                            return;
                        }
                        signalData.payload.sdp = cleanedSDP;
                    }
                    handleIncomingAnswer(signalData.payload);
                    break;
                case 'candidate':
                    // Only process candidates if we have a peer connection
                    if (state.peerConnection) {
                        handleIncomingCandidate(signalData.payload);
                    }
                    break;
                case 'hangup':
                case 'reject':
                    endCall(false);
                    if (signalData.type === 'reject') {
                        alert('Chamada recusada.');
                    }
                    break;
            }
        });
    
    console.log('WebSocket Reverb configurado para chat e chamadas');

    // Initial scroll
    scrollToBottom();
    }); // Fecha waitForEcho
})(); // Fecha função anônima auto-executável
</script>
@endsection