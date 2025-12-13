/**
 * Gerenciador de streams de mídia
 */
class MediaManager {
    constructor(stateManager, config, uiManager) {
        this.state = stateManager;
        this.config = config;
        this.ui = uiManager;
    }
    
    async getLocalStream(video = true) {
        // Try to get available devices first
        let availableDevices = { audio: false, video: false };
        
        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            availableDevices.audio = devices.some(d => d.kind === 'audioinput');
            availableDevices.video = devices.some(d => d.kind === 'videoinput');
            console.log('Available devices:', availableDevices);
        } catch (err) {
            console.warn('Could not enumerate devices, will try anyway:', err);
            availableDevices.audio = true;
            availableDevices.video = video;
        }
        
        // Build constraints
        const audioConstraints = availableDevices.audio ? {
            echoCancellation: true,
            noiseSuppression: true,
            autoGainControl: true,
            ...(this.config.isMobile && {
                sampleRate: 48000,
                channelCount: 1
            })
        } : false;
        
        const videoConstraints = (video && availableDevices.video) ? (this.config.isMobile ? {
            facingMode: this.state.currentCamera,
            width: { ideal: 640, max: 1280 },
            height: { ideal: 480, max: 720 },
            frameRate: { ideal: 30, max: 30 },
            aspectRatio: { ideal: 16/9 }
        } : {
            facingMode: this.state.currentCamera,
            width: { ideal: 1280 },
            height: { ideal: 720 }
        }) : false;
        
        let constraints = {
            audio: audioConstraints,
            video: videoConstraints
        };
        
        console.log('Requesting media with constraints:', constraints);
        
        let stream = null;
        let audioError = null;
        let videoError = null;
        
        try {
            // First try: get both if requested
            if (audioConstraints && videoConstraints) {
                try {
                    stream = await navigator.mediaDevices.getUserMedia(constraints);
                    console.log('Successfully obtained both audio and video');
                } catch (error) {
                    console.warn('Failed to get both, will try separately:', error);
                    if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
                        audioError = error;
                        videoError = error;
                    } else if (error.name === 'NotFoundError') {
                        if (error.message && error.message.includes('video')) {
                            videoError = error;
                        } else if (error.message && error.message.includes('audio')) {
                            audioError = error;
                        } else {
                            audioError = error;
                            videoError = error;
                        }
                    } else {
                        audioError = error;
                        videoError = error;
                    }
                }
            }
            
            // If we don't have a stream yet, try to get what we can
            if (!stream) {
                // Try audio only
                if (audioConstraints && !stream?.getAudioTracks().length) {
                    try {
                        const audioStream = await navigator.mediaDevices.getUserMedia({ audio: audioConstraints, video: false });
                        if (!stream) {
                            stream = audioStream;
                            console.log('Obtained audio only');
                        } else {
                            audioStream.getAudioTracks().forEach(track => {
                                stream.addTrack(track);
                            });
                            audioStream.getTracks().forEach(track => {
                                if (!stream.getTracks().includes(track)) {
                                    track.stop();
                                }
                            });
                            console.log('Added audio tracks to existing stream');
                        }
                    } catch (error) {
                        console.warn('Failed to get audio:', error);
                        audioError = error;
                    }
                }
                
                // Try video only
                if (videoConstraints && !stream?.getVideoTracks().length) {
                    try {
                        if (this.config.isMobile) {
                            try {
                                const videoStream = await navigator.mediaDevices.getUserMedia({ 
                                    audio: false, 
                                    video: videoConstraints 
                                });
                                if (!stream) {
                                    stream = videoStream;
                                    console.log('Obtained video only');
                                } else {
                                    videoStream.getVideoTracks().forEach(track => {
                                        stream.addTrack(track);
                                    });
                                    videoStream.getTracks().forEach(track => {
                                        if (!stream.getTracks().includes(track)) {
                                            track.stop();
                                        }
                                    });
                                    console.log('Added video tracks to existing stream');
                                }
                            } catch (error) {
                                console.warn('Video with constraints failed, trying simpler:', error);
                                const videoStream = await navigator.mediaDevices.getUserMedia({ 
                                    audio: false, 
                                    video: true 
                                });
                                if (!stream) {
                                    stream = videoStream;
                                    console.log('Obtained video only (simple)');
                                } else {
                                    videoStream.getVideoTracks().forEach(track => {
                                        stream.addTrack(track);
                                    });
                                    videoStream.getTracks().forEach(track => {
                                        if (!stream.getTracks().includes(track)) {
                                            track.stop();
                                        }
                                    });
                                    console.log('Added video tracks to existing stream (simple)');
                                }
                            }
                        } else {
                            const videoStream = await navigator.mediaDevices.getUserMedia({ 
                                audio: false, 
                                video: videoConstraints 
                            });
                            if (!stream) {
                                stream = videoStream;
                                console.log('Obtained video only');
                            } else {
                                videoStream.getVideoTracks().forEach(track => {
                                    stream.addTrack(track);
                                });
                                videoStream.getTracks().forEach(track => {
                                    if (!stream.getTracks().includes(track)) {
                                        track.stop();
                                    }
                                });
                                console.log('Added video tracks to existing stream');
                            }
                        }
                    } catch (error) {
                        console.warn('Failed to get video:', error);
                        videoError = error;
                    }
                }
            }
            
            // If we still don't have a stream, we failed completely
            if (!stream || (stream.getAudioTracks().length === 0 && stream.getVideoTracks().length === 0)) {
                throw new Error('Não foi possível obter nenhum dispositivo de mídia (câmera ou microfone)');
            }
            
            this.state.localStream = stream;
            if (this.ui.elements.localVideo) {
                this.ui.elements.localVideo.srcObject = stream;
            }
            
            this.state.isVideoEnabled = stream.getVideoTracks().length > 0;
            
            const videoTracks = stream.getVideoTracks();
            const audioTracks = stream.getAudioTracks();
            console.log('Stream obtained:', {
                videoTracks: videoTracks.length,
                audioTracks: audioTracks.length,
                hasVideo: videoTracks.length > 0,
                hasAudio: audioTracks.length > 0,
                videoSettings: videoTracks[0]?.getSettings(),
                audioSettings: audioTracks[0]?.getSettings()
            });
            
            if (video && videoTracks.length === 0) {
                console.warn('Video was requested but not available - continuing with audio only');
            }
            if (audioTracks.length === 0) {
                console.warn('Audio was requested but not available - continuing with video only');
            }
            
            return stream;
        } catch (error) {
            console.error('Error accessing media devices:', error);
            
            const hasAudio = this.state.localStream?.getAudioTracks().length > 0;
            const hasVideo = this.state.localStream?.getVideoTracks().length > 0;
            
            let errorMessage = '';
            
            if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
                if (!hasAudio && !hasVideo) {
                    errorMessage = 'Permissão negada para câmera e microfone.\n\n';
                    errorMessage += 'Para aceitar a chamada, você precisa permitir o acesso:\n';
                    errorMessage += '1. Clique no ícone de bloqueio/câmera na barra de endereço\n';
                    errorMessage += '2. Permita o acesso à câmera e/ou microfone\n';
                    errorMessage += '3. Tente aceitar a chamada novamente';
                } else {
                    errorMessage = 'Permissão negada para ' + (!hasAudio ? 'microfone' : 'câmera') + '.\n';
                    errorMessage += 'A chamada continuará com ' + (hasAudio ? 'áudio' : 'vídeo') + ' apenas.';
                }
            } else if (error.name === 'NotFoundError' || error.name === 'DevicesNotFoundError') {
                if (!hasAudio && !hasVideo) {
                    errorMessage = 'Nenhum dispositivo de mídia encontrado.\n';
                    errorMessage += 'Certifique-se de que seu dispositivo possui câmera ou microfone conectados.';
                } else {
                    errorMessage = 'Dispositivo ' + (!hasAudio ? 'de microfone' : 'de câmera') + ' não encontrado.\n';
                    errorMessage += 'A chamada continuará com ' + (hasAudio ? 'áudio' : 'vídeo') + ' apenas.';
                }
            } else if (error.name === 'NotReadableError' || error.name === 'TrackStartError') {
                if (!hasAudio && !hasVideo) {
                    errorMessage = 'Câmera e microfone já estão em uso por outro aplicativo.\n';
                    errorMessage += 'Feche outros aplicativos e tente novamente.';
                } else {
                    errorMessage = (!hasAudio ? 'Microfone' : 'Câmera') + ' já está em uso.\n';
                    errorMessage += 'A chamada continuará com ' + (hasAudio ? 'áudio' : 'vídeo') + ' apenas.';
                }
            } else {
                if (!hasAudio && !hasVideo) {
                    errorMessage = 'Erro ao acessar dispositivos de mídia: ' + (error.message || error.name || 'Erro desconhecido');
                } else {
                    console.warn('Partial success - continuing with available devices');
                    return this.state.localStream;
                }
            }
            
            if (!hasAudio && !hasVideo && errorMessage) {
                alert(errorMessage);
                throw error;
            } else if (errorMessage) {
                console.warn(errorMessage);
                return this.state.localStream;
            }
            
            if (this.state.localStream && (this.state.localStream.getAudioTracks().length > 0 || this.state.localStream.getVideoTracks().length > 0)) {
                return this.state.localStream;
            }
            
            throw error;
        }
    }
}

