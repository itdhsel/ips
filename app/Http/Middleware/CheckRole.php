<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Get the user's role from the database, force lowercase, and trim spaces
        $userRole = strtolower(trim(Auth::user()->role ?? ''));

        // Clean up the allowed roles passed from the routes/web.php file
        $allowedRoles = array_map('strtolower', array_map('trim', $roles));

        // If the user's role is in the allowed list, grant access!
        if (in_array($userRole, $allowedRoles)) {
            return $next($request);
        }

        // If it fails, print exactly what the middleware saw to help debug
        $allowedString = implode(', ', $allowedRoles);
        abort(403, "Access Denied. Your DB Role: '{$userRole}'. Allowed Roles: '{$allowedString}'.");
    }
}