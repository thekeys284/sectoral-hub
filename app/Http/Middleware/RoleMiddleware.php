<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek apakah user sudah login dan memiliki role yang sesuai
        if (!Auth::check()) {
            abort(403, 'Unauthorized action.');
        }

        $userRoles = is_string(Auth::user()->role) ? json_decode(Auth::user()->role, true) ?? [Auth::user()->role] : (array) Auth::user()->role;
        $activeRole = session('active_role', $userRoles[0] ?? '');

        if (!in_array($activeRole, $roles)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
