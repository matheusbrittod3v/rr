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
                                            <div class="mt-2 p-2 bg-black/10 rounded-lg text-sm flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg> 
                                                <a href="/storage/${message.file_path}" target="_blank" class="hover:underline">Ver anexo</a>
                                            </div>
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
                        <input type="file" id="file-input" class="hidden" accept="image/*,video/*,.pdf,.doc,.docx">
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

            <!-- Call Controls -->
            <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex items-center space-x-4 p-4 rounded-2xl bg-black/20 backdrop-blur-md">
                <button id="toggle-mic-btn" class="p-4 rounded-full bg-white/10 hover:bg-white/20 text-white transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                    </svg>
                </button>
                <button id="toggle-video-btn" class="p-4 rounded-full bg-white/10 hover:bg-white/20 text-white transition-all">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
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
        callStatus: document.getElementById('call-status'),
        toggleMicBtn: document.getElementById('toggle-mic-btn'),
        toggleVideoBtn: document.getElementById('toggle-video-btn'),
        hangupBtn: document.getElementById('hangup-btn')
    };
    
    // Variables
    const config = {
        roomCode: "{{ $chatRoom->room_code }}",
        csrfToken: "{{ csrf_token() }}",
        iceServers: {
            iceServers: [
                { urls: 'stun:stun.l.google.com:19302' },
                { urls: 'stun:stun1.l.google.com:19302' },
            ]
        }
    };
    
    let state = {
        peerConnection: null,
        localStream: null,
        lastSignalId: 0,
        pendingOffer: null,
        inCall: false,
        lastMessageId: {{ $chatRoom->messages->last()->id ?? 0 }}
    };
    
    // --- CHAT FUNCTIONS ---
    function scrollToBottom() {
        elements.chatMessages.scrollTop = elements.chatMessages.scrollHeight;
    }
    scrollToBottom();
    
    elements.messageInput.addEventListener('input', function() {
        elements.charCount.textContent = `${this.value.length}/1000`;
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 150) + 'px';
    });
    
    elements.attachButton.addEventListener('click', () => elements.fileInput.click());
    elements.fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            elements.fileName.textContent = this.files[0].name;
            elements.filePreview.classList.remove('hidden');
        }
    });
    elements.removeFileBtn.addEventListener('click', () => {
        elements.fileInput.value = '';
        elements.filePreview.classList.add('hidden');
    });
    
    elements.messageInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            elements.chatForm.dispatchEvent(new Event('submit'));
        }
    });
    
    elements.chatForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const message = elements.messageInput.value.trim();
        const file = elements.fileInput.files[0];
        
        if (!message && !file) return;
        
        elements.sendButton.disabled = true;
        const formData = new FormData();
        formData.append('message', message);
        if (file) formData.append('file', file);
        
        fetch("{{ route('chat.send-message', $chatRoom->room_code) }}", {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': config.csrfToken }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                elements.messageInput.value = '';
                elements.fileInput.value = '';
                elements.filePreview.classList.add('hidden');
                elements.messageInput.style.height = 'auto';
                elements.charCount.textContent = '0/1000';
                addMessageToChat(data.message);
                scrollToBottom();
                state.lastMessageId = data.message.id;
            }
        })
        .finally(() => {
            elements.sendButton.disabled = false;
            elements.messageInput.focus();
        });
    });

    function addMessageToChat(message) {
        // Simplified HTML construction for brevity
        const div = document.createElement('div');
        div.className = 'mb-4 animate-fade-in';
        const isClient = message.sender_type === 'client';
        const isAdmin = message.sender_type === 'admin';
        const align = isClient ? 'justify-end' : 'justify-start';
        let bg = isClient ? 'bg-gradient-to-br from-blue-500 to-blue-600 text-white' : 
                 (isAdmin ? 'bg-purple-100 text-purple-900 border border-purple-200' : 'bg-white text-gray-800 border border-gray-200');
        
        let content = message.message ? `<p class="text-sm break-words">${escapeHtml(message.message)}</p>` : '';
        if(message.file_path) content += `<div class="mt-2 p-2 bg-black/10 rounded-lg text-sm"><a href="/storage/${message.file_path}" target="_blank" class="hover:underline flex items-center gap-1">📎 Anexo</a></div>`;

        div.innerHTML = `<div class="flex ${align}"><div class="max-w-xs sm:max-w-md"><div class="px-4 py-3 rounded-2xl shadow-sm ${bg} rounded-${isClient ? 'br' : 'bl'}-sm">${content}<p class="text-xs mt-1 opacity-70">${isAdmin ? 'Admin • ' : ''}${new Date(message.created_at).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})}</p></div></div></div>`;
        elements.chatMessages.appendChild(div);
    }

    function escapeHtml(text) {
        return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
    }

    // --- WEBRTC FUNCTIONS ---

    function createPeerConnection() {
        if (state.peerConnection) return;
        
        state.peerConnection = new RTCPeerConnection(config.iceServers);
        
        state.peerConnection.onicecandidate = (event) => {
            if (event.candidate) sendSignal('candidate', event.candidate);
        };
        
        state.peerConnection.ontrack = (event) => {
            if (elements.remoteVideo.srcObject !== event.streams[0]) {
                elements.remoteVideo.srcObject = event.streams[0];
                elements.callStatus.classList.add('hidden');
            }
        };

        state.peerConnection.onconnectionstatechange = () => {
            if (['disconnected', 'failed', 'closed'].includes(state.peerConnection.connectionState)) {
                endCall(false); // Don't send signal, just close locally
            }
        };
    }

    async function startCall(videoEnabled) {
        elements.callOverlay.classList.remove('hidden');
        elements.callStatus.classList.remove('hidden');
        elements.callStatus.querySelector('div').textContent = 'Chamando...';
        state.inCall = true;

        try {
            state.localStream = await navigator.mediaDevices.getUserMedia({ video: videoEnabled, audio: true });
            elements.localVideo.srcObject = state.localStream;
            
            // Adjust toggle buttons based on initial state
            if(!videoEnabled) elements.toggleVideoBtn.classList.add('bg-red-500');

            createPeerConnection();
            state.localStream.getTracks().forEach(track => state.peerConnection.addTrack(track, state.localStream));
            
            const offer = await state.peerConnection.createOffer();
            await state.peerConnection.setLocalDescription(offer);
            
            sendSignal('offer', offer);
            
        } catch (err) {
            console.error(err);
            alert('Erro ao acessar mídia.');
            endCall();
        }
    }

    function handleIncomingOffer(offer) {
        if (state.inCall || !elements.incomingCallModal.classList.contains('hidden')) return; // Busy
        
        state.pendingOffer = offer;
        elements.incomingCallModal.classList.remove('hidden');
    }

    elements.acceptCallBtn.addEventListener('click', async () => {
        elements.incomingCallModal.classList.add('hidden');
        elements.callOverlay.classList.remove('hidden');
        elements.callStatus.classList.remove('hidden');
        elements.callStatus.querySelector('div').textContent = 'Conectando...';
        state.inCall = true;

        try {
            // Try video first, fallback to audio if no camera
            try {
                state.localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
            } catch(e) {
                 state.localStream = await navigator.mediaDevices.getUserMedia({ video: false, audio: true });
                 elements.toggleVideoBtn.classList.add('bg-red-500');
            }
            elements.localVideo.srcObject = state.localStream;

            createPeerConnection();
            state.localStream.getTracks().forEach(track => state.peerConnection.addTrack(track, state.localStream));

            await state.peerConnection.setRemoteDescription(new RTCSessionDescription(state.pendingOffer));
            const answer = await state.peerConnection.createAnswer();
            await state.peerConnection.setLocalDescription(answer);
            
            sendSignal('answer', answer);
            state.pendingOffer = null;

        } catch (err) {
            console.error(err);
            endCall();
        }
    });

    elements.rejectCallBtn.addEventListener('click', () => {
        elements.incomingCallModal.classList.add('hidden');
        state.pendingOffer = null;
        sendSignal('reject', {});
    });

    async function handleIncomingAnswer(answer) {
        if (!state.peerConnection) return;
        await state.peerConnection.setRemoteDescription(new RTCSessionDescription(answer));
    }

    async function handleIncomingCandidate(candidate) {
        if (!state.peerConnection) return;
        try { await state.peerConnection.addIceCandidate(new RTCIceCandidate(candidate)); } catch(e){}
    }

    function sendSignal(type, payload) {
        fetch("{{ route('chat.signal', $chatRoom->room_code) }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrfToken },
            body: JSON.stringify({ type, payload })
        });
    }

    function endCall(notify = true) {
        if (notify) sendSignal('hangup', {});
        
        if (state.peerConnection) {
            state.peerConnection.close();
            state.peerConnection = null;
        }
        if (state.localStream) {
            state.localStream.getTracks().forEach(track => track.stop());
            state.localStream = null;
        }
        
        elements.callOverlay.classList.add('hidden');
        elements.incomingCallModal.classList.add('hidden');
        elements.localVideo.srcObject = null;
        elements.remoteVideo.srcObject = null;
        state.inCall = false;
        state.pendingOffer = null;
        
        // Reset buttons
        elements.toggleVideoBtn.classList.remove('bg-red-500');
        elements.toggleVideoBtn.classList.add('bg-white/10');
        elements.toggleMicBtn.classList.remove('bg-red-500');
        elements.toggleMicBtn.classList.add('bg-white/10');
    }

    elements.hangupBtn.addEventListener('click', () => endCall(true));
    elements.voiceCallBtn.addEventListener('click', () => startCall(false));
    elements.videoCallBtn.addEventListener('click', () => startCall(true));
    
    // Toggle Mute
    elements.toggleMicBtn.addEventListener('click', () => {
        if(state.localStream) {
            const track = state.localStream.getAudioTracks()[0];
            if(track) {
                track.enabled = !track.enabled;
                elements.toggleMicBtn.classList.toggle('bg-red-500');
                elements.toggleMicBtn.classList.toggle('bg-white/10');
            }
        }
    });

    // Toggle Video
    elements.toggleVideoBtn.addEventListener('click', () => {
        if(state.localStream) {
            const track = state.localStream.getVideoTracks()[0];
            if(track) {
                track.enabled = !track.enabled;
                elements.toggleVideoBtn.classList.toggle('bg-red-500');
                elements.toggleVideoBtn.classList.toggle('bg-white/10');
            }
        }
    });

    // Polling
    setInterval(() => {
        // Messages
        fetch("{{ route('chat.get-messages', $chatRoom->room_code) }}")
            .then(res => res.json())
            .then(data => {
                if(data.success && data.messages.length > 0) {
                    const newMsgs = data.messages.filter(m => m.id > state.lastMessageId);
                    newMsgs.forEach(m => { addMessageToChat(m); state.lastMessageId = m.id; });
                    if(newMsgs.length) scrollToBottom();
                }
            });
            
        // Signals
        fetch("{{ route('chat.signals', $chatRoom->room_code) }}?last_signal_id=" + state.lastSignalId)
            .then(res => res.json())
            .then(data => {
                if(data.success && data.signals.length > 0) {
                    data.signals.forEach(signal => {
                        state.lastSignalId = signal.id; // Update immediately
                        const payload = JSON.parse(signal.payload);
                        
                        switch(signal.type) {
                            case 'offer': handleIncomingOffer(payload); break;
                            case 'answer': handleIncomingAnswer(payload); break;
                            case 'candidate': handleIncomingCandidate(payload); break;
                            case 'hangup': 
                            case 'reject':
                                endCall(false); 
                                if(signal.type === 'reject') alert('Chamada recusada.');
                                break;
                        }
                    });
                }
            });
    }, 2000);

});
</script>
@endsection