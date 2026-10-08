<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        // 1. Block if not logged in at all
        if (!$user) {
            abort(403, 'Unauthorized action.');
        }

        // 2. SUPER ADMIN BYPASS: If the user is ITD, grant access to everything
        if ($user->role === 'ITD') {
            return $next($request);
        }

        // 3. Normal Role Check: Block if they are not in the allowed list
        if (!in_array($user->role, $roles)) {
            abort(403, 'Unauthorized action. You do not have permission to view this module.');
        }

        return $next($request);
    }
}