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
    
    startPolling() {
        const signalPollInterval = this.config.isMobile ? 1500 : 2000;
        
        this.pollingInterval = setInterval(() => {
            fetch(`${this.config.signalsUrl}?last_signal_id=${this.state.lastSignalId}`, {
                method: 'GET',
                cache: 'no-cache',
                headers: {
                    'Cache-Control': 'no-cache',
                    'Pragma': 'no-cache'
                }
            })
                .then(res => {
                    if (!res.ok) {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.success && data.signals && data.signals.length > 0) {
                        data.signals.forEach(signal => {
                            if (this.state.processedSignalIds.has(signal.id)) {
                                this.state.lastSignalId = Math.max(this.state.lastSignalId, signal.id);
                                return;
                            }
                            
                            this.state.lastSignalId = Math.max(this.state.lastSignalId, signal.id);
                            this.state.processedSignalIds.add(signal.id);
                            
                            if (this.state.processedSignalIds.size > 100) {
                                const idsArray = Array.from(this.state.processedSignalIds);
                                const toRemove = idsArray.slice(0, idsArray.length - 100);
                                toRemove.forEach(id => this.state.processedSignalIds.delete(id));
                            }
                            
                            try {
                                let payload = JSON.parse(signal.payload);
                                
                                if ((signal.type === 'offer' || signal.type === 'answer') && payload) {
                                    if (typeof payload === 'object' && payload.type && payload.sdp) {
                                        const cleanedSDP = Utils.cleanSDP(payload.sdp);
                                        if (!cleanedSDP) {
                                            console.error('Invalid SDP: could not clean sdp field', signal.id);
                                            return;
                                        }
                                        
                                        payload = {
                                            type: payload.type,
                                            sdp: cleanedSDP
                                        };
                                        
                                        if (!payload.sdp || payload.sdp.length === 0) {
                                            console.error('Invalid SDP: empty sdp field', signal.id);
                                            return;
                                        }
                                        
                                        if (!['offer', 'answer'].includes(payload.type)) {
                                            console.error('Invalid SDP type:', payload.type, signal.id);
                                            return;
                                        }
                                    } else {
                                        console.error('Invalid SDP payload structure:', payload, signal.id);
                                        return;
                                    }
                                }
                                
                                if (this.state.inCall && signal.type === 'offer') {
                                    console.warn('Ignored conflicting offer during active call:', signal.id);
                                    return;
                                }
                                
                                if (this.state.inCall && signal.type === 'answer' && !this.state.peerConnection) {
                                    console.warn('Ignored answer: no peer connection yet');
                                    return;
                                }
                                
                                if (this.state.isAcceptingCall && signal.type === 'offer') {
                                    console.warn('Ignored offer: already accepting call');
                                    return;
                                }
                                
                                if (this.ui && !this.ui.elements.incomingCallModal?.classList.contains('hidden') && signal.type === 'offer') {
                                    console.warn('Ignored offer: modal already open');
                                    return;
                                }
                                
                                if (signal.type === 'answer' && this.state.peerConnection && this.state.peerConnection.remoteDescription) {
                                    const currentRemoteSDP = this.state.peerConnection.remoteDescription.sdp;
                                    const incomingSDP = payload?.sdp;
                                    
                                    if (incomingSDP && currentRemoteSDP && 
                                        (currentRemoteSDP.substring(0, 100) === incomingSDP.substring(0, 100) ||
                                         this.state.peerConnection.signalingState === 'stable')) {
                                        console.log('Ignoring duplicate answer signal:', signal.id);
                                        return;
                                    }
                                }

                                switch(signal.type) {
                                    case 'offer':
                                        this.callManager.handleIncomingOffer(payload, signal.id);
                                        break;
                                    case 'answer':
                                        this.webRTCManager.handleIncomingAnswer(payload);
                                        break;
                                    case 'candidate':
                                        if (this.state.peerConnection) {
                                            this.webRTCManager.handleIncomingCandidate(payload);
                                        }
                                        break;
                                    case 'hangup':
                                    case 'reject':
                                        this.callManager.endCall(false);
                                        if (signal.type === 'reject') {
                                            alert('Chamada recusada.');
                                        }
                                        break;
                                }
                            } catch (parseError) {
                                console.error('Error parsing signal payload:', parseError, signal);
                            }
                        });
                    }
                })
                .catch(e => {
                    if (!e.message || !e.message.includes('Failed to fetch')) {
                        console.error("Signal poll error", e);
                    }
                });
        }, signalPollInterval);
    }
    
    stopPolling() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            this.pollingInterval = null;
        }
    }
}

