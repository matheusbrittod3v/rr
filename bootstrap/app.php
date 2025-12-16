<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\VerifyMercadoPagoNotification;
use App\Http\Middleware\ChatSessionMiddleware;
use App\Http\Middleware\VerifyCsrfToken;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'verify.mercadopago' => VerifyMercadoPagoNotification::class,
            'chat.session' => ChatSessionMiddleware::class,
        ]);
        
        // Substituir o middleware CSRF padrão pelo nosso customizado no grupo web
        $middleware->web(replace: [
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class => VerifyCsrfToken::class,
        ]);
        
        // Trust proxies para funcionar atrás de Nginx/Cloudflare/etc
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();