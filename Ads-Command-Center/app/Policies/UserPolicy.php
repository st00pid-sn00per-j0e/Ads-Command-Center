<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can invite specialists.
     */
    public function invite(User $user): bool
    {
        // Simple role check; if you rely on organization_user pivot, replace with a membership check
        return $user->isAdmin();
    }
}
