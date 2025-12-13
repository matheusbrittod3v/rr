/**
 * Arquivo principal - Orquestra toda a aplicação
 */
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar configuração
    const config = new Config(
        "{{ $chatRoom->room_code }}",
        "{{ csrf_token() }}",
        {
            sendMessageUrl: "{{ route('chat.send-message', $chatRoom->room_code) }}",
            getMessagesUrl: "{{ route('chat.get-messages', $chatRoom->room_code) }}",
            signalsUrl: "{{ route('chat.signals', $chatRoom->room_code) }}",
            sendSignalUrl: "{{ route('chat.signal', $chatRoom->room_code) }}"
        }
    );
    
    // Inicializar managers
    const uiManager = new UIManager();
    const stateManager = new StateManager({{ $chatRoom->messages->max('id') ?? 0 }});
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
    
    // Iniciar polling de mensagens
    chatManager.startPolling();
    
    // Iniciar polling de sinais
    signalManager.startPolling();
    
    // Scroll inicial
    uiManager.scrollToBottom();
    
    console.log('Chat application initialized');
});
