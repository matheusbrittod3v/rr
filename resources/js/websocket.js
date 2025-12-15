import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Make Pusher available globally
window.Pusher = Pusher;

/**
 * Configuração do Laravel Echo com Reverb
 * 
 * Configurado para localhost:
 * - REVERB_APP_KEY: sdfghjkhgfdfghjkhgfghjk
 * - REVERB_HOST: localhost
 * - REVERB_PORT: 8080
 * - REVERB_SCHEME: http
 */

// Obter credenciais do ambiente ou usar valores padrão para localhost
const reverbKey = import.meta.env.VITE_REVERB_APP_KEY || 'sdfghjkhgfdfghjkhgfghjk';
const reverbHost = import.meta.env.VITE_REVERB_HOST || 'localhost';
const reverbPort = import.meta.env.VITE_REVERB_PORT ?? '8080';
const reverbScheme = import.meta.env.VITE_REVERB_SCHEME ?? 'http';

// Converter porta para número
// Em produção com HTTPS (porta 443), não especificamos porta (usa padrão)
const isProductionHTTPS = reverbScheme === 'https' && (reverbPort === '443' || reverbPort === '');
const port = isProductionHTTPS ? null : (parseInt(reverbPort, 10) || 8080);
const useTLS = reverbScheme === 'https';

console.log('[Echo] Configurando Laravel Echo com Reverb:', {
    key: reverbKey ? reverbKey.substring(0, 10) + '...' : 'não configurado',
    host: reverbHost,
    port: port,
    scheme: reverbScheme,
    useTLS: useTLS
});

// Inicializar Echo apenas se tivermos as credenciais necessárias
// IMPORTANTE: Echo SEMPRE usa broadcaster: 'pusher', mesmo conectando ao Reverb
// Reverb implementa o protocolo Pusher, então o Echo fala protocolo Pusher
if (reverbKey && reverbHost) {
    try {
        // Configuração base do Echo
        const echoConfig = {
            broadcaster: 'pusher', // ✅ CORRETO: sempre 'pusher', mesmo com Reverb
            key: reverbKey,
            wsHost: reverbHost,
            forceTLS: useTLS,
            enabledTransports: ['ws', 'wss'],
            disableStats: true, // Desabilita estatísticas do Pusher (não necessário para Reverb)
            cluster: 'mt1', // Cluster padrão (ignorado quando wsHost é customizado, mas necessário para Pusher.js)
        };

        // Em produção HTTPS (porta 443), não especificamos porta (usa padrão 443)
        // Em desenvolvimento ou porta customizada, especificamos a porta
        if (!isProductionHTTPS) {
            echoConfig.wsPort = port;
            echoConfig.wssPort = port;
        }

        window.Echo = new Echo(echoConfig);

        console.log('[Echo] ✅ Laravel Echo inicializado com sucesso!');
        console.log('[Echo] Broadcaster: pusher (conectando ao Reverb)');
        console.log('[Echo] Conectando em:', isProductionHTTPS 
            ? `${reverbScheme}://${reverbHost}` 
            : `${reverbScheme}://${reverbHost}:${port}`);
    } catch (error) {
        console.error('[Echo] ❌ Erro ao inicializar Echo:', error);
        console.error('[Echo] Detalhes do erro:', error.message, error.stack);
    }
} else {
    console.error('[Echo] ❌ Credenciais Reverb não configuradas:', {
        hasKey: !!reverbKey,
        hasHost: !!reverbHost
    });
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