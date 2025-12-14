import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Make Pusher available globally
window.Pusher = Pusher;

// Initialize Echo with Reverb
if (typeof Pusher !== 'undefined') {
    // Initialize Echo with Reverb configuration
    const reverbKey = import.meta.env.VITE_REVERB_APP_KEY || '';
    const reverbHost = import.meta.env.VITE_REVERB_HOST || '';
    const reverbPort = import.meta.env.VITE_REVERB_PORT || 443;
    const reverbScheme = import.meta.env.VITE_REVERB_SCHEME || 'https';
    
    if (reverbKey && reverbHost) {
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: reverbKey,
            wsHost: reverbHost,
            wsPort: reverbScheme === 'https' ? 443 : 80,
            wssPort: parseInt(reverbPort, 10),
            forceTLS: reverbScheme === 'https',
            enabledTransports: ['ws', 'wss'],
        });

        console.log('WebSocket connection initialized with Reverb');
    } else {
        console.warn('Reverb credentials not configured. WebSocket connection not initialized.');
    }
} else {
    console.warn('Pusher not available, WebSocket connection not initialized');
}

// Export functions for handling WebSocket events
export function listenToPaymentUpdates(paymentId, callback) {
    if (window.Echo) {
        // Listen to general payment updates
        window.Echo.channel('payments')
            .listen('PaymentStatusUpdated', (event) => {
                console.log('Payment status updated:', event);
                if (callback && typeof callback === 'function') {
                    callback(event);
                }
            });

        // Listen to specific payment updates
        window.Echo.private(`payments.${paymentId}`)
            .listen('PaymentStatusUpdated', (event) => {
                console.log('Specific payment status updated:', event);
                if (callback && typeof callback === 'function') {
                    callback(event);
                }
            });
    }
}

export function listenToChatMessages(roomId, callback) {
    if (window.Echo) {
        // Listen to chat room messages
        window.Echo.channel(`chat-room.${roomId}`)
            .listen('ChatMessageSent', (event) => {
                console.log('New chat message:', event);
                if (callback && typeof callback === 'function') {
                    callback(event);
                }
            });
    }
}

export function disconnect() {
    if (window.Echo) {
        window.Echo.disconnect();
        console.log('WebSocket connection disconnected');
    }
}