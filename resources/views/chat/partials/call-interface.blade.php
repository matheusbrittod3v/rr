{{-- Interface de Chamada de Voz/Vídeo WebRTC --}}

{{-- Botões de Chamada no Header do Chat --}}
<div class="flex items-center space-x-2">
    <button 
        id="voice-call-btn"
        class="p-2 bg-white/10 text-white hover:bg-white/20 rounded-lg transition-all backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-white"
        title="Chamada de voz"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
        </svg>
    </button>
    
    <button 
        id="video-call-btn"
        class="p-2 bg-white/10 text-white hover:bg-white/20 rounded-lg transition-all backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-white"
        title="Chamada de vídeo"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
        </svg>
    </button>
</div>

{{-- Modal de Chamada Recebida --}}
<div id="incoming-call-modal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-sm w-full transform transition-all">
        <div class="text-center">
            <div class="mx-auto w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mb-4 animate-pulse">
                <svg class="w-10 h-10 text-white" id="incoming-call-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
            </div>
            
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Chamada Recebida</h3>
            <p class="text-gray-600 mb-6" id="caller-name">{{ session('chat_user_role') === 'client' ? 'Prestador' : 'Cliente' }}</p>
            <p class="text-sm text-gray-500 mb-8" id="call-type-label">Chamada de voz</p>
            
            <div class="flex space-x-3">
                <button 
                    id="reject-call-btn"
                    class="flex-1 px-6 py-3 bg-red-500 text-white rounded-xl hover:bg-red-600 transition-all transform hover:scale-105 active:scale-95 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                >
                    <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Recusar
                </button>
                
                <button 
                    id="accept-call-btn"
                    class="flex-1 px-6 py-3 bg-green-500 text-white rounded-xl hover:bg-green-600 transition-all transform hover:scale-105 active:scale-95 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                >
                    <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Aceitar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Interface de Chamada Ativa --}}
<div id="call-interface" class="hidden fixed inset-0 bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 z-50">
    {{-- Vídeo Container --}}
    <div class="relative w-full h-full">
        {{-- Vídeo Remoto (Principal) --}}
        <video id="remote-video" class="w-full h-full object-cover" autoplay playsinline></video>
        
        {{-- Vídeo Local (PIP - Picture in Picture) --}}
        <div id="local-video-container" class="absolute top-4 right-4 w-32 h-32 sm:w-48 sm:h-48 bg-gray-900 rounded-2xl overflow-hidden shadow-2xl border-2 border-white/20">
            <video id="local-video" class="w-full h-full object-cover" autoplay playsinline muted></video>
        </div>
        
        {{--  Status da Chamada --}}
        <div class="absolute top-4 left-4 bg-black/50 backdrop-blur-md rounded-xl px-4 py-2 text-white">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                <span id="call-duration" class="text-sm font-medium">00:00</span>
            </div>
        </div>
        
        {{-- Controles da Chamada --}}
        <div class="absolute bottom-0 left-0 right-0 p-6" id="call-controls">
            <div class="max-w-2xl mx-auto bg-white/10 backdrop-blur-xl rounded-2xl p-4 border border-white/20">
                <div class="flex items-center justify-center space-x-4">
                    {{-- Mutar/Desmutar Microfone --}}
                    <button 
                        id="toggle-mic-btn"
                        class="p-4 bg-white/20 hover:bg-white/30 rounded-full transition-all transform hover:scale-110 active:scale-95 focus:outline-none focus:ring-2 focus:ring-white"
                        title="Mutar/Desmutar"
                    >
                        <svg id="mic-on-icon" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                        </svg>
                        <svg id="mic-off-icon" class="w-6 h-6 text-white hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
                        </svg>
                    </button>
                    
                    {{-- Ativar/Desativar Câmera --}}
                    <button 
                        id="toggle-camera-btn"
                        class="p-4 bg-white/20 hover:bg-white/30 rounded-full transition-all transform hover:scale-110 active:scale-95 focus:outline-none focus:ring-2 focus:ring-white"
                        title="Ativar/Desativar câmera"
                    >
                        <svg id="camera-on-icon" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <svg id="camera-off-icon" class="w-6 h-6 text-white hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                        </svg>
                    </button>
                    
                    {{-- Virar Câmera (Mobile) --}}
                    <button 
                        id="flip-camera-btn"
                        class="p-4 bg-white/20 hover:bg-white/30 rounded-full transition-all transform hover:scale-110 active:scale-95 focus:outline-none focus:ring-2 focus:ring-white md:hidden"
                        title="Virar câmera"
                    >
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                    
                    {{-- Encerrar Chamada --}}
                    <button 
                        id="end-call-btn"
                        class="p-4 bg-red-500 hover:bg-red-600 rounded-full transition-all transform hover:scale-110 active:scale-95 focus:outline-none focus:ring-2 focus:ring-white shadow-lg"
                        title="Encerrar chamada"
                    >
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M5 3a2 2 0 00-2 2v1c0 8.284 6.716 15 15 15h1a2 2 0 002-2v-3.28a1 1 0 00-.684-.948l-4.493-1.498a1 1 0 00-1.21.502l-1.13 2.257a11.042 11.042 0 01-5.516-5.517l2.257-1.128a1 1 0 00.502-1.21L9.228 3.683A1 1 0 008.279 3H5z" />
                        </svg>
                    </button>
                    
                    {{-- Minimizar --}}
                    <button 
                        id="minimize-call-btn"
                        class="p-4 bg-white/20 hover:bg-white/30 rounded-full transition-all transform hover:scale-110 active:scale-95 focus:outline-none focus:ring-2 focus:ring-white"
                        title="Minimizar"
                    >
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Chamada Minimizada --}}
<div id="minimized-call" class="hidden fixed bottom-4 right-4 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl shadow-2xl p-4 z-40 cursor-pointer hover:scale-105 transition-transform" onclick="maximizeCall()">
    <div class="flex items-center space-x-3">
        <div class="w-12 h-12 bg-white/20 rounded-full overflow-hidden">
            <video id="minimized-video" class="w-full h-full object-cover" autoplay playsinline muted></video>
        </div>
        <div class="text-white">
            <p class="font-medium">Chamada em andamento</p>
            <p class="text-xs opacity-80" id="minimized-duration">00:00</p>
        </div>
        <button onclick="event.stopPropagation(); endCall();" class="p-2 bg-red-500 hover:bg-red-600 rounded-full transition-all">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>
