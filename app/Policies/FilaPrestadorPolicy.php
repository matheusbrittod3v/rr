<?php

namespace App\Policies;

use App\Models\FilaPrestador;
use App\Models\User;

class FilaPrestadorPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_fila_prestadores');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FilaPrestador $filaPrestador): bool
    {
        return $user->hasPermission('view_fila_prestadores') || $user->hasPermission('view_all_data');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create_fila_prestadores');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FilaPrestador $filaPrestador): bool
    {
        return $user->hasPermission('edit_fila_prestadores');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FilaPrestador $filaPrestador): bool
    {
        return $user->hasPermission('delete_fila_prestadores');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FilaPrestador $filaPrestador): bool
    {
        return $user->hasPermission('delete_fila_prestadores');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FilaPrestador $filaPrestador): bool
    {
        return $user->hasPermission('delete_fila_prestadores');
    }
}
