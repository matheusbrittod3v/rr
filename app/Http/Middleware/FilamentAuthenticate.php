<?php

namespace App\Http\Middleware;

use Filament\Facades\Filament;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Database\Eloquent\Model;

class FilamentAuthenticate extends Middleware
{
    /**
     * @param  array<string>  $guards
     */
    protected function authenticate($request, array $guards): void
    {
        $guard = Filament::auth();

        if (! $guard->check()) {
            $this->unauthenticated($request, $guards);
            return;
        }

        $this->auth->shouldUse(Filament::getAuthGuard());

        /** @var Model $user */
        $user = $guard->user();

        $panel = Filament::getCurrentOrDefaultPanel();

        // Verificar se o usuário pode acessar o painel
        // Baseado em roles ou permissões, sem precisar implementar FilamentUser
        $canAccess = $this->canAccessPanel($user, $panel);

        abort_if(!$canAccess, 403, 'Acesso negado ao painel administrativo.');
    }

    /**
     * Verifica se o usuário pode acessar o painel
     */
    protected function canAccessPanel($user, $panel): bool
    {
        // Se o usuário implementa FilamentUser, usa o método padrão
        if ($user instanceof \Filament\Models\Contracts\FilamentUser) {
            return $user->canAccessPanel($panel);
        }

        // Caso contrário, verifica roles e permissões
        // Permitir acesso se tem role admin ou manager
        if (method_exists($user, 'hasRole')) {
            if ($user->hasRole('admin') || $user->hasRole('manager')) {
                return true;
            }
        }

        // Ou se tem a permissão view_dashboard
        if (method_exists($user, 'hasPermission')) {
            if ($user->hasPermission('view_dashboard')) {
                return true;
            }
        }

        // Em ambiente local, permitir acesso (compatibilidade)
        if (config('app.env') === 'local') {
            return true;
        }

        return false;
    }

    protected function redirectTo($request): ?string
    {
        return Filament::getLoginUrl();
    }
}

