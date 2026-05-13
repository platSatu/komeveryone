<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $allowedRoles)
    {
        $user = auth()->user();

        // Pecah jika banyak role: "1,2,3"
        $allowedRolesArray = explode(',', $allowedRoles);

        if (!in_array($user->role_id, $allowedRolesArray)) {
            abort(403, 'Not Found');
        }

        return $next($request);
    }
}
