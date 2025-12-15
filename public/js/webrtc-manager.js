/**
 * WebRTC Manager - Gerenciamento de chamadas de voz/vídeo
 * Sistema Híbrido: WebSocket (Reverb) para sinalização E mídia (fallback)
 * WebRTC para mídia principal, WebSocket como backup automático
 */

class WebRTCManager {
    constructor(roomCode, senderType) {
        this.roomCode = roomCode;
        this.senderType = senderType;
        this.peerConnection = null;
        this.localStream = null;
        this.remoteStream = null;
        this.isCallActive = false;
        this.isVideoEnabled = true;
        this.isAudioEnabled = true;
        this.isFrontCamera = true;
        this.isMinimized = false;
        
        // Modo de transmissão: 'webrtc' ou 'websocket'
        this.transmissionMode = 'webrtc';
        this.fallbackToWebSocket = false;
        
        // Canvas para capturar frames de vídeo (WebSocket fallback)
        this.videoCanvas = null;
        this.videoContext = null;
        this.audioContext = null;
        this.mediaRecorder = null;
        this.audioWorkletNode = null;
        
        // Intervalos para captura de mídia via WebSocket
        this.videoCaptureInterval = null;
        this.audioCaptureInterval = null;
        
        // Monitoramento de conexão
        this.webrtcHealthCheck = null;
        this.webrtcFailureCount = 0;
        this.maxFailuresBeforeFallback = 3;
        
        // Buffer para frames recebidos via WebSocket
        this.websocketVideoBuffer = [];
        this.websocketAudioBuffer = [];
        
        // Canvas para renderizar vídeo remoto via WebSocket
        this.remoteVideoCanvas = null;
        this.remoteVideoContext = null;
        this.websocketVideoStream = null;
        
        // Configuração STUN/TURN (pode adicionar servidores TURN para produção)
        this.rtcConfig = {
            iceServers: [
                { urls: 'stun:stun.l.google.com:19302' },
                { urls: 'stun:stun1.l.google.com:19302' }
            ]
        };
        
        this.setupEventListeners();
        this.initializeCanvas();
    }
    
    /**
     * Inicializar canvas para captura de frames
     */
    initializeCanvas() {
        this.videoCanvas = document.createElement('canvas');
        this.videoContext = this.videoCanvas.getContext('2d');
    }
    
    /**
     * Limpar SDP removendo linhas inválidas
     */
    cleanSDP(sdp) {
        if (typeof sdp !== 'string') {
            return sdp;
        }
        
        // Remover linhas inválidas do SDP
        const lines = sdp.split(/\r?\n/);
        const cleanedLines = [];
        
        for (let line of lines) {
            // Remover linhas vazias
            if (!line || line.trim() === '') {
                cleanedLines.push('');
                continue;
            }
            
            // Corrigir linhas msid inválidas
            // Formato esperado: a=ssrc:123 msid:stream-id track-id
            // Problema: às vezes vem com múltiplos msid na mesma linha
            if (line.startsWith('a=ssrc:') && line.includes('msid:')) {
                const parts = line.split(' ');
                if (parts.length > 3) {
                    // Pegar apenas ssrc, primeiro msid e primeiro track-id
                    const ssrcPart = parts[0];
                    const msidIndex = parts.findIndex(p => p.startsWith('msid:'));
                    if (msidIndex !== -1 && msidIndex + 1 < parts.length) {
                        line = `${ssrcPart} ${parts[msidIndex]} ${parts[msidIndex + 1]}`;
                    } else {
                        // Se não encontrar msid válido, manter apenas ssrc
                        line = ssrcPart;
                    }
                }
            }
            
            cleanedLines.push(line);
        }
        
        return cleanedLines.join('\r\n');
    }
    
    /**
     * Configurar listeners do WebSocket para sinalização
     */
    setupEventListeners() {
        if (!window.Echo) {
            console.error('[WebRTC] Echo não disponível');
            return;
        }
        
        const channel = window.Echo.channel(`chat-room.${this.roomCode}`);
        
        // Escutar sinais de chamada via WebSocket
        channel.listen('.call.signal', (event) => {
            console.log('[WebRTC] Sinal recebido:', event);
            this.handleSignal(event.data);
        });
    }
    
    /**
     * Iniciar chamada (iniciador)
     */
    async startCall(isVideo = true) {
        try {
            console.log('[WebRTC] Iniciando chamada...');
            this.isCallActive = true;
            this.isVideoEnabled = isVideo;
            
            // Obter stream local
            await this.getLocalStream(isVideo);
            
            // Criar conexão peer
            this.createPeerConnection();
            
            // Adicionar tracks locais
            this.localStream.getTracks().forEach(track => {
                this.peerConnection.addTrack(track, this.localStream);
            });
            
            // Criar e enviar offer
            const offer = await this.peerConnection.createOffer();
            await this.peerConnection.setLocalDescription(offer);
            
            this.sendSignal('offer', offer);
            
            // Preparar captura WebSocket (mas não enviar ainda)
            // Só enviará quando WebRTC falhar
            this.prepareWebSocketMediaCapture();
            
            console.log('[WebRTC] Offer enviado');
            return true;
        } catch (error) {
            console.error('[WebRTC] Erro ao iniciar chamada:', error);
            this.endCall();
            throw error;
        }
    }
    
    /**
     * Aceitar chamada (receptor)
     */
    async acceptCall(offer) {
        try {
            console.log('[WebRTC] Aceitando chamada...');
            this.isCallActive = true;
            
            // Obter stream local
            await this.getLocalStream(true);
            
            // Criar conexão peer
            this.createPeerConnection();
            
            // Adicionar tracks locais
            this.localStream.getTracks().forEach(track => {
                this.peerConnection.addTrack(track, this.localStream);
            });
            
            // Processar offer recebido
            // Garantir que offer está no formato correto
            let offerDesc = offer;
            if (typeof offer === 'object' && offer.type && offer.sdp) {
                offerDesc = offer;
            } else if (typeof offer === 'string') {
                // Se for string, tentar parsear como JSON
                try {
                    offerDesc = JSON.parse(offer);
                } catch (e) {
                    throw new Error('Invalid offer format');
                }
            }
            
            // Limpar SDP se necessário (remover linhas inválidas)
            if (offerDesc.sdp) {
                offerDesc.sdp = this.cleanSDP(offerDesc.sdp);
            }
            
            await this.peerConnection.setRemoteDescription(new RTCSessionDescription(offerDesc));
            
            // Criar e enviar answer
            const answer = await this.peerConnection.createAnswer();
            await this.peerConnection.setLocalDescription(answer);
            
            this.sendSignal('answer', answer);
            
            // Preparar captura WebSocket (mas não enviar ainda)
            this.prepareWebSocketMediaCapture();
            
            console.log('[WebRTC] Answer enviado');
            return true;
        } catch (error) {
            console.error('[WebRTC] Erro ao aceitar chamada:', error);
            this.endCall();
            throw error;
        }
    }
    
    /**
     * Obter stream de mídia local
     */
    async getLocalStream(isVideo = true) {
        try {
            const constraints = {
                audio: true,
                video: isVideo ? {
                    facingMode: this.isFrontCamera ? 'user' : 'environment',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                } : false
            };
            
            this.localStream = await navigator.mediaDevices.getUserMedia(constraints);
            console.log('[WebRTC] Stream local obtido');
            return this.localStream;
        } catch (error) {
            console.error('[WebRTC] Erro ao obter stream:', error);
            throw error;
        }
    }
    
    /**
     * Criar conexão peer WebRTC
     */
    createPeerConnection() {
        this.peerConnection = new RTCPeerConnection(this.rtcConfig);
        
        // Quando receber stream remoto
        this.peerConnection.ontrack = (event) => {
            console.log('[WebRTC] Stream remoto recebido');
            this.remoteStream = event.streams[0];
            this.onRemoteStream(this.remoteStream);
            this.webrtcFailureCount = 0; // Reset contador de falhas
        };
        
        // Quando ICE candidate for gerado
        this.peerConnection.onicecandidate = (event) => {
            if (event.candidate) {
                this.sendSignal('candidate', event.candidate);
            }
        };
        
        // Quando conexão mudar de estado
        this.peerConnection.onconnectionstatechange = () => {
            const state = this.peerConnection.connectionState;
            console.log('[WebRTC] Estado da conexão:', state);
            
            if (state === 'connected' || state === 'connecting') {
                this.webrtcFailureCount = 0;
                // Se estava em fallback, tentar voltar para WebRTC
                if (this.transmissionMode === 'websocket') {
                    this.attemptWebRTCRecovery();
                }
            } else if (state === 'failed' || state === 'disconnected') {
                this.webrtcFailureCount++;
                console.warn(`[WebRTC] Falha detectada (${this.webrtcFailureCount}/${this.maxFailuresBeforeFallback})`);
                
                if (this.webrtcFailureCount >= this.maxFailuresBeforeFallback) {
                    console.log('[WebRTC] Mudando para fallback WebSocket...');
                    this.switchToWebSocketFallback();
                }
            }
        };
        
        // Quando ICE connection mudar
        this.peerConnection.oniceconnectionstatechange = () => {
            const iceState = this.peerConnection.iceConnectionState;
            console.log('[WebRTC] Estado ICE:', iceState);
            
            if (iceState === 'failed' || iceState === 'disconnected') {
                this.webrtcFailureCount++;
                if (this.webrtcFailureCount >= this.maxFailuresBeforeFallback) {
                    this.switchToWebSocketFallback();
                }
            } else if (iceState === 'connected' || iceState === 'completed') {
                this.webrtcFailureCount = 0;
            }
        };
        
        // Iniciar monitoramento de saúde
        this.startWebRTCHealthCheck();
    }
    
    /**
     * Monitoramento de saúde do WebRTC
     */
    startWebRTCHealthCheck() {
        if (this.webrtcHealthCheck) {
            clearInterval(this.webrtcHealthCheck);
        }
        
        this.webrtcHealthCheck = setInterval(() => {
            if (!this.peerConnection || this.transmissionMode === 'websocket') {
                return;
            }
            
            const state = this.peerConnection.connectionState;
            const iceState = this.peerConnection.iceConnectionState;
            
            // Verificar se há stream remoto ativo
            const hasRemoteStream = this.remoteStream && 
                this.remoteStream.getTracks().some(track => track.readyState === 'live');
            
            if ((state === 'failed' || iceState === 'failed' || !hasRemoteStream) && 
                this.transmissionMode === 'webrtc') {
                this.webrtcFailureCount++;
                console.warn(`[WebRTC] Health check falhou (${this.webrtcFailureCount}/${this.maxFailuresBeforeFallback})`);
                
                if (this.webrtcFailureCount >= this.maxFailuresBeforeFallback) {
                    this.switchToWebSocketFallback();
                }
            } else {
                this.webrtcFailureCount = Math.max(0, this.webrtcFailureCount - 1);
            }
        }, 3000); // Verificar a cada 3 segundos
    }
    
    /**
     * Preparar captura de mídia via WebSocket (sem enviar ainda)
     */
    prepareWebSocketMediaCapture() {
        // Esta função apenas prepara os recursos, mas não inicia a transmissão
        // A transmissão só começará quando switchToWebSocketFallback() for chamado
        if (!this.localStream) return;
        
        // Inicializar canvas se necessário
        if (!this.videoCanvas) {
            this.initializeCanvas();
        }
        
        console.log('[WebSocket] Captura preparada (aguardando fallback se necessário)');
    }
    
    /**
     * Alternar para fallback WebSocket
     */
    switchToWebSocketFallback() {
        if (this.transmissionMode === 'websocket') {
            return; // Já está em fallback
        }
        
        console.log('[WebRTC] Alternando para WebSocket (fallback)');
        this.transmissionMode = 'websocket';
        this.fallbackToWebSocket = true;
        
        // Iniciar captura e transmissão via WebSocket
        this.startWebSocketMediaTransmission();
        
        // Notificar callback
        this.onTransmissionModeChanged('websocket');
    }
    
    /**
     * Tentar recuperar WebRTC
     */
    async attemptWebRTCRecovery() {
        if (this.transmissionMode === 'webrtc') {
            return; // Já está em WebRTC
        }
        
        console.log('[WebRTC] Tentando recuperar conexão WebRTC...');
        
        try {
            // Verificar se conexão WebRTC está estável
            if (this.peerConnection && 
                this.peerConnection.connectionState === 'connected' &&
                this.peerConnection.iceConnectionState === 'connected') {
                
                // Aguardar alguns segundos para confirmar estabilidade
                await new Promise(resolve => setTimeout(resolve, 5000));
                
                if (this.peerConnection.connectionState === 'connected') {
                    console.log('[WebRTC] WebRTC recuperado, voltando para modo principal');
                    this.transmissionMode = 'webrtc';
                    this.fallbackToWebSocket = false;
                    this.webrtcFailureCount = 0;
                    this.stopWebSocketMediaTransmission();
                    this.onTransmissionModeChanged('webrtc');
                }
            }
        } catch (error) {
            console.error('[WebRTC] Erro ao tentar recuperar WebRTC:', error);
        }
    }
    
    /**
     * Iniciar transmissão de mídia via WebSocket
     */
    startWebSocketMediaTransmission() {
        if (!this.localStream) return;
        
        // Se já está transmitindo, não reiniciar
        if (this.videoCaptureInterval || this.audioCaptureInterval) {
            return;
        }
        
        console.log('[WebSocket] Iniciando transmissão de mídia (backup)');
        
        // Capturar e enviar frames de vídeo
        if (this.isVideoEnabled) {
            const videoTrack = this.localStream.getVideoTracks()[0];
            if (videoTrack) {
                const videoElement = document.createElement('video');
                videoElement.srcObject = new MediaStream([videoTrack]);
                videoElement.play();
                
                // Configurar canvas
                this.videoCanvas.width = 640;
                this.videoCanvas.height = 480;
                
                // Capturar frames a cada 100ms (10 FPS para reduzir carga)
                this.videoCaptureInterval = setInterval(() => {
                    // Só enviar frames se estiver em modo WebSocket ou se WebRTC falhou
                    if (this.transmissionMode === 'websocket' || this.fallbackToWebSocket) {
                        if (videoElement.readyState === videoElement.HAVE_ENOUGH_DATA) {
                            this.videoContext.drawImage(videoElement, 0, 0, this.videoCanvas.width, this.videoCanvas.height);
                            
                            // Converter para base64 (JPEG com qualidade reduzida)
                            const frameData = this.videoCanvas.toDataURL('image/jpeg', 0.5);
                            
                            // Enviar frame via WebSocket
                            this.sendSignal('media_frame', {
                                type: 'video',
                                data: frameData,
                                timestamp: Date.now()
                            });
                        }
                    }
                }, 100); // 10 FPS
            }
        }
        
        // Capturar e enviar áudio (apenas quando em fallback)
        if (this.isAudioEnabled && this.transmissionMode === 'websocket') {
            const audioTrack = this.localStream.getAudioTracks()[0];
            if (audioTrack) {
                // Usar AudioContext para capturar amostras de áudio
                this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const source = this.audioContext.createMediaStreamSource(new MediaStream([audioTrack]));
                
                // Analyser para obter dados de áudio
                const analyser = this.audioContext.createAnalyser();
                analyser.fftSize = 2048;
                source.connect(analyser);
                
                const bufferLength = analyser.frequencyBinCount;
                const dataArray = new Uint8Array(bufferLength);
                
                // Capturar amostras de áudio
                this.audioCaptureInterval = setInterval(() => {
                    if (this.transmissionMode === 'websocket') {
                        analyser.getByteTimeDomainData(dataArray);
                        
                        // Enviar amostras via WebSocket
                        this.sendSignal('media_frame', {
                            type: 'audio',
                            data: Array.from(dataArray),
                            timestamp: Date.now()
                        });
                    }
                }, 100); // 10 vezes por segundo
            }
        }
        
        console.log('[WebSocket] Transmissão de mídia iniciada');
    }
    
    /**
     * Parar transmissão de mídia via WebSocket
     */
    stopWebSocketMediaTransmission() {
        if (this.videoCaptureInterval) {
            clearInterval(this.videoCaptureInterval);
            this.videoCaptureInterval = null;
        }
        
        if (this.audioCaptureInterval) {
            clearInterval(this.audioCaptureInterval);
            this.audioCaptureInterval = null;
        }
        
        if (this.audioContext) {
            this.audioContext.close();
            this.audioContext = null;
        }
        
        console.log('[WebSocket] Transmissão de mídia parada');
    }
    
    /**
     * Processar frame de mídia recebido via WebSocket
     */
    processWebSocketMediaFrame(frameData) {
        if (frameData.type === 'video') {
            // Adicionar frame ao buffer
            this.websocketVideoBuffer.push({
                data: frameData.data,
                timestamp: frameData.timestamp || Date.now()
            });
            
            // Limitar buffer (manter últimos 3 frames)
            if (this.websocketVideoBuffer.length > 3) {
                this.websocketVideoBuffer.shift();
            }
            
            // Renderizar frame mais recente
            this.renderWebSocketVideoFrame();
        } else if (frameData.type === 'audio') {
            // Processar áudio recebido via WebSocket
            // Nota: Áudio via WebSocket requer processamento mais complexo
            // Por enquanto, manteremos apenas WebRTC para áudio de alta qualidade
            this.websocketAudioBuffer.push(frameData);
            
            // Limitar buffer
            if (this.websocketAudioBuffer.length > 10) {
                this.websocketAudioBuffer.shift();
            }
        }
    }
    
    /**
     * Renderizar frame de vídeo recebido via WebSocket
     */
    renderWebSocketVideoFrame() {
        if (this.websocketVideoBuffer.length === 0) return;
        
        const remoteVideoElement = document.getElementById('remote-video');
        if (!remoteVideoElement) return;
        
        // Pegar frame mais recente
        const latestFrame = this.websocketVideoBuffer[this.websocketVideoBuffer.length - 1];
        
        // Criar canvas se não existir
        if (!this.remoteVideoCanvas) {
            this.remoteVideoCanvas = document.createElement('canvas');
            this.remoteVideoCanvas.width = 1280;
            this.remoteVideoCanvas.height = 720;
            this.remoteVideoContext = this.remoteVideoCanvas.getContext('2d');
        }
        
        const img = new Image();
        img.onload = () => {
            // Desenhar frame no canvas
            this.remoteVideoContext.clearRect(0, 0, this.remoteVideoCanvas.width, this.remoteVideoCanvas.height);
            this.remoteVideoContext.drawImage(img, 0, 0, this.remoteVideoCanvas.width, this.remoteVideoCanvas.height);
            
            // Criar stream a partir do canvas para o elemento video
            if (!this.websocketVideoStream) {
                this.websocketVideoStream = this.remoteVideoCanvas.captureStream(10); // 10 FPS
                remoteVideoElement.srcObject = this.websocketVideoStream;
            }
        };
        img.onerror = () => {
            console.error('[WebSocket] Erro ao carregar frame de vídeo');
        };
        img.src = latestFrame.data;
    }
    
    /**
     * Processar sinal recebido via WebSocket
     */
    async handleSignal(signalData) {
        const { type, payload, sender_type } = signalData;
        
        // Ignorar sinais do próprio usuário
        if (sender_type === this.senderType) {
            return;
        }
        
        try {
            switch (type) {
                case 'offer':
                    await this.acceptCall(payload);
                    break;
                    
                case 'answer':
                    if (this.peerConnection) {
                        let answerDesc = payload;
                        if (typeof payload === 'object' && payload.type && payload.sdp) {
                            answerDesc = payload;
                        } else if (typeof payload === 'string') {
                            try {
                                answerDesc = JSON.parse(payload);
                            } catch (e) {
                                console.error('[WebRTC] Erro ao parsear answer:', e);
                                return;
                            }
                        }
                        
                        // Limpar SDP se necessário
                        if (answerDesc.sdp) {
                            answerDesc.sdp = this.cleanSDP(answerDesc.sdp);
                        }
                        
                        await this.peerConnection.setRemoteDescription(new RTCSessionDescription(answerDesc));
                    }
                    break;
                    
                case 'candidate':
                    if (this.peerConnection) {
                        await this.peerConnection.addIceCandidate(new RTCIceCandidate(payload));
                    }
                    break;
                    
                case 'media_frame':
                    // Processar frame de mídia recebido via WebSocket (fallback)
                    if (this.transmissionMode === 'websocket' || this.fallbackToWebSocket) {
                        this.processWebSocketMediaFrame(payload);
                    }
                    break;
                    
                case 'bye':
                    this.endCall();
                    break;
            }
        } catch (error) {
            console.error('[WebRTC] Erro ao processar sinal:', error);
        }
    }
    
    /**
     * Enviar sinal via WebSocket
     */
    sendSignal(type, payload) {
        // Enviar via WebSocket (whisper/client event) em vez de HTTP
        if (!window.Echo) {
            console.error('[WebRTC] Echo não disponível');
            return;
        }

        try {
            const privateChannel = window.Echo.private(`private-chat-room.${this.roomCode}`);
            
            if (!privateChannel) {
                console.error('[WebRTC] Canal privado não disponível');
                return;
            }

            // Enviar via whisper (client event)
            privateChannel.whisper('client-call-signal', {
                room_code: this.roomCode,
                type: type,
                payload: payload,
                sender_type: this.senderType
            });

            debugLog('[WebRTC] Sinal enviado via WebSocket:', type);
        } catch (error) {
            console.error('[WebRTC] Erro ao enviar sinal via WebSocket:', error);
        }
    }
    
    /**
     * Toggle vídeo (ligar/desligar câmera)
     */
    toggleVideo() {
        if (!this.localStream) return;
        
        const videoTrack = this.localStream.getVideoTracks()[0];
        if (videoTrack) {
            this.isVideoEnabled = !this.isVideoEnabled;
            videoTrack.enabled = this.isVideoEnabled;
            this.onVideoToggled(this.isVideoEnabled);
            
            // Sincronizar ação via WebSocket
            this.sendSignal('call_action', {
                action: 'video_toggled',
                enabled: this.isVideoEnabled,
                timestamp: Date.now()
            });
        }
    }
    
    /**
     * Toggle áudio (mutar/desmutar)
     */
    toggleAudio() {
        if (!this.localStream) return;
        
        const audioTrack = this.localStream.getAudioTracks()[0];
        if (audioTrack) {
            this.isAudioEnabled = !this.isAudioEnabled;
            audioTrack.enabled = this.isAudioEnabled;
            this.onAudioToggled(this.isAudioEnabled);
            
            // Sincronizar ação via WebSocket
            this.sendSignal('call_action', {
                action: 'audio_toggled',
                enabled: this.isAudioEnabled,
                timestamp: Date.now()
            });
        }
    }
    
    /**
     * Trocar câmera (frente/trás)
     */
    async switchCamera() {
        if (!this.localStream || !this.isVideoEnabled) return;
        
        try {
            const videoTrack = this.localStream.getVideoTracks()[0];
            if (!videoTrack) return;
            
            // Parar track atual
            videoTrack.stop();
            
            // Trocar câmera
            this.isFrontCamera = !this.isFrontCamera;
            
            // Obter novo stream
            const newStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: this.isFrontCamera ? 'user' : 'environment'
                }
            });
            
            const newVideoTrack = newStream.getVideoTracks()[0];
            
            // Substituir track no stream local
            const sender = this.peerConnection.getSenders().find(s => 
                s.track && s.track.kind === 'video'
            );
            
            if (sender) {
                await sender.replaceTrack(newVideoTrack);
            }
            
            // Atualizar stream local
            this.localStream.removeTrack(videoTrack);
            this.localStream.addTrack(newVideoTrack);
            
            this.onCameraSwitched(this.isFrontCamera);
            
            // Sincronizar ação via WebSocket
            this.sendSignal('call_action', {
                action: 'camera_switched',
                isFront: this.isFrontCamera,
                timestamp: Date.now()
            });
        } catch (error) {
            console.error('[WebRTC] Erro ao trocar câmera:', error);
        }
    }
    
    /**
     * Minimizar/maximizar chamada
     */
    toggleMinimize() {
        this.isMinimized = !this.isMinimized;
        this.onMinimizeToggled(this.isMinimized);
    }
    
    /**
     * Encerrar chamada
     */
    endCall() {
        console.log('[WebRTC] Encerrando chamada...');
        
        this.isCallActive = false;
        
        // Parar transmissão WebSocket
        this.stopWebSocketMediaTransmission();
        
        // Parar monitoramento
        if (this.webrtcHealthCheck) {
            clearInterval(this.webrtcHealthCheck);
            this.webrtcHealthCheck = null;
        }
        
        // Parar streams
        if (this.localStream) {
            this.localStream.getTracks().forEach(track => track.stop());
            this.localStream = null;
        }
        
        // Fechar conexão peer
        if (this.peerConnection) {
            this.peerConnection.close();
            this.peerConnection = null;
        }
        
        // Reset estados
        this.transmissionMode = 'webrtc';
        this.fallbackToWebSocket = false;
        this.webrtcFailureCount = 0;
        
        // Limpar recursos WebSocket
        this.websocketVideoBuffer = [];
        this.websocketAudioBuffer = [];
        this.websocketVideoStream = null;
        if (this.remoteVideoCanvas) {
            this.remoteVideoCanvas = null;
            this.remoteVideoContext = null;
        }
        
        // Enviar sinal de encerramento
        this.sendSignal('bye', {});
        
        // Callbacks
        this.onCallEnded();
    }
    
    // Callbacks (podem ser sobrescritos)
    onRemoteStream(stream) {
        console.log('[WebRTC] Stream remoto disponível');
    }
    
    onVideoToggled(enabled) {
        console.log('[WebRTC] Vídeo:', enabled ? 'ligado' : 'desligado');
    }
    
    onAudioToggled(enabled) {
        console.log('[WebRTC] Áudio:', enabled ? 'ligado' : 'desligado');
    }
    
    onCameraSwitched(isFront) {
        console.log('[WebRTC] Câmera:', isFront ? 'frente' : 'trás');
    }
    
    onMinimizeToggled(minimized) {
        console.log('[WebRTC] Minimizado:', minimized);
    }
    
    onCallEnded() {
        console.log('[WebRTC] Chamada encerrada');
    }
    
    onTransmissionModeChanged(mode) {
        console.log('[WebRTC] Modo de transmissão:', mode);
    }
}

// Exportar para uso global
window.WebRTCManager = WebRTCManager;

