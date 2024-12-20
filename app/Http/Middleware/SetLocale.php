<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next): Response
    // {
    //     $locale = $request->query('locale', session('locale', config('app.locale')));

    //     App::setLocale($locale);

    //     session(['locale' => $locale]);
    //     return $next($request);
    // }

    public function handle($request, Closure $next)
    {
        // Get the locale from session or fallback to default
        $locale = session('locale', config('app.locale'));
        App::setLocale($locale);

        return $next($request);
    }
}
