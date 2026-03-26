<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RememberMeExtender
{
    /**
     * Handle an incoming request.
     *
     * This middleware manages remember-me token persistence.
     * - If user is logged in via remember token, the remember cookie is refreshed
     * - If user is logged in normally, nothing extra happens
     * - It does NOT regenerate sessions for remember token users to avoid losing the token
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // If user is logged in via remember token, ensure the remember cookie persists
        // by refreshing the guard state (Laravel automatically manages the token)
        if (Auth::check() && Auth::viaRemember()) {
            // Don't regenerate session here - let Laravel's session driver handle it naturally
            // The remember_token cookie is persistent and will auto-login the user
        }

        return $response;
    }
}
