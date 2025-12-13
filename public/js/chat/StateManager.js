/**
 * Gerenciador de estado da aplicação
 */
class StateManager {
    constructor(initialMessageId = 0) {
        this.inCall = false;
        this.isCallMinimized = false;
        this.isVideoCall = false;
        this.isMuted = false;
        this.isVideoEnabled = true;
        this.lastMessageId = initialMessageId;
        this.lastSignalId = 0;
        this.localStream = null;
        this.remoteStream = null;
        this.peerConnection = null;
        this.currentCamera = 'user';
        this.selectedFile = null;
        this.callStartTime = null;
        this.callTimerInterval = null;
        this.trackMonitorInterval = null;
        this.processedSignalIds = new Set();
        this.isAcceptingCall = false;
        this.connectionTimeout = null;
        this.answerReceived = false;
        this.pendingCandidates = [];
        this.pendingOffer = null;
        this.pendingOfferSignalId = null;
    }
    
    reset() {
        this.inCall = false;
        this.isCallMinimized = false;
        this.isVideoCall = false;
        this.isVideoEnabled = true;
        this.isMuted = false;
        this.currentCamera = 'user';
        this.selectedFile = null;
        this.isAcceptingCall = false;
        this.answerReceived = false;
        this.pendingCandidates = [];
        this.pendingOffer = null;
        this.pendingOfferSignalId = null;
        
        if (this.callTimerInterval) {
            clearInterval(this.callTimerInterval);
            this.callTimerInterval = null;
        }
        
        if (this.trackMonitorInterval) {
            clearInterval(this.trackMonitorInterval);
            this.trackMonitorInterval = null;
        }
        
        if (this.connectionTimeout) {
            clearTimeout(this.connectionTimeout);
            this.connectionTimeout = null;
        }
        
        if (this.localStream) {
            this.localStream.getTracks().forEach(track => track.stop());
            this.localStream = null;
        }
        
        if (this.peerConnection) {
            this.peerConnection.close();
            this.peerConnection = null;
        }
        
        this.remoteStream = null;
    }
}

