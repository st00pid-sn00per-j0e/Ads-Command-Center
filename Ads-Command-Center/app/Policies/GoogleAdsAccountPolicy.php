<?php

namespace App\Policies;

use App\Models\GoogleAdsAccount;
use App\Models\User;

class GoogleAdsAccountPolicy
{
    public function view(User $user, GoogleAdsAccount $account): bool
    {
        if ($user->organization_id !== $account->connection->organization_id || $user->status !== 'active') {
            return false;
        }

        return $user->isAdmin() || $account->specialists()->whereKey($user)->wherePivot('status', 'active')->exists();
    }
}
