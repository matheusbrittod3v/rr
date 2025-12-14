import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    
    // Configuração do Reverb baseada no test-reverb.php que funciona
    // Mesmas credenciais do teste: REVERB_APP_KEY, REVERB_HOST, REVERB_PORT=443, REVERB_SCHEME=http
    const REVERB_APP_KEY = env.VITE_REVERB_APP_KEY || env.REVERB_APP_KEY || 'sdfghjkhgfdfghjkhgfghjk';
    const REVERB_HOST = env.VITE_REVERB_HOST || env.REVERB_HOST || 'aplication-pdr-laravel-aplication-dqvnri-fc8b57-31-97-14-4.traefik.me';
    const REVERB_PORT = env.VITE_REVERB_PORT || env.REVERB_PORT || '443';
    const REVERB_SCHEME = env.VITE_REVERB_SCHEME || env.REVERB_SCHEME || 'http';
    
    return {
        define: {
            'import.meta.env.VITE_REVERB_APP_KEY': JSON.stringify(REVERB_APP_KEY),
            'import.meta.env.VITE_REVERB_HOST': JSON.stringify(REVERB_HOST),
            'import.meta.env.VITE_REVERB_PORT': JSON.stringify(REVERB_PORT),
            'import.meta.env.VITE_REVERB_SCHEME': JSON.stringify(REVERB_SCHEME),
        },
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
            tailwindcss(),
        ],
    };
});