<?php

namespace App\Policies;

use App\Models\User as UserModel;
use App\Models\WhatsApi as WhatsApiModel;
use Illuminate\Auth\Access\Response;

class WhatsApiPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(UserModel $user): bool
    {
        // Check if user has permission to view WhatsApp APIs
        return $user->hasPermission('view_whatsapis');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(UserModel $user, WhatsApiModel $whatsApi): bool
    {
        // Users can only view their own WhatsApp APIs
        return $user->id === $whatsApi->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(UserModel $user): bool
    {
        // Users can only create a WhatsApp API if they don't already have one
        return !WhatsApiModel::userHasConfig($user->id);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(UserModel $user, WhatsApiModel $whatsApi): bool
    {
        // Users can only update their own WhatsApp APIs
        return $user->id === $whatsApi->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(UserModel $user, WhatsApiModel $whatsApi): bool
    {
        // Users can only delete their own WhatsApp APIs
        return $user->id === $whatsApi->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(UserModel $user, WhatsApiModel $whatsApi): bool
    {
        // Users can only restore their own WhatsApp APIs
        return $user->id === $whatsApi->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(UserModel $user, WhatsApiModel $whatsApi): bool
    {
        // Users can only permanently delete their own WhatsApp APIs
        return $user->id === $whatsApi->user_id;
    }
}