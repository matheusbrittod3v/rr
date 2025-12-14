import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Make Pusher available globally
window.Pusher = Pusher;

// Initialize Echo with Reverb
if (typeof Pusher !== 'undefined') {
    // Initialize Echo with Reverb configuration usando variáveis do .env
    const reverbKey = import.meta.env.VITE_REVERB_APP_KEY || '';
    const reverbHost = import.meta.env.VITE_REVERB_HOST || '';
    const reverbPort = import.meta.env.VITE_REVERB_PORT || '445';
    const reverbScheme = import.meta.env.VITE_REVERB_SCHEME || 'http';
    
    console.log('[DEBUG] Configurando Reverb com credenciais:', {
        key: reverbKey ? reverbKey.substring(0, 10) + '...' : 'não configurado',
        host: reverbHost,
        port: reverbPort,
        scheme: reverbScheme
    });
    
    if (reverbKey && reverbHost) {
        const port = parseInt(reverbPort, 10) || 445;
        const useTLS = reverbScheme === 'https';
        
        // Configuração do Echo com Reverb
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: reverbKey,
            wsHost: reverbHost,
            wsPort: port, // Porta para WebSocket (ws://)
            wssPort: port, // Porta para WebSocket Secure (wss://)
            forceTLS: useTLS,
            enabledTransports: ['ws', 'wss'],
        });

        console.log('[DEBUG] ✅ Echo inicializado com Reverb:', {
            broadcaster: 'reverb',
            key: reverbKey.substring(0, 10) + '...',
            wsHost: reverbHost,
            wsPort: port,
            wssPort: port,
            forceTLS: useTLS,
            scheme: reverbScheme,
            enabledTransports: ['ws', 'wss']
        });
        console.log('[DEBUG] Conexão WebSocket configurada e pronta para:', reverbHost + ':' + port);
    } else {
        console.error('[DEBUG] ❌ Credenciais Reverb não configuradas:', {
            hasKey: !!reverbKey,
            hasHost: !!reverbHost
        });
        console.warn('Reverb credentials not configured. WebSocket connection not initialized.');
    }
} else {
    console.error('[DEBUG] ❌ Pusher não disponível');
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