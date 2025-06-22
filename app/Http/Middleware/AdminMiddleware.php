<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized: Admin access required.');
        }

        // Mark that AdminMiddleware was passed
        $request->attributes->set('_admin_middleware', true);

        return $next($request);
    }
}
