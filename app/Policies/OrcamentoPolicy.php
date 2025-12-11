<?php

namespace App\Policies;

use App\Models\Orcamento;
use App\Models\User;

class OrcamentoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_orcamentos');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Orcamento $orcamento): bool
    {
        // Check filtering logic if needed (e.g., user owns orcamento)
        // For now, check general permissions + view_all_data
        if ($user->hasPermission('view_all_data')) {
            return true;
        }

        if ($user->hasPermission('view_orcamentos')) {
            // Check if user owns it (optional, but good practice)
            // Assuming email link or similar logic if strict ownership needed
            // But basic permission check is requested.
            return true; 
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create_orcamentos');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Orcamento $orcamento): bool
    {
        return $user->hasPermission('edit_orcamentos');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Orcamento $orcamento): bool
    {
        return $user->hasPermission('delete_orcamentos');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Orcamento $orcamento): bool
    {
        return $user->hasPermission('delete_orcamentos');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Orcamento $orcamento): bool
    {
        return $user->hasPermission('delete_orcamentos');
    }
}
