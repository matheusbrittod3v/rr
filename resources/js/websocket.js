import Echo from 'laravel-echo';

// Check if Pusher is available
if (typeof Pusher !== 'undefined') {
    // Initialize Echo with Soketi configuration
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: import.meta.env.VITE_SOKETI_APP_KEY || 'fghjhgfdfgh',
        wsHost: import.meta.env.VITE_SOKETI_HOST || 'data-base-soketi-85305f-31-97-14-4.traefik.me',
        wsPort: import.meta.env.VITE_SOKETI_PORT || 6001,
        wssPort: import.meta.env.VITE_SOKETI_PORT || 6001,
        forceTLS: false,
        encrypted: false,
        disableStats: true,
        enabledTransports: ['ws', 'wss'],
    });

    console.log('WebSocket connection initialized with Soketi');
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