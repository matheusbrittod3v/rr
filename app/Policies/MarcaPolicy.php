<?php

namespace App\Policies;

use App\Models\Marca as MarcaModel;
use App\Models\User as UserModel;
use Illuminate\Auth\Access\Response;

class MarcaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(UserModel $user): bool
    {
        return $user->hasPermission('view_marcas');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(UserModel $user, MarcaModel $marca): bool
    {
        // Users can view all marcas if they have the view_all_data permission
        // or if they have the view_marcas permission
        return $user->hasPermission('view_marcas') || $user->hasPermission('view_all_data');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(UserModel $user): bool
    {
        return $user->hasPermission('create_marcas');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(UserModel $user, MarcaModel $marca): bool
    {
        return $user->hasPermission('edit_marcas');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(UserModel $user, MarcaModel $marca): bool
    {
        return $user->hasPermission('delete_marcas');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(UserModel $user, MarcaModel $marca): bool
    {
        return $user->hasPermission('delete_marcas');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(UserModel $user, MarcaModel $marca): bool
    {
        return $user->hasPermission('delete_marcas');
    }
}