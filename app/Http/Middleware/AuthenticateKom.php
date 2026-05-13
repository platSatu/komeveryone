<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthenticateKom
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle($request, Closure $next)
    // {
    //     if (!Auth::guard('kom')->check()) {
    //         Log::info('User not authenticated, redirecting to login.');
    //         return redirect()->route('kom.login');
    //     }

    //     Log::info('User is authenticated, proceeding with the request.');
    //     return $next($request);
    // }

    public function handle($request, Closure $next)
    {
        //$user = Auth::guard('web')->user();
        $user = Auth::user();
        if (!$user) {
            Log::warning('AuthKom: User not authenticated, redirecting to login. Path: ' . $request->path());
            return redirect()->route('kom.login');
        }

        Log::info('AuthKom: Authenticated user ID ' . $user->id);
        return $next($request);
    }

}
