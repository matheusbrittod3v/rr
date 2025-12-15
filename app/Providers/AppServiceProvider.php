<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Forçar HTTPS em produção para corrigir mixed content
        // Isso garante que todas as URLs geradas pelo Laravel (asset(), route(), url(), etc.) usem HTTPS
        // 
        // IMPORTANTE: Para funcionar corretamente:
        // 1. Configure APP_URL=https://uniontechnicals.com no .env
        // 2. Limpe o cache: php artisan config:clear && php artisan view:clear && php artisan route:clear
        // 3. Recompile os assets se necessário: npm run build
        
        // Força HTTPS sempre em produção OU quando detectar que está atrás de proxy HTTPS
        $forceHttps = false;
        
        if (env('APP_ENV') === 'production') {
            $forceHttps = true;
        }
        
        
        // 2. Se estiver atrás de proxy reverso com HTTPS (Nginx, Cloudflare, etc)
        try {
            if (request()->hasHeader('X-Forwarded-Proto') && 
                request()->header('X-Forwarded-Proto') === 'https') {
                $forceHttps = true;
            }
        } catch (\Exception $e) {
            // Ignora se request não estiver disponível ainda
        }
        
        // 3. Ou se APP_URL já estiver configurado como HTTPS
        $appUrl = config('app.url', '');
        if (!empty($appUrl) && str_starts_with($appUrl, 'https://')) {
            $forceHttps = true;
        }
        
        if ($forceHttps) {
            URL::forceScheme('https');
        }
    }
}
