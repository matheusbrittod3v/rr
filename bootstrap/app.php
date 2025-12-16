<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\VerifyMercadoPagoNotification;
use App\Http\Middleware\ChatSessionMiddleware;
use Illuminate\Http\Middleware\HandleCors;



return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        
        // Registrar o middleware CORS
        $middleware->prepend(HandleCors::class);
        $middleware->alias([
            'verify.mercadopago' => VerifyMercadoPagoNotification::class,
            'chat.session' => ChatSessionMiddleware::class,
        ]);
        
        // Remover middleware CSRF do grupo web (desativado)
        $middleware->web(remove: [
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
        ]);
        
        // Trust proxies para funcionar atrás de Nginx/Cloudflare/etc
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();