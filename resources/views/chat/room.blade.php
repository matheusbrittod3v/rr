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

<script>
document.addEventListener('DOMContentLoaded', function() {
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
        callTimerInterval: null
    };
    
    // WebRTC Config
    const rtcConfig = {
        iceServers: [
            { urls: 'stun:stun.l.google.com:19302' },
            { urls: 'stun:stun1.l.google.com:19302' }
        ]
    };
    
    // Config
    const config = {
        roomCode: "{{ $chatRoom->room_code }}",
        csrfToken: "{{ csrf_token() }}",
        sendMessageUrl: "{{ route('chat.send-message', $chatRoom->room_code) }}",
        getMessagesUrl: "{{ route('chat.get-messages', $chatRoom->room_code) }}",
        signalsUrl: "{{ route('chat.signals', $chatRoom->room_code) }}",
        sendSignalUrl: "{{ route('chat.send-signal', $chatRoom->room_code) }}"
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
            state.localStream.getTracks().forEach(track => {
                state.peerConnection.addTrack(track, state.localStream);
            });
        }
        
        // Handle remote stream
        state.peerConnection.ontrack = (event) => {
            const [remoteStream] = event.streams;
            state.remoteStream = remoteStream;
            elements.remoteVideo.srcObject = remoteStream;
            elements.minimizedRemoteVideo.srcObject = remoteStream;
            
            // Start timer when connection is established
            if (state.inCall && !state.callStartTime) {
                startCallTimer();
            }
        };
        
        // Handle ICE candidates
        state.peerConnection.onicecandidate = (event) => {
            if (event.candidate) {
                sendSignal('candidate', event.candidate);
            }
        };
        
        // Handle connection state
        state.peerConnection.onconnectionstatechange = () => {
            console.log('Connection state:', state.peerConnection.connectionState);
            if (state.peerConnection.connectionState === 'failed' || 
                state.peerConnection.connectionState === 'disconnected') {
                updateCallStatus('Conexão perdida');
            }
        };
    }

    async function getLocalStream(video = true) {
        try {
            const constraints = {
                audio: true,
                video: video ? {
                    facingMode: state.currentCamera
                } : false
            };
            
            const stream = await navigator.mediaDevices.getUserMedia(constraints);
            state.localStream = stream;
            elements.localVideo.srcObject = stream;
            state.isVideoEnabled = video;
            return stream;
        } catch (error) {
            console.error('Error accessing media devices:', error);
            alert('Erro ao acessar câmera/microfone. Verifique as permissões.');
            throw error;
        }
    }

    async function startCall(isVideo = true) {
        try {
            state.isVideoCall = isVideo;
            await getLocalStream(isVideo);
            await createPeerConnection();
            
            const offer = await state.peerConnection.createOffer();
            await state.peerConnection.setLocalDescription(offer);
            
            sendSignal('offer', offer);
            showCallOverlay();
            updateCallStatus('Chamando...');
        } catch (error) {
            console.error('Error starting call:', error);
            endCall(false);
        }
    }

    let pendingOffer = null;

    async function handleIncomingOffer(offer) {
        try {
            if (state.inCall) return;
            
            const isVideo = offer.sdp && offer.sdp.includes('video');
            state.isVideoCall = isVideo;
            pendingOffer = offer;
            
            showIncomingCallModal();
        } catch (error) {
            console.error('Error handling offer:', error);
        }
    }

    async function acceptIncomingCall() {
        try {
            if (!pendingOffer) return;
            
            const isVideo = pendingOffer.sdp && pendingOffer.sdp.includes('video');
            state.isVideoCall = isVideo;
            
            await getLocalStream(isVideo);
            await createPeerConnection();
            
            await state.peerConnection.setRemoteDescription(new RTCSessionDescription(pendingOffer));
            const answer = await state.peerConnection.createAnswer();
            await state.peerConnection.setLocalDescription(answer);
            
            sendSignal('answer', answer);
            showCallOverlay();
            updateCallStatus('Conectando...');
            startCallTimer();
            pendingOffer = null;
        } catch (error) {
            console.error('Error accepting call:', error);
            endCall(false);
        }
    }

    async function handleIncomingAnswer(answer) {
        try {
            if (!state.peerConnection) return;
            await state.peerConnection.setRemoteDescription(new RTCSessionDescription(answer));
            updateCallStatus('Conectado');
            startCallTimer();
            setTimeout(() => {
                elements.callStatus.classList.add('hidden');
            }, 2000);
        } catch (error) {
            console.error('Error handling answer:', error);
        }
    }

    async function handleIncomingCandidate(candidate) {
        try {
            if (!state.peerConnection) return;
            await state.peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
        } catch (error) {
            console.error('Error handling candidate:', error);
        }
    }

    function sendSignal(type, payload) {
        fetch(config.sendSignalUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': config.csrfToken
            },
            body: JSON.stringify({ type, payload })
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
        
        pendingOffer = null;
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
                const newStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: state.currentCamera },
                    audio: false
                });
                
                const newVideoTrack = newStream.getVideoTracks()[0];
                const sender = state.peerConnection.getSenders().find(s => 
                    s.track && s.track.kind === 'video'
                );
                
                if (sender) {
                    await sender.replaceTrack(newVideoTrack);
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
        pendingOffer = null;
        endCall(false);
    });

    // ========== CALL BUTTONS ==========

    elements.voiceCallBtn.addEventListener('click', () => startCall(false));
    elements.videoCallBtn.addEventListener('click', () => startCall(true));

    // ========== POLLING ==========

    // Message polling
    setInterval(() => {
        fetch(config.getMessagesUrl)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.messages && data.messages.length > 0) {
                    const newMsgs = data.messages.filter(m => m.id > state.lastMessageId);
                    newMsgs.forEach(m => {
                        addMessageToChat(m);
                        state.lastMessageId = m.id;
                    });
                }
            })
            .catch(e => console.error("Message poll error", e));
    }, 3000);

    // Signal polling
    setInterval(() => {
        fetch(`${config.signalsUrl}?last_signal_id=${state.lastSignalId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.signals && data.signals.length > 0) {
                    data.signals.forEach(signal => {
                        state.lastSignalId = signal.id;
                        
                        const payload = JSON.parse(signal.payload);
                        
                        if (state.inCall && (signal.type === 'offer' || signal.type === 'answer')) {
                            console.warn('Ignored conflicting signal during active call:', signal.type);
                            return;
                        }
                        
                        if (!elements.incomingCallModal.classList.contains('hidden') && signal.type === 'offer') {
                            return;
                        }

                        switch(signal.type) {
                            case 'offer':
                                handleIncomingOffer(payload);
                                break;
                            case 'answer':
                                handleIncomingAnswer(payload);
                                break;
                            case 'candidate':
                                handleIncomingCandidate(payload);
                                break;
                            case 'hangup':
                            case 'reject':
                                endCall(false);
                                if (signal.type === 'reject') {
                                    alert('Chamada recusada.');
                                }
                                break;
                        }
                    });
                }
            })
            .catch(e => console.error("Signal poll error", e));
    }, 2000);

    // Initial scroll
    scrollToBottom();
});
</script>
@endsection