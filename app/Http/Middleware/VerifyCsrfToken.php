<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Log;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Broadcasting auth já é excluído automaticamente pelo Laravel
    ];

    /**
     * Determine if the session and input CSRF tokens match.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function tokensMatch($request)
    {
        $token = $this->getTokenFromRequest($request);
        $sessionToken = $request->session()->token();

        // Log para debug
        Log::debug('CSRF Token Verification', [
            'path' => $request->path(),
            'method' => $request->method(),
            'has_session_token' => !empty($sessionToken),
            'has_request_token' => !empty($token),
            'session_id' => $request->session()->getId(),
        ]);

        $match = is_string($sessionToken) &&
                 is_string($token) &&
                 hash_equals($sessionToken, $token);

        if (!$match) {
            Log::warning('CSRF Token Mismatch', [
                'path' => $request->path(),
                'session_token_length' => strlen($sessionToken ?? ''),
                'request_token_length' => strlen($token ?? ''),
                'session_id' => $request->session()->getId(),
            ]);
        }

        return $match;
    }
}

