<?php

namespace App\Policies;

use App\Models\EmailConfig as EmailConfigModel;
use App\Models\User as UserModel;
use Illuminate\Auth\Access\Response;

class EmailConfigPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(UserModel $user): bool
    {
        // Check if user has permission to view email configs
        return $user->hasPermission('view_emailconfigs');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(UserModel $user, EmailConfigModel $emailConfig): bool
    {
        // Users can only view their own email configs
        return $user->id === $emailConfig->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(UserModel $user): bool
    {
        // Users can only create an email config if they don't already have one
        return !EmailConfigModel::userHasConfig($user->id);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(UserModel $user, EmailConfigModel $emailConfig): bool
    {
        // Users can only update their own email configs
        return $user->id === $emailConfig->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(UserModel $user, EmailConfigModel $emailConfig): bool
    {
        // Users can only delete their own email configs
        return $user->id === $emailConfig->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(UserModel $user, EmailConfigModel $emailConfig): bool
    {
        // Users can only restore their own email configs
        return $user->id === $emailConfig->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(UserModel $user, EmailConfigModel $emailConfig): bool
    {
        // Users can only permanently delete their own email configs
        return $user->id === $emailConfig->user_id;
    }
}