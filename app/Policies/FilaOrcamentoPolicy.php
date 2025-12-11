<?php

namespace App\Policies;

use App\Models\FilaOrcamento;
use App\Models\User;

class FilaOrcamentoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_fila_orcamentos');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FilaOrcamento $filaOrcamento): bool
    {
        return $user->hasPermission('view_fila_orcamentos') || $user->hasPermission('view_all_data');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create_fila_orcamentos');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FilaOrcamento $filaOrcamento): bool
    {
        return $user->hasPermission('edit_fila_orcamentos');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FilaOrcamento $filaOrcamento): bool
    {
        return $user->hasPermission('delete_fila_orcamentos');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FilaOrcamento $filaOrcamento): bool
    {
        return $user->hasPermission('delete_fila_orcamentos');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FilaOrcamento $filaOrcamento): bool
    {
        return $user->hasPermission('delete_fila_orcamentos');
    }
}
