<?php

namespace App\Providers;

use App\Filament\Pages\CustomDashboard;
use App\Models\EmailConfig;
use App\Models\GatewayPagamento;
use App\Models\Marca;
use App\Models\Payment;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use App\Models\WhatsApi;
use App\Policies\DashboardPolicy;
use App\Policies\EmailConfigPolicy;
use App\Policies\GatewayPagamentoPolicy;
use App\Policies\MarcaPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use App\Policies\ServicePolicy;
use App\Policies\UserPolicy;
use App\Policies\WhatsApiPolicy;
use App\Models\ChatRoom;
use App\Policies\ChatRoomPolicy;
use App\Models\Orcamento;
use App\Policies\OrcamentoPolicy;
use App\Models\FilaOrcamento;
use App\Policies\FilaOrcamentoPolicy;
use App\Models\FilaPrestador;
use App\Policies\FilaPrestadorPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        Permission::class => PermissionPolicy::class,
        Service::class => ServicePolicy::class,
        Marca::class => MarcaPolicy::class,
        Payment::class => PaymentPolicy::class,
        EmailConfig::class => EmailConfigPolicy::class,
        GatewayPagamento::class => GatewayPagamentoPolicy::class,
        WhatsApi::class => WhatsApiPolicy::class,
        CustomDashboard::class => DashboardPolicy::class,
        ChatRoom::class => ChatRoomPolicy::class,
        Orcamento::class => OrcamentoPolicy::class,
        FilaOrcamento::class => FilaOrcamentoPolicy::class,
        FilaPrestador::class => FilaPrestadorPolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}