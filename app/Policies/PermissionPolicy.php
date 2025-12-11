<?php

namespace App\Policies;

use App\Models\Permission as PermissionModel;
use App\Models\User as UserModel;
use Illuminate\Auth\Access\Response;

class PermissionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(UserModel $user): bool
    {
        return $user->hasPermission('manage_permissions');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(UserModel $user, PermissionModel $permission): bool
    {
        return $user->hasPermission('manage_permissions');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(UserModel $user): bool
    {
        return $user->hasPermission('manage_permissions');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(UserModel $user, PermissionModel $permission): bool
    {
        return $user->hasPermission('manage_permissions');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(UserModel $user, PermissionModel $permission): bool
    {
        return $user->hasPermission('manage_permissions');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(UserModel $user, PermissionModel $permission): bool
    {
        return $user->hasPermission('manage_permissions');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(UserModel $user, PermissionModel $permission): bool
    {
        return $user->hasPermission('manage_permissions');
    }
}