<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsSpecialistOrAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        $isSpecialist = $user->organizations()->wherePivot('role', 'specialist')->wherePivot('status', 'active')->exists();
        $isAdmin = $user->isAdmin() || $user->organizations()->wherePivot('role', 'admin')->wherePivot('status', 'active')->exists();

        if (! ($isSpecialist || $isAdmin)) {
            abort(403);
        }

        return $next($request);
    }
}
