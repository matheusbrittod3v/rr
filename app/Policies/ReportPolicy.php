<?php

namespace App\Policies;

use App\Models\User;

class ReportPolicy
{
    /**
     * Determine whether the user can view reports.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_reports') || $user->hasPermission('view_all_data');
    }

    /**
     * Determine whether the user can generate reports.
     */
    public function generate(User $user): bool
    {
        return $user->hasPermission('generate_reports');
    }
}
