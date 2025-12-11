<?php

namespace App\Policies;

use App\Models\User as UserModel;

class DashboardPolicy
{
    /**
     * Determine whether the user can view the dashboard.
     */
    public function view(UserModel $user): bool
    {
        return $user->hasPermission('view_dashboard');
    }
}