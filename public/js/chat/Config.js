/**
 * Configurações da aplicação de chat
 */
class Config {
    constructor(roomCode, csrfToken, routes) {
        this.roomCode = roomCode;
        this.csrfToken = csrfToken;
        this.routes = routes;
        this.sendMessageUrl = routes.sendMessageUrl;
        this.getMessagesUrl = routes.getMessagesUrl;
        this.signalsUrl = routes.signalsUrl;
        this.sendSignalUrl = routes.sendSignalUrl;
        
        // Detectar dispositivo móvel
        this.isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || 
                       (navigator.maxTouchPoints && navigator.maxTouchPoints > 2 && /MacIntel/.test(navigator.platform));
        
        console.log('Device type:', this.isMobile ? 'Mobile' : 'Desktop');
        
        // Configuração WebRTC
        this.rtcConfig = {
            iceServers: [
                // STUN servers (for discovery)
                { urls: 'stun:stun.l.google.com:19302' },
                { urls: 'stun:stun1.l.google.com:19302' },
                { urls: 'stun:stun2.l.google.com:19302' },
                { urls: 'stun:stun.stunprotocol.org:3478' },
                // Public TURN servers (for relay when direct connection fails)
                { 
                    urls: 'turn:openrelay.metered.ca:80',
                    username: 'openrelayproject',
                    credential: 'openrelayproject'
                },
                { 
                    urls: 'turn:openrelay.metered.ca:443',
                    username: 'openrelayproject',
                    credential: 'openrelayproject'
                },
                { 
                    urls: 'turn:openrelay.metered.ca:443?transport=tcp',
                    username: 'openrelayproject',
                    credential: 'openrelayproject'
                }
            ],
            iceCandidatePoolSize: this.isMobile ? 20 : 10,
            iceTransportPolicy: 'all',
            bundlePolicy: 'max-bundle',
            rtcpMuxPolicy: 'require'
        };
        
        // Intervalos de polling
        this.messagePollInterval = 3000;
        this.signalPollInterval = this.isMobile ? 1500 : 2000;
    }
}

