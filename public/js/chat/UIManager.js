/**
 * Gerenciador de elementos da interface
 */
class UIManager {
    constructor() {
        this.elements = {
            chatForm: document.getElementById('chat-form'),
            messageInput: document.getElementById('message-input'),
            fileInput: document.getElementById('file-input'),
            attachButton: document.getElementById('attach-button'),
            sendButton: document.getElementById('send-button'),
            chatMessages: document.getElementById('chat-messages'),
            filePreview: document.getElementById('file-preview'),
            fileName: document.getElementById('file-name'),
            removeFileBtn: document.getElementById('remove-file'),
            charCount: document.getElementById('char-count'),
            voiceCallBtn: document.getElementById('voice-call-btn'),
            videoCallBtn: document.getElementById('video-call-btn'),
            callOverlay: document.getElementById('call-overlay'),
            incomingCallModal: document.getElementById('incoming-call-modal'),
            acceptCallBtn: document.getElementById('accept-call-btn'),
            rejectCallBtn: document.getElementById('reject-call-btn'),
            remoteVideo: document.getElementById('remote-video'),
            localVideo: document.getElementById('local-video'),
            minimizedRemoteVideo: document.getElementById('minimized-remote-video'),
            minimizedCallWidget: document.getElementById('minimized-call-widget'),
            callStatus: document.getElementById('call-status'),
            toggleMicBtn: document.getElementById('toggle-mic-btn'),
            toggleVideoBtn: document.getElementById('toggle-video-btn'),
            switchCameraBtn: document.getElementById('switch-camera-btn'),
            switchToVoiceBtn: document.getElementById('switch-to-voice-btn'),
            minimizeCallBtn: document.getElementById('minimize-call-btn'),
            hangupBtn: document.getElementById('hangup-btn'),
            micOnIcon: document.getElementById('mic-on-icon'),
            micOffIcon: document.getElementById('mic-off-icon'),
            videoOnIcon: document.getElementById('video-on-icon'),
            videoOffIcon: document.getElementById('video-off-icon'),
            callTimer: document.getElementById('call-timer'),
            timerDisplay: document.getElementById('timer-display')
        };
    }
    
    scrollToBottom() {
        if (this.elements.chatMessages) {
            this.elements.chatMessages.scrollTop = this.elements.chatMessages.scrollHeight;
        }
    }
    
    updateCallStatus(text) {
        if (this.elements.callStatus) {
            const statusDiv = this.elements.callStatus.querySelector('div');
            if (statusDiv) {
                statusDiv.textContent = text;
            }
            this.elements.callStatus.classList.remove('hidden');
        }
    }
    
    hideCallStatus() {
        if (this.elements.callStatus) {
            this.elements.callStatus.classList.add('hidden');
        }
    }
    
    showCallOverlay() {
        if (this.elements.callOverlay) {
            this.elements.callOverlay.classList.remove('hidden');
        }
        if (this.elements.incomingCallModal) {
            this.elements.incomingCallModal.classList.add('hidden');
        }
    }
    
    hideCallOverlay() {
        if (this.elements.callOverlay) {
            this.elements.callOverlay.classList.add('hidden');
        }
    }
    
    showIncomingCallModal() {
        if (this.elements.incomingCallModal) {
            this.elements.incomingCallModal.classList.remove('hidden');
        }
    }
    
    hideIncomingCallModal() {
        if (this.elements.incomingCallModal) {
            this.elements.incomingCallModal.classList.add('hidden');
        }
    }
    
    updateIncomingCallModalText(text) {
        if (this.elements.incomingCallModal) {
            const modalContent = this.elements.incomingCallModal.querySelector('p');
            if (modalContent) {
                modalContent.textContent = text;
            }
        }
    }
    
    showMinimizedCall() {
        if (this.elements.minimizedCallWidget) {
            this.elements.minimizedCallWidget.classList.remove('hidden');
        }
    }
    
    hideMinimizedCall() {
        if (this.elements.minimizedCallWidget) {
            this.elements.minimizedCallWidget.classList.add('hidden');
        }
    }
    
    showCallTimer() {
        if (this.elements.callTimer) {
            this.elements.callTimer.classList.remove('hidden');
        }
    }
    
    hideCallTimer() {
        if (this.elements.callTimer) {
            this.elements.callTimer.classList.add('hidden');
        }
    }
    
    updateTimerDisplay(time) {
        if (this.elements.timerDisplay) {
            this.elements.timerDisplay.textContent = time;
        }
    }
    
    showMicOn() {
        if (this.elements.micOnIcon) {
            this.elements.micOnIcon.classList.remove('hidden');
        }
        if (this.elements.micOffIcon) {
            this.elements.micOffIcon.classList.add('hidden');
        }
        if (this.elements.toggleMicBtn) {
            this.elements.toggleMicBtn.classList.remove('bg-red-500/50');
        }
    }
    
    showMicOff() {
        if (this.elements.micOnIcon) {
            this.elements.micOnIcon.classList.add('hidden');
        }
        if (this.elements.micOffIcon) {
            this.elements.micOffIcon.classList.remove('hidden');
        }
        if (this.elements.toggleMicBtn) {
            this.elements.toggleMicBtn.classList.add('bg-red-500/50');
        }
    }
    
    showVideoOn() {
        if (this.elements.videoOnIcon) {
            this.elements.videoOnIcon.classList.remove('hidden');
        }
        if (this.elements.videoOffIcon) {
            this.elements.videoOffIcon.classList.add('hidden');
        }
        if (this.elements.localVideo) {
            this.elements.localVideo.style.display = 'block';
        }
    }
    
    showVideoOff() {
        if (this.elements.videoOnIcon) {
            this.elements.videoOnIcon.classList.add('hidden');
        }
        if (this.elements.videoOffIcon) {
            this.elements.videoOffIcon.classList.remove('hidden');
        }
        if (this.elements.localVideo) {
            this.elements.localVideo.style.display = 'none';
        }
    }
    
    showSwitchToVoiceBtn() {
        if (this.elements.switchToVoiceBtn) {
            this.elements.switchToVoiceBtn.classList.remove('hidden');
        }
    }
    
    hideSwitchToVoiceBtn() {
        if (this.elements.switchToVoiceBtn) {
            this.elements.switchToVoiceBtn.classList.add('hidden');
        }
    }
    
    showSwitchCameraBtn() {
        if (this.elements.switchCameraBtn) {
            this.elements.switchCameraBtn.classList.remove('hidden');
        }
    }
    
    hideSwitchCameraBtn() {
        if (this.elements.switchCameraBtn) {
            this.elements.switchCameraBtn.classList.add('hidden');
        }
    }
    
    showMinimizedCallWidget() {
        if (this.elements.minimizedCallWidget) {
            this.elements.minimizedCallWidget.classList.remove('hidden');
        }
    }
    
    hideMinimizedCallWidget() {
        if (this.elements.minimizedCallWidget) {
            this.elements.minimizedCallWidget.classList.add('hidden');
        }
    }
}

