<?php

namespace App\Policies;

use App\Models\GatewayPagamento as GatewayPagamentoModel;
use App\Models\User as UserModel;
use Illuminate\Auth\Access\Response;

class GatewayPagamentoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(UserModel $user): bool
    {
        // Check if user has permission to view gateway pagamentos
        return $user->hasPermission('view_gatewaypagamentos');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(UserModel $user, GatewayPagamentoModel $gatewayPagamento): bool
    {
        // Users can only view their own gateway pagamentos
        return $user->id === $gatewayPagamento->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(UserModel $user): bool
    {
        // Users can only create a gateway pagamento if they don't already have one
        return !GatewayPagamentoModel::userHasConfig($user->id);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(UserModel $user, GatewayPagamentoModel $gatewayPagamento): bool
    {
        // Users can only update their own gateway pagamentos
        return $user->id === $gatewayPagamento->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(UserModel $user, GatewayPagamentoModel $gatewayPagamento): bool
    {
        // Users can only delete their own gateway pagamentos
        return $user->id === $gatewayPagamento->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(UserModel $user, GatewayPagamentoModel $gatewayPagamento): bool
    {
        // Users can only restore their own gateway pagamentos
        return $user->id === $gatewayPagamento->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(UserModel $user, GatewayPagamentoModel $gatewayPagamento): bool
    {
        // Users can only permanently delete their own gateway pagamentos
        return $user->id === $gatewayPagamento->user_id;
    }
}