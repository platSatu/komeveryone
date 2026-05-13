<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role = null, $permission = null)
    {
        // Cek apakah user sudah login
        if (!auth()->check()) {
            abort(403, 'Unauthorized access');
        }

        // Cek apakah role yang diberikan ada dan apakah user memiliki role tersebut
        if ($role && !auth()->user()->hasRole($role)) {
            abort(403, 'You do not have the required role.');
        }

        // Cek apakah permission yang diberikan ada dan apakah user memiliki permission tersebut
        if ($permission && !auth()->user()->can($permission)) {
            abort(403, 'You do not have the required permission.');
        }

        return $next($request);
    }
}
