<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->hasRole('doctor')) {
            return $next($request);
        }

        return redirect()->route('dashboard')->with('error', 'Unauthorized: Doctor access required.');
    }
}
