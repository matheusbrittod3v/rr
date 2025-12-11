<?php

namespace App\Policies;

use App\Models\Role as RoleModel;
use App\Models\User as UserModel;
use Illuminate\Auth\Access\Response;

class RolePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(UserModel $user): bool
    {
        return $user->hasPermission('manage_roles');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(UserModel $user, RoleModel $role): bool
    {
        return $user->hasPermission('manage_roles');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(UserModel $user): bool
    {
        return $user->hasPermission('manage_roles');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(UserModel $user, RoleModel $role): bool
    {
        return $user->hasPermission('manage_roles');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(UserModel $user, RoleModel $role): bool
    {
        return $user->hasPermission('manage_roles');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(UserModel $user, RoleModel $role): bool
    {
        return $user->hasPermission('manage_roles');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(UserModel $user, RoleModel $role): bool
    {
        return $user->hasPermission('manage_roles');
    }
}