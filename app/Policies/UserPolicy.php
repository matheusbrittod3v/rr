<?php

namespace App\Policies;

use App\Models\User as UserModel;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(UserModel $user): bool
    {
        return $user->hasPermission('view_users');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(UserModel $user, UserModel $model): bool
    {
        // Users can view their own profile or if they have the view_users permission
        return $user->id === $model->id || $user->hasPermission('view_users');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(UserModel $user): bool
    {
        return $user->hasPermission('create_users');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(UserModel $user, UserModel $model): bool
    {
        // Users can update their own profile or if they have the edit_users permission
        return $user->id === $model->id || $user->hasPermission('edit_users');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(UserModel $user, UserModel $model): bool
    {
        return $user->hasPermission('delete_users');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(UserModel $user, UserModel $model): bool
    {
        return $user->hasPermission('delete_users');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(UserModel $user, UserModel $model): bool
    {
        return $user->hasPermission('delete_users');
    }
}