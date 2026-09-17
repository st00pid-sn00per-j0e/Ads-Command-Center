<?php

namespace App\Policies;

use App\Models\ApprovalRequest;
use App\Models\User;

class ApprovalRequestPolicy
{
    public function view(User $user, ApprovalRequest $request): bool
    {
        return $user->organization_id === $request->organization_id && ($user->isAdmin() || $request->requested_by === $user->id);
    }

    public function approve(User $user, ApprovalRequest $request): bool
    {
        return $user->isAdmin() && $user->organization_id === $request->organization_id && $request->status === 'pending';
    }
}
