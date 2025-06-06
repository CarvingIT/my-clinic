<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->hasRole('staff')) {
            return $next($request);
        }

        return redirect()->route('dashboard')->with('error', 'Unauthorized: Staff access required.');
    }
}
