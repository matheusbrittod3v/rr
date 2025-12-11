import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    
    return {
        define: {
            'import.meta.env.VITE_SOKETI_APP_KEY': JSON.stringify(env.SOKETI_APP_KEY || 'fghjhgfdfgh'),
            'import.meta.env.VITE_SOKETI_HOST': JSON.stringify(env.SOKETI_HOST || 'data-base-soketi-85305f-31-97-14-4.traefik.me'),
            'import.meta.env.VITE_SOKETI_PORT': JSON.stringify(env.SOKETI_PORT || 6001),
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