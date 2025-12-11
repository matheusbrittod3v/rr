<?php

namespace App\Policies;

use App\Models\Payment as PaymentModel;
use App\Models\User as UserModel;
use Illuminate\Auth\Access\Response;

class PaymentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(UserModel $user): bool
    {
        return $user->hasPermission('view_payments');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(UserModel $user, PaymentModel $payment): bool
    {
        // Users can view all payments if they have the view_all_data permission
        // or if they have the view_payments permission
        return $user->hasPermission('view_payments') || $user->hasPermission('view_all_data');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(UserModel $user): bool
    {
        return $user->hasPermission('create_payments');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(UserModel $user, PaymentModel $payment): bool
    {
        return $user->hasPermission('edit_payments');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(UserModel $user, PaymentModel $payment): bool
    {
        return $user->hasPermission('delete_payments');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(UserModel $user, PaymentModel $payment): bool
    {
        return $user->hasPermission('delete_payments');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(UserModel $user, PaymentModel $payment): bool
    {
        return $user->hasPermission('delete_payments');
    }
}