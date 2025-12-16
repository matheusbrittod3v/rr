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
let reverbScheme = import.meta.env.VITE_REVERB_SCHEME ?? 'http';

// Converter porta para número
// Se a porta for 443, assumir HTTPS automaticamente
let finalPort = parseInt(reverbPort, 10) || 8080;

// Se porta for 443 mas scheme for http, corrigir para https
if (finalPort === 443 && reverbScheme === 'http') {
    reverbScheme = 'https';
    console.warn('[Echo] ⚠️ Porta 443 detectada com scheme http. Corrigindo para https.');
}

// Em produção com HTTPS (porta 443), não especificamos porta (usa padrão)
const isProductionHTTPS = reverbScheme === 'https' && finalPort === 443;
const port = isProductionHTTPS ? null : finalPort;
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
            // Usar authorizer customizado para garantir que cookies sejam enviados
            authorizer: null, // Não é necessário para canais públicos
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

        // Adicionar listeners de conexão para debug
        if (window.Echo.connector && window.Echo.connector.pusher) {
            const pusher = window.Echo.connector.pusher;
            
            pusher.connection.bind('connected', () => {
                console.log('[Echo] ✅ WebSocket CONECTADO com sucesso!');
            });
            
            pusher.connection.bind('disconnected', () => {
                console.warn('[Echo] ⚠️ WebSocket DESCONECTADO');
            });
            
            pusher.connection.bind('error', (error) => {
                console.error('[Echo] ❌ Erro na conexão WebSocket:', error);
            });
            
            pusher.connection.bind('state_change', (states) => {
                console.log('[Echo] Estado da conexão mudou:', states.previous, '->', states.current);
            });
        }
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
        window.Echo.channel(`payments.${paymentId}`)
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
        // Listen to chat room messages on public channel
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