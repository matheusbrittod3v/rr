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
        // Verifica:
        // 1. Se está em produção
        // 2. Se está atrás de proxy reverso com X-Forwarded-Proto: https
        if ($this->app->environment('production') || 
            request()->header('X-Forwarded-Proto') === 'https' ||
            request()->server('HTTP_X_FORWARDED_PROTO') === 'https') {
            URL::forceScheme('https');
        }
    }
}
