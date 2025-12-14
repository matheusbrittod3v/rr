/**
 * Gerenciador de chamadas
 */
class CallManager {
    constructor(stateManager, config, uiManager, mediaManager, webRTCManager, signalManager) {
        this.state = stateManager;
        this.config = config;
        this.ui = uiManager;
        this.mediaManager = mediaManager;
        this.webRTCManager = webRTCManager;
        this.signalManager = signalManager;
        this.pendingOffer = null;
        this.pendingOfferSignalId = null;
    }
    
    async startCall(isVideo = true) {
        try {
            if (this.state.inCall || this.state.isAcceptingCall) {
                console.log('Cannot start call: already in call');
                return;
            }
            
            this.state.pendingCandidates = [];
            this.state.isVideoCall = isVideo;
            await this.mediaManager.getLocalStream(isVideo);
            
            if (this.state.localStream) {
                const hasVideo = this.state.localStream.getVideoTracks().length > 0;
                const hasAudio = this.state.localStream.getAudioTracks().length > 0;
                
                if (isVideo && !hasVideo && hasAudio) {
                    console.log('Video requested but not available - adjusting to voice call');
                    this.state.isVideoCall = false;
                }
                
                console.log('Call type adjusted:', {
                    requested: isVideo ? 'video' : 'voice',
                    actual: this.state.isVideoCall ? 'video' : 'voice',
                    hasVideo: hasVideo,
                    hasAudio: hasAudio
                });
            }
            
            await this.webRTCManager.createPeerConnection();
            
            const transceivers = this.state.peerConnection.getTransceivers();
            console.log('Transceivers before offer:', transceivers.map(t => ({
                kind: t.receiver.track?.kind || t.sender.track?.kind || 'unknown',
                direction: t.direction,
                currentDirection: t.currentDirection
            })));
            
            const offerOptions = this.config.isMobile ? {
                offerToReceiveAudio: true,
                offerToReceiveVideo: this.state.isVideoCall
            } : {};
            
            const offer = await this.state.peerConnection.createOffer(offerOptions);
            console.log('Offer created:', {
                type: offer.type,
                hasSDP: !!offer.sdp,
                sdpLength: offer.sdp?.length,
                isMobile: this.config.isMobile
            });
            
            await this.state.peerConnection.setLocalDescription(offer);
            console.log('Local description set (offer)');
            
            const transceiversAfter = this.state.peerConnection.getTransceivers();
            console.log('Transceivers after offer:', transceiversAfter.map(t => ({
                kind: t.receiver.track?.kind || t.sender.track?.kind || 'unknown',
                direction: t.direction,
                currentDirection: t.currentDirection
            })));
            
            this.signalManager.sendSignal('offer', offer);
            
            this.state.answerReceived = false;
            
            this.state.connectionTimeout = setTimeout(() => {
                if (this.state.peerConnection) {
                    const connectionState = this.state.peerConnection.connectionState;
                    const iceState = this.state.peerConnection.iceConnectionState;
                    console.warn('Connection timeout check:', {
                        connectionState,
                        iceState,
                        answerReceived: this.state.answerReceived
                    });
                    
                    if (!this.state.answerReceived) {
                        console.warn('Answer not received after 30 seconds');
                        this.ui.updateCallStatus('Aguardando resposta...');
                    } else if (connectionState !== 'connected' && connectionState !== 'connecting') {
                        console.error('Connection appears to be stuck, state:', connectionState);
                        this.ui.updateCallStatus('Tentando reconectar...');
                    }
                }
            }, 30000);
            
            this.showCallOverlay();
            this.ui.updateCallStatus('Chamando...');
        } catch (error) {
            console.error('Error starting call:', error);
            this.endCall(false);
            alert('Erro ao iniciar chamada. Verifique as permissões de câmera/microfone.');
        }
    }
    
    async handleIncomingOffer(offer, signalId) {
        try {
            if (this.state.inCall || this.state.isAcceptingCall) {
                console.log('Ignoring offer: already in call or accepting');
                return;
            }
            
            // Check if modal is already open
            if (this.ui.elements.incomingCallModal && !this.ui.elements.incomingCallModal.classList.contains('hidden')) {
                console.log('Ignoring offer: modal already open');
                return;
            }
            
            if (this.pendingOffer) {
                console.log('Ignoring offer: already have pending offer');
                return;
            }
            
            if (offer && offer.sdp) {
                const hasNewlines = offer.sdp.includes('\n');
                const lineCount = offer.sdp.split('\n').length;
                console.log('Received offer SDP:', {
                    hasNewlines,
                    lineCount,
                    length: offer.sdp.length,
                    firstChars: offer.sdp.substring(0, 100)
                });
            }
            
            const isVideo = offer.sdp ? Utils.isVideoCallSDP(offer.sdp) : false;
            this.state.isVideoCall = isVideo;
            
            console.log('Incoming call type detected:', {
                isVideo: isVideo,
                sdpLength: offer.sdp?.length
            });
            
            this.pendingOffer = offer;
            this.pendingOfferSignalId = signalId;
            
            this.ui.showIncomingCallModal();
        } catch (error) {
            console.error('Error handling offer:', error);
        }
    }
    
    async acceptIncomingCall() {
        try {
            if (this.state.isAcceptingCall || !this.pendingOffer) {
                console.log('Cannot accept: already accepting or no pending offer');
                return;
            }
            
            if (!this.pendingOffer.type || !this.pendingOffer.sdp) {
                console.error('Invalid pending offer:', this.pendingOffer);
                this.pendingOffer = null;
                this.pendingOfferSignalId = null;
                this.ui.hideIncomingCallModal();
                alert('Erro: oferta de chamada inválida.');
                return;
            }
            
            this.state.isAcceptingCall = true;
            this.state.pendingCandidates = [];
            
            const isVideo = this.pendingOffer.sdp ? Utils.isVideoCallSDP(this.pendingOffer.sdp) : false;
            this.state.isVideoCall = isVideo;
            
            console.log('Detected call type:', {
                isVideo: isVideo,
                sdpLength: this.pendingOffer.sdp?.length,
                hasVideoMediaLine: this.pendingOffer.sdp?.includes('m=video')
            });
            
            const modalContent = this.ui.elements.incomingCallModal?.querySelector('p');
            const originalText = modalContent?.textContent || '';
            if (modalContent) {
                if (isVideo) {
                    modalContent.textContent = 'Solicitando permissões de câmera e microfone...';
                } else {
                    modalContent.textContent = 'Solicitando permissão de microfone...';
                }
            }
            
            if (this.pendingOfferSignalId) {
                this.state.processedSignalIds.add(this.pendingOfferSignalId);
            }
            
            let cleanedSDP = Utils.cleanSDP(this.pendingOffer.sdp);
            if (!cleanedSDP) {
                console.warn('SDP cleaning failed, using original');
                cleanedSDP = String(this.pendingOffer.sdp).replace(/\r\n/g, '\n').replace(/\r/g, '\n');
            }
            
            const sdpWithoutProblematicLines = cleanedSDP.split('\n')
                .filter(line => {
                    if (line.includes('ssrc:') && line.includes('msid:')) {
                        const match = line.match(/a=ssrc:\d+\s+msid:[a-f0-9\-]+\s+[a-f0-9\-]+/i);
                        return match !== null;
                    }
                    return true;
                })
                .join('\n');
            
            if (!cleanedSDP.includes('v=') || !cleanedSDP.includes('m=')) {
                console.error('SDP missing required fields:', {
                    hasVersion: cleanedSDP.includes('v='),
                    hasMedia: cleanedSDP.includes('m='),
                    originalLength: this.pendingOffer.sdp ? this.pendingOffer.sdp.length : 0,
                    cleanedLength: cleanedSDP.length
                });
                throw new Error('SDP format invalid: missing required fields');
            }
            
            const cleanOffer = {
                type: this.pendingOffer.type,
                sdp: cleanedSDP
            };
            
            try {
                await this.mediaManager.getLocalStream(isVideo);
                this.ui.hideIncomingCallModal();
            } catch (streamError) {
                console.error('Failed to get local stream when accepting call:', streamError);
                
                const modalContent = this.ui.elements.incomingCallModal?.querySelector('p');
                if (modalContent) {
                    modalContent.textContent = 'Alguém deseja iniciar uma chamada de vídeo/voz com você.';
                }
                
                this.state.isAcceptingCall = false;
                this.pendingOffer = null;
                this.pendingOfferSignalId = null;
                return;
            }
            
            try {
                await this.webRTCManager.createPeerConnection();
            } catch (pcError) {
                console.error('Failed to create peer connection:', pcError);
                if (this.state.localStream) {
                    this.state.localStream.getTracks().forEach(track => track.stop());
                    this.state.localStream = null;
                }
                this.state.isAcceptingCall = false;
                this.pendingOffer = null;
                this.pendingOfferSignalId = null;
                this.ui.hideIncomingCallModal();
                alert('Erro ao criar conexão. Tente novamente.');
                return;
            }
            
            try {
                const sessionDescription = new RTCSessionDescription(cleanOffer);
                
                const sdpLines = cleanedSDP.split('\n');
                const problematicLines = sdpLines.filter(line => 
                    line.includes('ssrc:') && line.includes('msid:')
                );
                if (problematicLines.length > 0) {
                    console.log('Found ssrc/msid lines:', problematicLines);
                }
                
                await this.state.peerConnection.setRemoteDescription(sessionDescription);
            } catch (sdpError) {
                console.error('SDP Error details:', {
                    error: sdpError.message,
                    sdpType: cleanOffer.type,
                    sdpLength: cleanedSDP.length,
                    problematicLines: cleanedSDP.split('\n').filter(line => 
                        line.includes('ssrc:') && line.includes('msid:')
                    ).slice(0, 3)
                });
                
                if (sdpWithoutProblematicLines !== cleanedSDP && sdpWithoutProblematicLines.length > 0) {
                    console.log('Attempting with SDP without problematic ssrc/msid lines...');
                    try {
                        const cleanOfferNoProblematic = {
                            type: this.pendingOffer.type,
                            sdp: sdpWithoutProblematicLines
                        };
                        await this.state.peerConnection.setRemoteDescription(new RTCSessionDescription(cleanOfferNoProblematic));
                        console.log('Success with SDP without problematic lines');
                        cleanedSDP = sdpWithoutProblematicLines;
                    } catch (noProblematicError) {
                        console.warn('SDP without problematic lines also failed, trying original...');
                        
                        if (this.pendingOffer.sdp !== cleanedSDP) {
                            try {
                                const originalOffer = {
                                    type: this.pendingOffer.type,
                                    sdp: String(this.pendingOffer.sdp).replace(/\r\n/g, '\n').replace(/\r/g, '\n')
                                };
                                await this.state.peerConnection.setRemoteDescription(new RTCSessionDescription(originalOffer));
                                console.log('Success with original SDP');
                            } catch (originalError) {
                                console.error('All SDP attempts failed:', {
                                    cleaned: sdpError.message,
                                    noProblematic: noProblematicError.message,
                                    original: originalError.message
                                });
                                throw sdpError;
                            }
                        } else {
                            throw sdpError;
                        }
                    }
                } else if (this.pendingOffer.sdp !== cleanedSDP) {
                    console.log('Attempting with original SDP...');
                    try {
                        const originalOffer = {
                            type: this.pendingOffer.type,
                            sdp: String(this.pendingOffer.sdp).replace(/\r\n/g, '\n').replace(/\r/g, '\n')
                        };
                        await this.state.peerConnection.setRemoteDescription(new RTCSessionDescription(originalOffer));
                        console.log('Success with original SDP');
                    } catch (originalError) {
                        console.error('Original SDP also failed:', originalError);
                        throw sdpError;
                    }
                } else {
                    throw sdpError;
                }
            }
            
            await this.webRTCManager.processPendingCandidates();
            
            console.log('Creating answer...');
            
            const transceivers = this.state.peerConnection.getTransceivers();
            console.log('Transceivers before answer:', transceivers.map(t => ({
                kind: t.receiver.track?.kind || 'unknown',
                direction: t.direction,
                currentDirection: t.currentDirection
            })));
            
            const answerOptions = this.config.isMobile ? {
                offerToReceiveAudio: true,
                offerToReceiveVideo: this.state.isVideoCall
            } : {};
            
            const answer = await this.state.peerConnection.createAnswer(answerOptions);
            console.log('Answer created, setting local description...', {
                answerType: answer.type,
                hasSDP: !!answer.sdp,
                sdpLength: answer.sdp?.length,
                isMobile: this.config.isMobile
            });
            
            await this.state.peerConnection.setLocalDescription(answer);
            console.log('Local description set, sending answer signal...');
            
            const transceiversAfter = this.state.peerConnection.getTransceivers();
            console.log('Transceivers after answer:', transceiversAfter.map(t => ({
                kind: t.receiver.track?.kind || t.sender.track?.kind || 'unknown',
                direction: t.direction,
                currentDirection: t.currentDirection,
                hasReceiverTrack: !!t.receiver.track,
                hasSenderTrack: !!t.sender.track
            })));
            
            transceiversAfter.forEach(transceiver => {
                if (!this.state.isVideoCall && transceiver.sender.track?.kind === 'audio') {
                    if (!transceiver.receiver.track && transceiver.direction === 'sendonly') {
                        console.warn('Audio transceiver is sendonly, may need adjustment');
                    }
                }
                if (transceiver.direction === 'sendonly') {
                    console.warn(`Transceiver ${transceiver.sender.track?.kind || 'unknown'} is sendonly, may not receive media`);
                }
            });
            
            this.signalManager.sendSignal('answer', answer);
            console.log('Answer signal sent');
            
            this.state.answerReceived = false;
            
            this.state.connectionTimeout = setTimeout(() => {
                if (this.state.peerConnection) {
                    const connectionState = this.state.peerConnection.connectionState;
                    const iceState = this.state.peerConnection.iceConnectionState;
                    console.warn('Connection timeout check:', {
                        connectionState,
                        iceState,
                        answerReceived: this.state.answerReceived
                    });
                    
                    if (connectionState !== 'connected' && connectionState !== 'connecting') {
                        console.error('Connection appears to be stuck, state:', connectionState);
                        this.ui.updateCallStatus('Tentando reconectar...');
                    } else if (iceState === 'checking' || iceState === 'disconnected') {
                        console.warn('ICE connection seems stuck, state:', iceState);
                    }
                }
            }, 30000);
            
            this.showCallOverlay();
            this.ui.updateCallStatus('Conectando...');
            
            this.pendingOffer = null;
            this.pendingOfferSignalId = null;
            this.state.isAcceptingCall = false;
        } catch (error) {
            console.error('Error accepting call:', error);
            
            this.state.isAcceptingCall = false;
            this.pendingOffer = null;
            this.pendingOfferSignalId = null;
            this.state.pendingCandidates = [];
            
            if (this.state.localStream) {
                this.state.localStream.getTracks().forEach(track => track.stop());
                this.state.localStream = null;
            }
            
            if (this.state.peerConnection) {
                this.state.peerConnection.close();
                this.state.peerConnection = null;
            }
            
            this.ui.hideIncomingCallModal();
            this.ui.hideCallOverlay();
            
            if (!error.message || (!error.message.includes('permission') && !error.name?.includes('NotAllowed'))) {
                let errorMessage = 'Erro ao aceitar chamada.';
                if (error.message && error.message.includes('SDP')) {
                    errorMessage += ' Problema com a descrição da sessão. Tente iniciar uma nova chamada.';
                } else if (error.message) {
                    errorMessage += ' ' + error.message;
                }
                alert(errorMessage);
            }
        }
    }
    
    endCall(sendHangup = true) {
        this.stopCallTimer();
        
        if (this.state.trackMonitorInterval) {
            clearInterval(this.state.trackMonitorInterval);
            this.state.trackMonitorInterval = null;
        }
        
        if (this.state.connectionTimeout) {
            clearTimeout(this.state.connectionTimeout);
            this.state.connectionTimeout = null;
        }
        
        if (sendHangup && this.state.inCall) {
            this.signalManager.sendSignal('hangup', {});
        }
        
        if (this.state.localStream) {
            this.state.localStream.getTracks().forEach(track => track.stop());
            this.state.localStream = null;
        }
        
        if (this.state.peerConnection) {
            this.state.peerConnection.close();
            this.state.peerConnection = null;
        }
        
        if (this.state.remoteStream) {
            this.state.remoteStream.getTracks().forEach(track => track.stop());
            this.state.remoteStream = null;
        }
        
        this.pendingOffer = null;
        this.pendingOfferSignalId = null;
        this.state.pendingCandidates = [];
        this.state.isAcceptingCall = false;
        this.state.inCall = false;
        this.state.isCallMinimized = false;
        this.state.isVideoCall = false;
        this.state.isVideoEnabled = true;
        this.state.isMuted = false;
        this.state.currentCamera = 'user';
        
        this.ui.hideCallOverlay();
        this.ui.hideIncomingCallModal();
        this.ui.hideMinimizedCallWidget();
        
        if (this.ui.elements.remoteVideo) {
            this.ui.elements.remoteVideo.srcObject = null;
        }
        if (this.ui.elements.localVideo) {
            this.ui.elements.localVideo.srcObject = null;
        }
        if (this.ui.elements.minimizedRemoteVideo) {
            this.ui.elements.minimizedRemoteVideo.srcObject = null;
        }
        
        if (this.ui.elements.micOnIcon) {
            this.ui.elements.micOnIcon.classList.remove('hidden');
        }
        if (this.ui.elements.micOffIcon) {
            this.ui.elements.micOffIcon.classList.add('hidden');
        }
        if (this.ui.elements.videoOnIcon) {
            this.ui.elements.videoOnIcon.classList.remove('hidden');
        }
        if (this.ui.elements.videoOffIcon) {
            this.ui.elements.videoOffIcon.classList.add('hidden');
        }
        if (this.ui.elements.localVideo) {
            this.ui.elements.localVideo.style.display = 'block';
        }
    }
    
    showCallOverlay() {
        this.state.inCall = true;
        this.ui.showCallOverlay();
        
        if (this.state.isVideoCall) {
            this.ui.showSwitchToVoiceBtn();
            this.ui.showSwitchCameraBtn();
        } else {
            this.ui.hideSwitchToVoiceBtn();
            this.ui.hideSwitchCameraBtn();
        }
    }
    
    startCallTimer() {
        if (this.state.callTimerInterval) {
            return;
        }
        
        this.state.callStartTime = Date.now();
        this.ui.showCallTimer();
        
        this.state.callTimerInterval = setInterval(() => {
            if (!this.state.callStartTime) return;
            
            const elapsed = Math.floor((Date.now() - this.state.callStartTime) / 1000);
            const minutes = Math.floor(elapsed / 60);
            const seconds = elapsed % 60;
            const timeString = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            
            this.ui.updateTimerDisplay(timeString);
        }, 1000);
    }
    
    stopCallTimer() {
        if (this.state.callTimerInterval) {
            clearInterval(this.state.callTimerInterval);
            this.state.callTimerInterval = null;
        }
        this.state.callStartTime = null;
        this.ui.hideCallTimer();
        this.ui.updateTimerDisplay('00:00');
    }
    
    toggleMic() {
        if (!this.state.localStream) return;
        
        this.state.isMuted = !this.state.isMuted;
        this.state.localStream.getAudioTracks().forEach(track => {
            track.enabled = !this.state.isMuted;
        });
        
        if (this.state.isMuted) {
            this.ui.showMicOff();
        } else {
            this.ui.showMicOn();
        }
    }
    
    toggleVideo() {
        if (!this.state.localStream) return;
        
        this.state.isVideoEnabled = !this.state.isVideoEnabled;
        this.state.localStream.getVideoTracks().forEach(track => {
            track.enabled = this.state.isVideoEnabled;
        });
        
        if (this.state.isVideoEnabled) {
            this.ui.showVideoOn();
        } else {
            this.ui.showVideoOff();
        }
    }
    
    async switchCamera() {
        if (!this.state.localStream || !this.state.isVideoCall || !this.state.isVideoEnabled) return;
        
        try {
            this.state.currentCamera = this.state.currentCamera === 'user' ? 'environment' : 'user';
            
            const videoTrack = this.state.localStream.getVideoTracks()[0];
            if (videoTrack) {
                let videoConstraints = this.config.isMobile ? {
                    facingMode: this.state.currentCamera,
                    width: { ideal: 640, max: 1280 },
                    height: { ideal: 480, max: 720 },
                    frameRate: { ideal: 30, max: 30 }
                } : {
                    facingMode: this.state.currentCamera
                };
                
                let newStream;
                try {
                    newStream = await navigator.mediaDevices.getUserMedia({
                        video: videoConstraints,
                        audio: false
                    });
                } catch (error) {
                    if (error.name === 'OverconstrainedError' || error.name === 'NotReadableError') {
                        console.warn('Camera switch with constraints failed, trying simple...');
                        newStream = await navigator.mediaDevices.getUserMedia({
                            video: { facingMode: this.state.currentCamera },
                            audio: false
                        });
                    } else {
                        throw error;
                    }
                }
                
                const newVideoTrack = newStream.getVideoTracks()[0];
                const sender = this.state.peerConnection.getSenders().find(s => 
                    s.track && s.track.kind === 'video'
                );
                
                if (sender) {
                    await sender.replaceTrack(newVideoTrack);
                    console.log('Camera track replaced successfully');
                } else {
                    console.warn('No video sender found');
                }
                
                videoTrack.stop();
                this.state.localStream.removeTrack(videoTrack);
                this.state.localStream.addTrack(newVideoTrack);
                if (this.ui.elements.localVideo) {
                    this.ui.elements.localVideo.srcObject = this.state.localStream;
                }
                
                newStream.getAudioTracks().forEach(track => track.stop());
            }
        } catch (error) {
            console.error('Error switching camera:', error);
            this.state.currentCamera = this.state.currentCamera === 'user' ? 'environment' : 'user';
            alert('Erro ao trocar câmera. Verifique se o dispositivo suporta múltiplas câmeras.');
        }
    }
    
    async switchToVoice() {
        if (!this.state.localStream || !this.state.isVideoCall) return;
        
        this.state.localStream.getVideoTracks().forEach(track => {
            track.stop();
            this.state.localStream.removeTrack(track);
        });
        
        this.state.isVideoCall = false;
        this.state.isVideoEnabled = false;
        if (this.ui.elements.localVideo) {
            this.ui.elements.localVideo.style.display = 'none';
        }
        this.ui.showVideoOff();
        
        const sender = this.state.peerConnection.getSenders().find(s => 
            s.track && s.track.kind === 'video'
        );
        if (sender) {
            await sender.replaceTrack(null);
        }
    }
    
    minimizeCall() {
        this.state.isCallMinimized = true;
        this.ui.hideCallOverlay();
        this.ui.showMinimizedCallWidget();
    }
    
    maximizeCall() {
        this.state.isCallMinimized = false;
        this.ui.showCallOverlay();
        this.ui.hideMinimizedCallWidget();
    }
}

