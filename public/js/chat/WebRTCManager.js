/**
 * Gerenciador de conexões WebRTC
 */
class WebRTCManager {
    constructor(stateManager, config, uiManager, callManager, signalManager) {
        this.state = stateManager;
        this.config = config;
        this.ui = uiManager;
        this.callManager = callManager;
        this.signalManager = signalManager;
    }
    
    async createPeerConnection() {
        this.state.peerConnection = new RTCPeerConnection(this.config.rtcConfig);
        
        // Add local stream tracks
        if (this.state.localStream) {
            const availableAudioTracks = this.state.localStream.getAudioTracks();
            const availableVideoTracks = this.state.localStream.getVideoTracks();
            
            let tracksToAdd = [];
            
            if (this.state.isVideoCall) {
                tracksToAdd = [...availableAudioTracks, ...availableVideoTracks];
                console.log('Video call - adding all available tracks');
            } else {
                if (availableAudioTracks.length > 0) {
                    tracksToAdd = availableAudioTracks;
                    console.log('Voice call - adding audio tracks only');
                } else if (availableVideoTracks.length > 0) {
                    tracksToAdd = availableVideoTracks;
                    console.log('Voice call - no audio available, using video tracks instead');
                }
            }
            
            console.log('Adding local tracks to peer connection:', {
                totalTracks: this.state.localStream.getTracks().length,
                videoTracks: availableVideoTracks.length,
                audioTracks: availableAudioTracks.length,
                tracksToAdd: tracksToAdd.length,
                isVideoCall: this.state.isVideoCall,
                tracksToAddTypes: tracksToAdd.map(t => t.kind)
            });
            
            if (tracksToAdd.length === 0) {
                console.warn('No tracks available to add to peer connection!');
            }
            
            tracksToAdd.forEach(track => {
                console.log(`Adding local ${track.kind} track:`, {
                    enabled: track.enabled,
                    readyState: track.readyState,
                    muted: track.muted
                });
                const sender = this.state.peerConnection.addTrack(track, this.state.localStream);
                console.log(`Track added, sender:`, {
                    track: sender.track?.kind,
                    transport: sender.transport?.state
                });
            });
            
            if (!this.state.isVideoCall && availableVideoTracks.length > 0 && availableAudioTracks.length > 0) {
                console.log('Voice call detected - stopping unused video tracks from local stream');
                availableVideoTracks.forEach(track => {
                    track.stop();
                    console.log('Stopped video track for voice call');
                });
            }
            
            const transceivers = this.state.peerConnection.getTransceivers();
            transceivers.forEach(transceiver => {
                if (transceiver.direction !== 'sendrecv') {
                    console.log(`Setting transceiver direction to sendrecv for ${transceiver.receiver.track?.kind || 'unknown'}`);
                    transceiver.direction = 'sendrecv';
                }
            });
        } else {
            console.warn('No local stream available when creating peer connection');
        }
        
        this.setupEventHandlers();
    }
    
    setupEventHandlers() {
        // Handle remote stream
        this.state.peerConnection.ontrack = (event) => {
            this.handleRemoteTrack(event);
        };
        
        // Handle ICE candidates
        this.state.peerConnection.onicecandidate = (event) => {
            if (event.candidate) {
                console.log('Sending ICE candidate:', {
                    candidate: event.candidate.candidate,
                    sdpMLineIndex: event.candidate.sdpMLineIndex
                });
                this.signalManager.sendSignal('candidate', event.candidate);
            } else {
                console.log('ICE candidate gathering completed');
            }
        };
        
        // Handle connection state
        this.state.peerConnection.onconnectionstatechange = () => {
            this.handleConnectionStateChange();
        };
        
        // Handle ICE connection state
        this.state.peerConnection.oniceconnectionstatechange = () => {
            this.handleICEConnectionStateChange();
        };
    }
    
    handleRemoteTrack(event) {
        console.log('Received remote track:', {
            kind: event.track.kind,
            id: event.track.id,
            streams: event.streams.length,
            trackEnabled: event.track.enabled,
            trackReadyState: event.track.readyState,
            transceiver: event.transceiver?.currentDirection,
            receiver: event.receiver?.track?.kind
        });
        
        if (!this.state.remoteStream) {
            this.state.remoteStream = new MediaStream();
            console.log('Created new remote stream');
        }
        
        const existingTrack = this.state.remoteStream.getTracks().find(t => 
            t.id === event.track.id || 
            (t.kind === event.track.kind && t.readyState === 'live')
        );
        
        if (!existingTrack) {
            this.state.remoteStream.addTrack(event.track);
            console.log(`Added ${event.track.kind} track to remote stream. Total tracks:`, {
                video: this.state.remoteStream.getVideoTracks().length,
                audio: this.state.remoteStream.getAudioTracks().length
            });
        } else {
            console.log(`Track ${event.track.kind} already in stream, skipping duplicate`);
        }
        
        if (event.streams && event.streams.length > 0) {
            const eventStream = event.streams[0];
            eventStream.getTracks().forEach(track => {
                const exists = this.state.remoteStream.getTracks().some(t => t.id === track.id);
                if (!exists) {
                    this.state.remoteStream.addTrack(track);
                    console.log(`Added track from event stream: ${track.kind}`);
                }
            });
        }
        
        this.assignRemoteStreamToElements();
        
        if (this.state.inCall && !this.state.callStartTime) {
            console.log('First remote track received, starting timer');
            this.callManager.startCallTimer();
        }
    }
    
    assignRemoteStreamToElements() {
        const remoteStream = this.state.remoteStream;
        if (remoteStream && remoteStream.getTracks().length > 0) {
            console.log('Assigning remote stream to video elements...', {
                videoTracks: remoteStream.getVideoTracks().length,
                audioTracks: remoteStream.getAudioTracks().length,
                totalTracks: remoteStream.getTracks().length
            });
            
            const playDelay = this.config.isMobile ? 500 : 200;
            const maxRetries = this.config.isMobile ? 8 : 5;
            
            if (this.ui.elements.remoteVideo) {
                this.ui.elements.remoteVideo.autoplay = true;
                this.ui.elements.remoteVideo.playsInline = true;
                this.ui.elements.remoteVideo.muted = false;
                this.ui.elements.remoteVideo.setAttribute('playsinline', 'true');
                this.ui.elements.remoteVideo.setAttribute('webkit-playsinline', 'true');
                this.ui.elements.remoteVideo.srcObject = remoteStream;
            }
            
            if (this.ui.elements.minimizedRemoteVideo) {
                this.ui.elements.minimizedRemoteVideo.autoplay = true;
                this.ui.elements.minimizedRemoteVideo.playsInline = true;
                this.ui.elements.minimizedRemoteVideo.muted = false;
                this.ui.elements.minimizedRemoteVideo.setAttribute('playsinline', 'true');
                this.ui.elements.minimizedRemoteVideo.setAttribute('webkit-playsinline', 'true');
                this.ui.elements.minimizedRemoteVideo.srcObject = remoteStream;
            }
            
            console.log('Stream assigned to video elements');
            
            this.tryPlayVideo(this.ui.elements.remoteVideo, 'Remote video', playDelay, maxRetries);
            this.tryPlayVideo(this.ui.elements.minimizedRemoteVideo, 'Minimized remote video', playDelay, maxRetries);
            
            this.setupVideoListeners(remoteStream);
            
            remoteStream.getTracks().forEach(track => {
                console.log(`Remote ${track.kind} track:`, {
                    enabled: track.enabled,
                    readyState: track.readyState,
                    muted: track.muted
                });
            });
        }
    }
    
    tryPlayVideo(videoElement, elementName, playDelay, maxRetries, retryCount = 0) {
        if (!videoElement) return;
        
        const srcObject = videoElement.srcObject;
        if (srcObject) {
            const hasVideoTracks = srcObject.getVideoTracks().length > 0;
            const hasAudioTracks = srcObject.getAudioTracks().length > 0;
            
            if (!hasVideoTracks && !hasAudioTracks) {
                console.warn(`${elementName}: No tracks in stream yet, waiting...`);
                if (retryCount < maxRetries) {
                    setTimeout(() => this.tryPlayVideo(videoElement, elementName, playDelay, maxRetries, retryCount + 1), playDelay);
                }
                return;
            }
            
            const tracks = srcObject.getTracks();
            const liveTracks = tracks.filter(t => t.readyState === 'live' || t.readyState === 'ended');
            if (liveTracks.length === 0 && retryCount < maxRetries) {
                console.log(`${elementName}: Waiting for tracks to become live...`);
                setTimeout(() => this.tryPlayVideo(videoElement, elementName, playDelay, maxRetries, retryCount + 1), playDelay);
                return;
            }
        }
        
        if (videoElement.paused || videoElement.readyState < 2) {
            videoElement.play().then(() => {
                console.log(`${elementName} started playing successfully`, {
                    paused: videoElement.paused,
                    readyState: videoElement.readyState,
                    muted: videoElement.muted,
                    hasVideo: srcObject?.getVideoTracks().length > 0,
                    hasAudio: srcObject?.getAudioTracks().length > 0
                });
                
                if (videoElement.volume === 0) {
                    videoElement.volume = 1.0;
                    console.log(`${elementName}: Volume was 0, set to 1.0`);
                }
            }).catch(err => {
                if (err.name !== 'AbortError' && retryCount < maxRetries) {
                    console.warn(`${elementName} play failed (attempt ${retryCount + 1}/${maxRetries}):`, err.name, err.message);
                    setTimeout(() => {
                        this.tryPlayVideo(videoElement, elementName, playDelay, maxRetries, retryCount + 1);
                    }, playDelay * (retryCount + 1));
                } else if (err.name !== 'AbortError') {
                    console.error(`Error playing ${elementName} after ${maxRetries} retries:`, err);
                }
            });
        } else {
            console.log(`${elementName} is already playing`);
        }
    }
    
    setupVideoListeners(remoteStream) {
        if (!this.ui.elements.remoteVideo || this.ui.elements.remoteVideo.hasAttribute('data-listener-added')) {
            return;
        }
        
        this.ui.elements.remoteVideo.setAttribute('data-listener-added', 'true');
        
        this.ui.elements.remoteVideo.addEventListener('playing', () => {
            console.log('Remote video is now playing', {
                hasVideo: remoteStream.getVideoTracks().length > 0,
                hasAudio: remoteStream.getAudioTracks().length > 0,
                muted: this.ui.elements.remoteVideo.muted
            });
        });
        
        this.ui.elements.remoteVideo.addEventListener('loadedmetadata', () => {
            console.log('Remote video metadata loaded');
            if (this.state.remoteStream && this.ui.elements.remoteVideo.srcObject !== this.state.remoteStream) {
                this.ui.elements.remoteVideo.srcObject = this.state.remoteStream;
            }
            if (this.ui.elements.remoteVideo.paused) {
                this.ui.elements.remoteVideo.play().catch(err => {
                    if (err.name !== 'AbortError') {
                        console.warn('Play on loadedmetadata failed:', err);
                    }
                });
            }
        });
        
        remoteStream.addEventListener('addtrack', (event) => {
            console.log('Track added to remote stream:', event.track.kind);
            if (this.ui.elements.remoteVideo && this.ui.elements.remoteVideo.srcObject !== remoteStream) {
                this.ui.elements.remoteVideo.srcObject = remoteStream;
            }
            if (this.ui.elements.minimizedRemoteVideo && this.ui.elements.minimizedRemoteVideo.srcObject !== remoteStream) {
                this.ui.elements.minimizedRemoteVideo.srcObject = remoteStream;
            }
        });
    }
    
    handleConnectionStateChange() {
        const connectionState = this.state.peerConnection.connectionState;
        console.log('Connection state changed:', connectionState);
        
        if (connectionState === 'connected') {
            this.handleConnected();
        } else if (connectionState === 'failed') {
            this.handleConnectionFailed();
        } else if (connectionState === 'disconnected') {
            this.handleDisconnected();
        } else if (connectionState === 'connecting') {
            this.ui.updateCallStatus('Conectando...');
        }
    }
    
    handleConnected() {
        console.log('Peer connection connected!');
        
        if (this.state.connectionTimeout) {
            clearTimeout(this.state.connectionTimeout);
            this.state.connectionTimeout = null;
        }
        
        this.ui.updateCallStatus('Conectado');
        
        const transceivers = this.state.peerConnection.getTransceivers();
        console.log('Transceivers after connection:', transceivers.map(t => ({
            kind: t.receiver.track?.kind || t.sender.track?.kind,
            direction: t.direction,
            currentDirection: t.currentDirection,
            receiverTrack: t.receiver.track ? {
                kind: t.receiver.track.kind,
                enabled: t.receiver.track.enabled,
                readyState: t.receiver.track.readyState
            } : null
        })));
        
        transceivers.forEach(transceiver => {
            if (transceiver.receiver && transceiver.receiver.track) {
                const track = transceiver.receiver.track;
                if (!this.state.remoteStream) {
                    this.state.remoteStream = new MediaStream();
                }
                
                const exists = this.state.remoteStream.getTracks().some(t => t.id === track.id);
                if (!exists) {
                    this.state.remoteStream.addTrack(track);
                    console.log(`Added ${track.kind} track from transceiver to remote stream`);
                }
            }
        });
        
        if (this.state.remoteStream) {
            const videoTracks = this.state.remoteStream.getVideoTracks();
            const audioTracks = this.state.remoteStream.getAudioTracks();
            
            console.log('Remote stream state:', {
                videoTracks: videoTracks.length,
                audioTracks: audioTracks.length,
                videoTracksEnabled: videoTracks.filter(t => t.enabled).length,
                audioTracksEnabled: audioTracks.filter(t => t.enabled).length,
                videoTracksLive: videoTracks.filter(t => t.readyState === 'live').length,
                audioTracksLive: audioTracks.filter(t => t.readyState === 'live').length
            });
            
            if (this.ui.elements.remoteVideo && this.ui.elements.remoteVideo.srcObject !== this.state.remoteStream) {
                this.ui.elements.remoteVideo.srcObject = this.state.remoteStream;
            }
            if (this.ui.elements.minimizedRemoteVideo && this.ui.elements.minimizedRemoteVideo.srcObject !== this.state.remoteStream) {
                this.ui.elements.minimizedRemoteVideo.srcObject = this.state.remoteStream;
            }
            
            if (this.ui.elements.remoteVideo) {
                this.ui.elements.remoteVideo.muted = false;
                this.ui.elements.remoteVideo.volume = 1.0;
                if (this.ui.elements.remoteVideo.paused) {
                    this.ui.elements.remoteVideo.play().catch(err => {
                        console.warn('Auto-play failed, user interaction may be required:', err);
                    });
                }
            }
            
            audioTracks.forEach((track, index) => {
                console.log(`Audio track ${index}:`, {
                    id: track.id,
                    enabled: track.enabled,
                    readyState: track.readyState,
                    muted: track.muted,
                    settings: track.getSettings()
                });
            });
        } else {
            console.warn('No remote stream available after connection - this may indicate a problem');
        }
        
        this.callManager.startCallTimer();
        this.startTrackMonitor();
        
        setTimeout(() => {
            this.ui.hideCallStatus();
        }, 2000);
    }
    
    handleConnectionFailed() {
        console.error('Connection failed', {
            iceState: this.state.peerConnection.iceConnectionState,
            signalingState: this.state.peerConnection.signalingState
        });
        
        const hasActiveTracks = this.state.remoteStream && 
            this.state.remoteStream.getTracks().some(track => 
                track.readyState === 'live' && track.enabled
            );
        
        if (hasActiveTracks) {
            console.log('Connection failed but tracks are active - attempting recovery');
            this.ui.updateCallStatus('Conexão instável - tentando manter...');
        }
        
        if (this.state.peerConnection && this.state.inCall) {
            console.log('Attempting to restart ICE...');
            try {
                this.state.peerConnection.restartIce();
                if (!hasActiveTracks) {
                    this.ui.updateCallStatus('Reconectando...');
                }
                
                const retryTimeout = hasActiveTracks ? 10000 : 5000;
                setTimeout(() => {
                    if (this.state.peerConnection && this.state.peerConnection.connectionState === 'failed') {
                        const stillActive = this.state.remoteStream && 
                            this.state.remoteStream.getTracks().some(track => 
                                track.readyState === 'live' && track.enabled
                            );
                        
                        if (stillActive) {
                            console.warn('Connection state is failed but tracks remain active - continuing');
                            this.ui.updateCallStatus('Conexão mantida');
                        } else {
                            console.error('Connection still failed after ICE restart - no active tracks');
                            this.ui.updateCallStatus('Conexão perdida - tente novamente');
                        }
                    }
                }, retryTimeout);
            } catch (restartError) {
                console.error('Error restarting ICE:', restartError);
                if (!hasActiveTracks) {
                    this.ui.updateCallStatus('Conexão perdida');
                }
            }
        } else {
            if (!hasActiveTracks) {
                this.ui.updateCallStatus('Conexão perdida');
            }
        }
    }
    
    handleDisconnected() {
        console.warn('Connection disconnected', {
            iceState: this.state.peerConnection.iceConnectionState
        });
        
        if (this.state.peerConnection && this.state.peerConnection.iceConnectionState === 'failed') {
            this.ui.updateCallStatus('Conexão perdida');
        } else {
            this.ui.updateCallStatus('Reconectando...');
            
            setTimeout(() => {
                if (this.state.peerConnection && 
                    this.state.peerConnection.connectionState === 'disconnected' &&
                    this.state.peerConnection.iceConnectionState !== 'failed' &&
                    this.state.inCall) {
                    console.log('Still disconnected, attempting ICE restart...');
                    try {
                        this.state.peerConnection.restartIce();
                    } catch (err) {
                        console.error('Error restarting ICE after disconnect:', err);
                    }
                }
            }, 3000);
        }
    }
    
    handleICEConnectionStateChange() {
        const iceState = this.state.peerConnection.iceConnectionState;
        console.log('ICE connection state:', iceState);
        
        if (iceState === 'connected' || iceState === 'completed') {
            console.log('ICE connection established');
            if (this.state.peerConnection.connectionState === 'connected') {
                this.ui.updateCallStatus('Conectado');
            }
        } else if (iceState === 'failed') {
            this.handleICEFailed();
        } else if (iceState === 'disconnected') {
            console.warn('ICE disconnected, may reconnect');
            
            if (this.state.remoteStream) {
                const activeTracks = this.state.remoteStream.getTracks().filter(t => 
                    t.readyState === 'live' && t.enabled
                );
                if (activeTracks.length > 0) {
                    console.log('ICE disconnected but tracks are still active - waiting for reconnect');
                }
            }
        } else if (iceState === 'checking') {
            console.log('ICE checking...');
            this.ui.updateCallStatus('Conectando...');
        }
    }
    
    handleICEFailed() {
        console.error('ICE connection failed');
        
        const hasActiveTracks = this.state.remoteStream && 
            this.state.remoteStream.getTracks().some(track => 
                track.readyState === 'live' && track.enabled
            );
        
        console.log('ICE failed, checking stream status:', {
            hasRemoteStream: !!this.state.remoteStream,
            hasActiveTracks: hasActiveTracks,
            trackStates: this.state.remoteStream ? 
                this.state.remoteStream.getTracks().map(t => ({
                    kind: t.kind,
                    enabled: t.enabled,
                    readyState: t.readyState
                })) : []
        });
        
        if (hasActiveTracks) {
            console.log('ICE failed but tracks are active - attempting recovery...');
            this.ui.updateCallStatus('Reconectando...');
        }
        
        if (this.state.peerConnection && this.state.inCall) {
            console.log('ICE failed, attempting restart...');
            try {
                this.state.peerConnection.restartIce();
                if (!hasActiveTracks) {
                    this.ui.updateCallStatus('Reconectando...');
                }
                
                const retryTimeout = hasActiveTracks ? 10000 : 5000;
                setTimeout(() => {
                    if (this.state.peerConnection && 
                        this.state.peerConnection.iceConnectionState === 'failed') {
                        if (!hasActiveTracks) {
                            console.error('ICE restart failed - no recovery possible');
                            this.ui.updateCallStatus('Falha na conexão ICE');
                        } else {
                            console.warn('ICE failed but tracks remain active - attempting to continue');
                            this.ui.updateCallStatus('Conexão instável - tentando manter...');
                        }
                    }
                }, retryTimeout);
            } catch (restartError) {
                console.error('Error restarting ICE:', restartError);
                if (!hasActiveTracks) {
                    this.ui.updateCallStatus('Falha na conexão ICE');
                }
            }
        } else {
            if (!hasActiveTracks) {
                this.ui.updateCallStatus('Falha na conexão ICE');
            }
        }
    }
    
    startTrackMonitor() {
        if (this.state.trackMonitorInterval) {
            return;
        }
        
        this.state.trackMonitorInterval = setInterval(() => {
            if (!this.state.peerConnection || !this.state.inCall) {
                if (this.state.trackMonitorInterval) {
                    clearInterval(this.state.trackMonitorInterval);
                    this.state.trackMonitorInterval = null;
                }
                return;
            }
            
            if (this.state.remoteStream) {
                const tracks = this.state.remoteStream.getTracks();
                const activeTracks = tracks.filter(t => 
                    t.readyState === 'live' && t.enabled
                );
                
                if (activeTracks.length > 0 && 
                    this.state.peerConnection.connectionState === 'failed') {
                    console.log('Tracks are active despite failed connection state - attempting recovery');
                    try {
                        this.state.peerConnection.restartIce();
                    } catch (err) {
                        console.warn('Error restarting ICE in monitor:', err);
                    }
                }
                
                if (tracks.length > 0) {
                    console.log('Track monitor:', {
                        totalTracks: tracks.length,
                        activeTracks: activeTracks.length,
                        connectionState: this.state.peerConnection.connectionState,
                        iceState: this.state.peerConnection.iceConnectionState
                    });
                }
            }
        }, 5000);
    }
    
    async handleIncomingCandidate(candidate) {
        try {
            if (!this.state.peerConnection) {
                console.warn('Received candidate but no peer connection exists');
                return;
            }
            
            if (!this.state.peerConnection.remoteDescription) {
                console.log('Remote description not set yet, queuing candidate:', {
                    candidate: candidate.candidate?.substring(0, 50),
                    sdpMLineIndex: candidate.sdpMLineIndex
                });
                this.state.pendingCandidates.push(candidate);
                return;
            }
            
            const transceivers = this.state.peerConnection.getTransceivers();
            const sdpMLineIndex = candidate.sdpMLineIndex;
            
            if (sdpMLineIndex !== null && sdpMLineIndex >= transceivers.length) {
                console.warn('Skipping candidate for non-existent media line:', {
                    sdpMLineIndex,
                    availableTransceivers: transceivers.length
                });
                return;
            }
            
            console.log('Processing incoming ICE candidate:', {
                candidate: candidate.candidate?.substring(0, 50),
                sdpMLineIndex: candidate.sdpMLineIndex,
                sdpMid: candidate.sdpMid
            });
            
            let retries = 0;
            const maxRetries = this.config.isMobile ? 3 : 1;
            
            while (retries <= maxRetries) {
                try {
                    await this.state.peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
                    console.log('ICE candidate added successfully');
                    break;
                } catch (addError) {
                    retries++;
                    if (retries > maxRetries) {
                        throw addError;
                    }
                    await new Promise(resolve => setTimeout(resolve, this.config.isMobile ? 100 : 50));
                }
            }
        } catch (error) {
            if (error.message && (error.message.includes('sdpMLineIndex') || error.message.includes('InvalidStateError'))) {
                if (error.message.includes('InvalidStateError') && !this.state.peerConnection?.remoteDescription) {
                    console.log('Queueing candidate (remote description not set):', {
                        candidate: candidate.candidate?.substring(0, 50)
                    });
                    this.state.pendingCandidates.push(candidate);
                    return;
                }
                console.warn('Candidate skipped:', {
                    sdpMLineIndex: candidate.sdpMLineIndex,
                    error: error.message,
                    connectionState: this.state.peerConnection?.connectionState
                });
            } else {
                console.error('Error handling candidate:', error, candidate);
            }
        }
    }
    
    async processPendingCandidates() {
        if (!this.state.peerConnection || !this.state.peerConnection.remoteDescription) {
            return;
        }
        
        if (this.state.pendingCandidates.length === 0) {
            return;
        }
        
        console.log(`Processing ${this.state.pendingCandidates.length} pending ICE candidates...`);
        
        const candidates = [...this.state.pendingCandidates];
        this.state.pendingCandidates = [];
        
        for (const candidate of candidates) {
            try {
                await this.state.peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
                console.log('Queued ICE candidate added successfully');
            } catch (error) {
                if (!error.message.includes('InvalidStateError')) {
                    console.warn('Error adding queued candidate:', error.message);
                }
            }
        }
    }
    
    async handleIncomingAnswer(answer) {
        try {
            if (!this.state.peerConnection) {
                console.warn('Received answer but no peer connection exists yet');
                return;
            }
            
            if (this.state.peerConnection.remoteDescription) {
                const currentRemoteSDP = this.state.peerConnection.remoteDescription.sdp;
                const incomingSDP = answer?.sdp;
                
                if (incomingSDP && currentRemoteSDP && 
                    currentRemoteSDP.substring(0, 100) === incomingSDP.substring(0, 100)) {
                    console.log('Ignoring duplicate answer - remote description already set');
                    return;
                }
                
                if (this.state.peerConnection.signalingState === 'stable') {
                    console.log('Ignoring answer - connection already has remote description in stable state');
                    return;
                }
            }
            
            console.log('Processing incoming answer...', {
                hasPeerConnection: !!this.state.peerConnection,
                answerType: answer?.type,
                hasSDP: !!answer?.sdp,
                connectionState: this.state.peerConnection?.connectionState,
                signalingState: this.state.peerConnection?.signalingState,
                hasRemoteDescription: !!this.state.peerConnection?.remoteDescription
            });
            
            if (!answer || !answer.type || !answer.sdp) {
                console.error('Invalid answer payload:', answer);
                return;
            }
            
            const cleanedSDP = Utils.cleanSDP(answer.sdp);
            if (!cleanedSDP) {
                throw new Error('Invalid SDP in answer');
            }
            
            const cleanAnswer = {
                type: answer.type,
                sdp: cleanedSDP
            };
            
            console.log('Setting remote description with answer...');
            const sessionDescription = new RTCSessionDescription(cleanAnswer);
            await this.state.peerConnection.setRemoteDescription(sessionDescription);
            
            await this.processPendingCandidates();
            
            this.state.answerReceived = true;
            
            if (this.state.connectionTimeout) {
                clearTimeout(this.state.connectionTimeout);
                this.state.connectionTimeout = null;
            }
            
            console.log('Answer processed successfully, connection state:', this.state.peerConnection.connectionState);
            this.ui.updateCallStatus('Conectando...');
        } catch (error) {
            console.error('Error handling answer:', error);
            
            if (error.message && (error.message.includes('wrong state') || 
                                  error.message.includes('stable') ||
                                  error.message.includes('InvalidStateError'))) {
                console.warn('Answer error (likely duplicate):', error.message);
                return;
            }
            
            if (error.message && error.message.includes('SDP')) {
                console.error('SDP parsing error in answer:', error);
                if (!this.state.peerConnection.remoteDescription) {
                    this.callManager.endCall(false);
                    alert('Erro na conexão. A chamada será encerrada.');
                } else {
                    console.warn('SDP error but remote description exists - connection may still work');
                }
            }
        }
    }
}

