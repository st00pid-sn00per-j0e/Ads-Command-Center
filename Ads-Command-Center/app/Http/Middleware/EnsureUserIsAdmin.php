<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        $isAdmin = ($user->role ?? null) === 'admin'
            || $user->organizations()->wherePivot('role', 'admin')->wherePivot('status', 'active')->exists();

        if (! $isAdmin) {
            abort(403);
        }

        return $next($request);
    }
}
