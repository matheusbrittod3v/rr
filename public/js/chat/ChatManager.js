/**
 * Gerenciador de mensagens do chat
 */
class ChatManager {
    constructor(uiManager, config, stateManager) {
        this.uiManager = uiManager;
        this.config = config;
        this.state = stateManager;
        this.lastMessageId = stateManager ? stateManager.lastMessageId : 0;
        this.selectedFile = null;
        this.pollInterval = null;
        
        this.initializeEventListeners();
    }
    
    initializeEventListeners() {
        // Auto-resize textarea
        if (this.uiManager.elements.messageInput) {
            this.uiManager.elements.messageInput.addEventListener('input', () => {
                const input = this.uiManager.elements.messageInput;
                input.style.height = 'auto';
                input.style.height = (input.scrollHeight) + 'px';
                if (this.uiManager.elements.charCount) {
                    this.uiManager.elements.charCount.textContent = input.value.length + '/1000';
                }
            });
        }
        
        // Send message
        if (this.uiManager.elements.chatForm) {
            this.uiManager.elements.chatForm.addEventListener('submit', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.handleSendMessage(e);
                return false;
            });
        }
        
        // File handling
        if (this.uiManager.elements.attachButton) {
            this.uiManager.elements.attachButton.addEventListener('click', () => {
                if (this.uiManager.elements.fileInput) {
                    this.uiManager.elements.fileInput.click();
                }
            });
        }
        
        if (this.uiManager.elements.fileInput) {
            this.uiManager.elements.fileInput.addEventListener('change', (e) => {
                this.handleFileSelect(e);
            });
        }
        
        if (this.uiManager.elements.removeFileBtn) {
            this.uiManager.elements.removeFileBtn.addEventListener('click', () => {
                this.removeFile();
            });
        }
    }
    
    async handleSendMessage(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        const message = this.uiManager.elements.messageInput?.value.trim();
        if (!message && !this.selectedFile) return;
        
        const formData = new FormData();
        formData.append('message', message || '');
        formData.append('_token', this.config.csrfToken);
        if (this.selectedFile) {
            formData.append('file', this.selectedFile);
        }
        
        try {
            const url = this.config.sendMessageUrl || this.config.routes?.sendMessageUrl;
            if (!url) {
                console.error('sendMessageUrl não encontrada na configuração');
                return;
            }
            const res = await fetch(url, {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                this.clearInput();
            }
        } catch (error) {
            console.error('Error sending message:', error);
        }
    }
    
    clearInput() {
        if (this.uiManager.elements.messageInput) {
            this.uiManager.elements.messageInput.value = '';
            this.uiManager.elements.messageInput.style.height = 'auto';
        }
        if (this.uiManager.elements.charCount) {
            this.uiManager.elements.charCount.textContent = '0/1000';
        }
        this.selectedFile = null;
        if (this.uiManager.elements.filePreview) {
            this.uiManager.elements.filePreview.classList.add('hidden');
        }
        if (this.uiManager.elements.fileInput) {
            this.uiManager.elements.fileInput.value = '';
        }
    }
    
    handleFileSelect(e) {
        const file = e.target.files[0];
        if (file) {
            this.selectedFile = file;
            if (this.uiManager.elements.fileName) {
                this.uiManager.elements.fileName.textContent = file.name;
            }
            if (this.uiManager.elements.filePreview) {
                this.uiManager.elements.filePreview.classList.remove('hidden');
            }
        }
    }
    
    removeFile() {
        this.selectedFile = null;
        if (this.uiManager.elements.filePreview) {
            this.uiManager.elements.filePreview.classList.add('hidden');
        }
        if (this.uiManager.elements.fileInput) {
            this.uiManager.elements.fileInput.value = '';
        }
    }
    
    addMessageToChat(message) {
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
        
        if (this.uiManager.elements.chatMessages) {
            this.uiManager.elements.chatMessages.appendChild(messageDiv);
            this.uiManager.scrollToBottom();
        }
    }
    
    // Polling removido - agora usa WebSocket via WebSocketManager
    // Mantido apenas para fallback caso WebSocket não esteja disponível
    startPolling() {
        // Não fazer nada - usar WebSocket ao invés de polling
        console.log('Polling de mensagens desabilitado - usando WebSocket');
    }
    
    stopPolling() {
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
            this.pollInterval = null;
        }
    }
    
    // Método mantido apenas para carregar mensagens iniciais
    async fetchInitialMessages() {
        try {
            const url = this.config.getMessagesUrl || this.config.routes?.getMessagesUrl;
            if (!url) {
                console.error('getMessagesUrl não encontrada na configuração');
                return;
            }
            const res = await fetch(url);
            const data = await res.json();
            if (data.success && data.messages && data.messages.length > 0) {
                data.messages.forEach(m => {
                    // Verificar se já existe no DOM
                    const existingMessage = document.querySelector(`[data-message-id="${m.id}"]`);
                    if (!existingMessage) {
                        this.addMessageToChat(m);
                    }
                    this.lastMessageId = Math.max(this.lastMessageId, m.id);
                    if (this.state) {
                        this.state.lastMessageId = Math.max(this.state.lastMessageId, m.id);
                    }
                });
            }
        } catch (e) {
            console.error("Error fetching initial messages", e);
        }
    }
    
    setLastMessageId(id) {
        this.lastMessageId = id;
    }
}

