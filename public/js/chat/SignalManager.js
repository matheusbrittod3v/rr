/**
 * Gerenciador de sinais WebRTC
 */
class SignalManager {
    constructor(stateManager, config, callManager, webRTCManager, uiManager) {
        this.state = stateManager;
        this.config = config;
        this.callManager = callManager;
        this.webRTCManager = webRTCManager;
        this.ui = uiManager;
        this.pollingInterval = null;
    }
    
    sendSignal(type, payload) {
        let cleanPayload = payload;
        if (payload && typeof payload === 'object' && 'type' in payload && 'sdp' in payload) {
            const cleanedSDP = Utils.cleanSDP(payload.sdp);
            if (!cleanedSDP) {
                console.error('Invalid SDP in payload:', payload);
                return;
            }
            
            cleanPayload = {
                type: payload.type,
                sdp: cleanedSDP
            };
        } else if (payload && typeof payload === 'object' && 'candidate' in payload) {
            cleanPayload = payload;
        }
        
        fetch(this.config.sendSignalUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.config.csrfToken
            },
            body: JSON.stringify({ type, payload: cleanPayload })
        }).catch(err => console.error('Error sending signal:', err));
    }
    
    // Polling removido - agora usa WebSocket via WebSocketManager
    startPolling() {
        // Não fazer nada - usar WebSocket ao invés de polling
        console.log('Polling de sinais desabilitado - usando WebSocket');
    }
    
    stopPolling() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            this.pollingInterval = null;
        }
    }
}

