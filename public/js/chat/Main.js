/**
 * Arquivo principal - Orquestra toda a aplicação
 * 
 * IMPORTANTE: As configurações são passadas via window.CHAT_CONFIG
 * que deve ser definido no Blade antes de carregar este arquivo.
 */
document.addEventListener('DOMContentLoaded', function() {
    // Verificar se a configuração foi passada
    if (!window.CHAT_CONFIG) {
        console.error('CHAT_CONFIG não encontrado. Certifique-se de que foi definido antes de carregar Main.js');
        return;
    }
    
    // Inicializar configuração
    const config = new Config(
        window.CHAT_CONFIG.roomCode,
        window.CHAT_CONFIG.csrfToken,
        {
            sendMessageUrl: window.CHAT_CONFIG.routes.sendMessageUrl,
            getMessagesUrl: window.CHAT_CONFIG.routes.getMessagesUrl,
            signalsUrl: window.CHAT_CONFIG.routes.signalsUrl,
            sendSignalUrl: window.CHAT_CONFIG.routes.sendSignalUrl
        }
    );
    
    // Inicializar managers
    const uiManager = new UIManager();
    const stateManager = new StateManager(window.CHAT_CONFIG.lastMessageId || 0);
    const chatManager = new ChatManager(uiManager, config, stateManager);
    const mediaManager = new MediaManager(stateManager, config, uiManager);
    const webRTCManager = new WebRTCManager(stateManager, config, uiManager, null, null);
    const callManager = new CallManager(stateManager, config, uiManager, mediaManager, webRTCManager, null);
    const signalManager = new SignalManager(stateManager, config, callManager, webRTCManager, uiManager);
    
    // Conectar referências circulares
    webRTCManager.callManager = callManager;
    webRTCManager.signalManager = signalManager;
    callManager.signalManager = signalManager;
    
    // Inicializar event listeners de chamadas
    if (uiManager.elements.voiceCallBtn) {
        uiManager.elements.voiceCallBtn.addEventListener('click', () => callManager.startCall(false));
    }
    
    if (uiManager.elements.videoCallBtn) {
        uiManager.elements.videoCallBtn.addEventListener('click', () => callManager.startCall(true));
    }
    
    if (uiManager.elements.acceptCallBtn) {
        uiManager.elements.acceptCallBtn.addEventListener('click', () => {
            uiManager.hideIncomingCallModal();
            callManager.acceptIncomingCall();
        });
    }
    
    if (uiManager.elements.rejectCallBtn) {
        uiManager.elements.rejectCallBtn.addEventListener('click', () => {
            signalManager.sendSignal('reject', {});
            if (callManager.pendingOfferSignalId) {
                stateManager.processedSignalIds.add(callManager.pendingOfferSignalId);
            }
            callManager.pendingOffer = null;
            callManager.pendingOfferSignalId = null;
            stateManager.isAcceptingCall = false;
            uiManager.hideIncomingCallModal();
        });
    }
    
    if (uiManager.elements.hangupBtn) {
        uiManager.elements.hangupBtn.addEventListener('click', () => callManager.endCall(true));
    }
    
    if (uiManager.elements.toggleMicBtn) {
        uiManager.elements.toggleMicBtn.addEventListener('click', () => callManager.toggleMic());
    }
    
    if (uiManager.elements.toggleVideoBtn) {
        uiManager.elements.toggleVideoBtn.addEventListener('click', () => callManager.toggleVideo());
    }
    
    if (uiManager.elements.switchCameraBtn) {
        uiManager.elements.switchCameraBtn.addEventListener('click', () => callManager.switchCamera());
    }
    
    if (uiManager.elements.switchToVoiceBtn) {
        uiManager.elements.switchToVoiceBtn.addEventListener('click', () => callManager.switchToVoice());
    }
    
    if (uiManager.elements.minimizeCallBtn) {
        uiManager.elements.minimizeCallBtn.addEventListener('click', () => callManager.minimizeCall());
    }
    
    if (uiManager.elements.minimizedCallWidget) {
        uiManager.elements.minimizedCallWidget.addEventListener('click', () => callManager.maximizeCall());
    }
    
    // Carregar mensagens iniciais (apenas uma vez)
    chatManager.fetchInitialMessages().then(() => {
        uiManager.scrollToBottom();
    });
    
    // Aguardar Echo estar disponível (carregado via @vite)
    function initializeWebSocket() {
        if (!window.Echo) {
            console.warn('Echo ainda não disponível, tentando novamente...');
            setTimeout(initializeWebSocket, 500);
            return;
        }
        
        // Inicializar WebSocket Manager (substitui polling)
        const webSocketManager = new WebSocketManager(
            stateManager, 
            config, 
            callManager, 
            webRTCManager, 
            chatManager, 
            uiManager
        );
        
        // Tentar inicializar WebSocket
        const wsInitialized = webSocketManager.initialize();
        
        if (!wsInitialized) {
            console.warn('WebSocket não disponível, usando polling como fallback');
            // Fallback para polling se WebSocket não estiver disponível
            chatManager.startPolling();
            signalManager.startPolling();
        } else {
            console.log('✅ WebSocket conectado - polling desabilitado');
        }
    }
    
    // Inicializar após um pequeno delay para garantir que Echo esteja carregado
    setTimeout(initializeWebSocket, 100);
    
    // Scroll inicial
    uiManager.scrollToBottom();
    
    console.log('Chat application initialized');
});
