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
                        <!-- Call Buttons (WebRTC Placeholders) -->
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

            <!-- Participants Info -->
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
                <!-- Messages will be loaded here -->
                @foreach($chatRoom->messages as $message)
                    <div class="mb-4 animate-fade-in" data-message-id="{{ $message->id }}">
                        <div class="flex {{ $message->sender_type === 'client' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-xs sm:max-w-md lg:max-w-lg">
                                <div class="flex items-end gap-2 {{ $message->sender_type === 'client' ? 'flex-row-reverse' : 'flex-row' }}">
                                    <!-- Sender Avatar/Initial usually implied by side, but for Admin lets show -->
                                    @if($message->sender_type === 'admin')
                                        <div class="w-6 h-6 rounded-full bg-purple-600 flex items-center justify-center text-xs text-white" title="Admin">A</div>
                                    @endif

                                    <!-- Message bubble -->
                                    <div class="px-4 py-3 rounded-2xl shadow-sm {{ 
                                        $message->sender_type === 'client' ? 'bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-br-sm' : 
                                        ($message->sender_type === 'admin' ? 'bg-purple-100 text-purple-900 border border-purple-200 rounded-bl-sm' : 
                                        'bg-white text-gray-800 border border-gray-200 rounded-bl-sm') 
                                    }}">
                                        @if($message->message)
                                            <p class="text-sm break-words">{{ $message->message }}</p>
                                        @endif
                                        
                                        @if($message->file_path)
                                            @if(Str::startsWith($message->file_type, 'image/'))
                                                <div class="mt-2 rounded-lg overflow-hidden">
                                                    <img src="{{ asset('storage/' . $message->file_path) }}" alt="Imagem" class="max-w-full h-auto rounded-lg">
                                                </div>
                                            @elseif(Str::startsWith($message->file_type, 'video/'))
                                                <div class="mt-2 rounded-lg overflow-hidden">
                                                    <video controls class="max-w-full h-auto rounded-lg">
                                                        <source src="{{ asset('storage/' . $message->file_path) }}" type="{{ $message->file_type }}">
                                                        Seu navegador não suporta vídeos.
                                                    </video>
                                                </div>
                                            @else
                                                <div class="mt-2 p-2 {{ $message->sender_type === 'client' ? 'bg-blue-400/30' : 'bg-gray-100' }} rounded-lg">
                                                    <a href="{{ asset('storage/' . $message->file_path) }}" target="_blank" class="{{ $message->sender_type === 'client' ? 'text-white' : 'text-blue-600' }} hover:underline flex items-center text-sm">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                        </svg>
                                                        Arquivo anexado
                                                    </a>
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

            <!-- Input Area -->
            <div class="bg-white border-t border-gray-200 p-4">
                <form id="chat-form" class="flex items-end space-x-3">
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
                        <input type="file" id="file-input" class="hidden" accept="image/*,video/*,.pdf,.doc,.docx">
                        <button 
                            type="button" 
                            id="attach-button" 
                            class="p-3 bg-gray-100 rounded-xl hover:bg-gray-200 transition-all hover:scale-105"
                            title="Anexar arquivo"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                        </button>
                        <button 
                            type="submit" 
                            id="send-button"
                            class="p-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all hover:scale-105 shadow-lg"
                            title="Enviar mensagem"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </div>
                </form>
                
                <div id="file-preview" class="hidden mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span id="file-name" class="text-sm text-gray-700 font-medium"></span>
                        </div>
                        <button type="button" id="remove-file" class="text-red-500 hover:text-red-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
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
    #chat-messages::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    const fileInput = document.getElementById('file-input');
    const attachButton = document.getElementById('attach-button');
    const sendButton = document.getElementById('send-button');
    const chatMessages = document.getElementById('chat-messages');
    const filePreview = document.getElementById('file-preview');
    const fileName = document.getElementById('file-name');
    const removeFileBtn = document.getElementById('remove-file');
    const charCount = document.getElementById('char-count');
    
    // Call buttons (placeholders for now)
    const voiceCallBtn = document.getElementById('voice-call-btn');
    const videoCallBtn = document.getElementById('video-call-btn');

    voiceCallBtn.addEventListener('click', () => {
        alert('Funcionalidade de chamada de voz em desenvolvimento.');
    });

    videoCallBtn.addEventListener('click', () => {
        alert('Funcionalidade de chamada de vídeo em desenvolvimento.');
    });
    
    let lastMessageId = {{ $chatRoom->messages->last()->id ?? 0 }};
    let pollingInterval = null;
    
    // Focus on message input
    messageInput.focus();
    
    // Scroll to bottom
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    scrollToBottom();
    
    // Character counter
    messageInput.addEventListener('input', function() {
        charCount.textContent = `${this.value.length}/1000`;
        
        // Auto-resize textarea
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 150) + 'px';
    });
    
    // Attach file button
    attachButton.addEventListener('click', () => fileInput.click());
    
    // File input change
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            const file = this.files[0];
            fileName.textContent = file.name;
            filePreview.classList.remove('hidden');
        }
    });
    
    // Remove file
    removeFileBtn.addEventListener('click', function() {
        fileInput.value = '';
        filePreview.classList.add('hidden');
    });
    
    // Handle Enter key (without Shift)
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatForm.dispatchEvent(new Event('submit'));
        }
    });
    
    // Send message
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        const file = fileInput.files[0];
        
        if (!message && !file) return;
        
        // Disable send button
        sendButton.disabled = true;
        sendButton.innerHTML = '<svg class="animate-spin h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
        
        const formData = new FormData();
        formData.append('message', message);
        if (file) formData.append('file', file);
        
        fetch("{{ route('chat.send-message', $chatRoom->room_code) }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Clear inputs
                messageInput.value = '';
                fileInput.value = '';
                filePreview.classList.add('hidden');
                messageInput.style.height = 'auto';
                charCount.textContent = '0/1000';
                
                // Add message to chat immediately
                addMessageToChat(data.message);
                scrollToBottom();
                
                // Update last message ID
                lastMessageId = data.message.id;
            } else {
                alert('Erro ao enviar mensagem.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao enviar mensagem.');
        })
        .finally(() => {
            sendButton.disabled = false;
            sendButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>';
            messageInput.focus();
        });
    });
    
    // Add message to chat
    function addMessageToChat(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'mb-4 animate-fade-in';
        messageDiv.dataset.messageId = message.id;
        
        const isClient = message.sender_type === 'client';
        const isAdmin = message.sender_type === 'admin';
        const alignment = isClient ? 'justify-end' : 'justify-start';
        
        let bubbleClass = 'bg-white text-gray-800 border border-gray-200 rounded-bl-sm';
        let timeClass = 'text-gray-500';
        
        if (isClient) {
            bubbleClass = 'bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-br-sm';
            timeClass = 'text-blue-100';
        } else if (isAdmin) {
             bubbleClass = 'bg-purple-100 text-purple-900 border border-purple-200 rounded-bl-sm';
             timeClass = 'text-purple-400';
        }
        
        let fileHtml = '';
        if (message.file_path) {
            if (message.file_type && message.file_type.startsWith('image/')) {
                fileHtml = `<div class="mt-2 rounded-lg overflow-hidden"><img src="/storage/${message.file_path}" alt="Imagem" class="max-w-full h-auto rounded-lg"></div>`;
            } else if (message.file_type && message.file_type.startsWith('video/')) {
                fileHtml = `<div class="mt-2 rounded-lg overflow-hidden"><video controls class="max-w-full h-auto rounded-lg"><source src="/storage/${message.file_path}" type="${message.file_type}"></video></div>`;
            } else {
                const bgClass = isClient ? 'bg-blue-400/30' : 'bg-gray-100';
                const linkClass = isClient ? 'text-white' : 'text-blue-600';
                fileHtml = `<div class="mt-2 p-2 ${bgClass} rounded-lg"><a href="/storage/${message.file_path}" target="_blank" class="${linkClass} hover:underline flex items-center text-sm"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>Arquivo anexado</a></div>`;
            }
        }
        
        const time = new Date(message.created_at).toLocaleTimeString('pt-BR', {hour: '2-digit', minute:'2-digit'});
        
        let adminBadge = '';
        if (isAdmin) {
            adminBadge = '<div class="w-6 h-6 rounded-full bg-purple-600 flex items-center justify-center text-xs text-white" title="Admin">A</div>';
        }

        const flexRow = isClient ? 'flex-row-reverse' : 'flex-row';

        messageDiv.innerHTML = `
            <div class="flex ${alignment}">
                <div class="max-w-xs sm:max-w-md lg:max-w-lg">
                    <div class="flex items-end gap-2 ${flexRow}">
                        ${adminBadge}
                        <div class="px-4 py-3 rounded-2xl shadow-sm ${bubbleClass}">
                            ${message.message ? `<p class="text-sm break-words">${escapeHtml(message.message)}</p>` : ''}
                            ${fileHtml}
                            <p class="text-xs mt-1.5 ${timeClass}">
                                ${isAdmin ? '<strong>Admin</strong> • ' : ''}
                                ${time}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        chatMessages.appendChild(messageDiv);
    }
    
    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Poll for new messages every 3 seconds
    function startPolling() {
        pollingInterval = setInterval(() => {
            fetch("{{ route('chat.get-messages', $chatRoom->room_code) }}")
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.messages) {
                        // Filter only new messages
                        const newMessages = data.messages.filter(msg => msg.id > lastMessageId);
                        
                        newMessages.forEach(message => {
                            addMessageToChat(message);
                            lastMessageId = message.id;
                        });
                        
                        if (newMessages.length > 0) {
                            scrollToBottom();
                        }
                    }
                })
                .catch(error => console.error('Polling error:', error));
        }, 3000); // Poll every 3 seconds
    }
    
    // Start polling
    startPolling();
    
    // Stop polling when page is hidden (performance optimization)
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            if (pollingInterval) clearInterval(pollingInterval);
        } else {
            startPolling();
        }
    });
    
    // Clean up on page unload
    window.addEventListener('beforeunload', function() {
        if (pollingInterval) clearInterval(pollingInterval);
    });
});
</script>
@endsection