/**
 * Gerenciador de WebSocket usando Reverb/Echo
 * Substitui o polling por conexão WebSocket em tempo real
 */
class WebSocketManager {
    constructor(stateManager, config, callManager, webRTCManager, chatManager, uiManager) {
        this.state = stateManager;
        this.config = config;
        this.callManager = callManager;
        this.webRTCManager = webRTCManager;
        this.chatManager = chatManager;
        this.ui = uiManager;
        this.echo = null;
        this.channel = null;
    }
    
    initialize() {
        if (!window.Echo) {
            console.error('Echo não está disponível. Certifique-se de que Reverb está configurado.');
            return false;
        }
        
        this.echo = window.Echo;
        const channelName = `chat-room.${this.config.roomCode}`;
        
        console.log('Conectando ao canal:', channelName);
        
        // Conectar ao canal do chat room
        this.channel = this.echo.channel(channelName);
        
        // Escutar mensagens de chat
        // O Laravel adiciona o prefixo do namespace, então usamos o nome do evento diretamente
        this.channel.listen('chat.message.sent', (event) => {
            console.log('Nova mensagem recebida via WebSocket:', event);
            if (this.chatManager && event.id) {
                // Formatar como mensagem completa
                const message = {
                    id: event.id,
                    message: event.message || event.content,
                    sender_type: event.sender_type,
                    file_path: event.file_path,
                    file_type: event.file_type,
                    created_at: event.created_at || event.timestamp
                };
                
                // Verificar se a mensagem já existe (evitar duplicatas)
                const existingMessage = document.querySelector(`[data-message-id="${message.id}"]`);
                if (!existingMessage) {
                    this.chatManager.addMessageToChat(message);
                    if (this.state) {
                        this.state.lastMessageId = Math.max(this.state.lastMessageId, message.id);
                    }
                }
            }
        });
        
        // Escutar sinais WebRTC
        this.channel.listen('call.signal', (event) => {
            console.log('Sinal WebRTC recebido via WebSocket:', event);
            
            if (!event.data || !event.data.type) {
                console.error('Sinal inválido:', event);
                return;
            }
            
            const { type, payload, sender_type } = event.data;
            
            // Evitar processar sinais próprios
            const currentUserType = this.getCurrentUserType();
            if (sender_type === currentUserType) {
                return;
            }
            
            try {
                let signalPayload = payload;
                
                // Parse se necessário
                if (typeof payload === 'string') {
                    signalPayload = JSON.parse(payload);
                }
                
                // Processar o sinal
                switch(type) {
                    case 'offer':
                        if (this.callManager) {
                            this.callManager.handleIncomingOffer(signalPayload, null);
                        }
                        break;
                    case 'answer':
                        if (this.webRTCManager) {
                            this.webRTCManager.handleIncomingAnswer(signalPayload);
                        }
                        break;
                    case 'candidate':
                        if (this.webRTCManager && this.state.peerConnection) {
                            this.webRTCManager.handleIncomingCandidate(signalPayload);
                        }
                        break;
                    case 'hangup':
                    case 'reject':
                        if (this.callManager) {
                            this.callManager.endCall(false);
                            if (type === 'reject') {
                                alert('Chamada recusada.');
                            }
                        }
                        break;
                }
            } catch (error) {
                console.error('Erro ao processar sinal WebRTC:', error);
            }
        });
        
        console.log('WebSocket conectado e escutando eventos');
        return true;
    }
    
    getCurrentUserType() {
        // Determinar tipo de usuário atual
        // Isso deve corresponder à lógica no backend
        if (document.body.dataset.userRole === 'admin') {
            return 'admin';
        }
        return 'client';
    }
    
    disconnect() {
        if (this.channel) {
            this.echo.leave(`chat-room.${this.config.roomCode}`);
            this.channel = null;
        }
    }
}

