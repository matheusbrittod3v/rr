<?php

namespace App\Policies;

use App\Models\Service as ServiceModel;
use App\Models\User as UserModel;
use Illuminate\Auth\Access\Response;

class ServicePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(UserModel $user): bool
    {
        return $user->hasPermission('view_services');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(UserModel $user, ServiceModel $service): bool
    {
        // Users can view all services if they have the view_all_data permission
        // or if they have the view_services permission
        return $user->hasPermission('view_services') || $user->hasPermission('view_all_data');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(UserModel $user): bool
    {
        return $user->hasPermission('create_services');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(UserModel $user, ServiceModel $service): bool
    {
        return $user->hasPermission('edit_services');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(UserModel $user, ServiceModel $service): bool
    {
        return $user->hasPermission('delete_services');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(UserModel $user, ServiceModel $service): bool
    {
        return $user->hasPermission('delete_services');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(UserModel $user, ServiceModel $service): bool
    {
        return $user->hasPermission('delete_services');
    }
}