<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        // Global middleware

        \App\Http\Middleware\SetLocale::class, // Set locale middleware
    ];

    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\SetLocale::class, // Set locale middleware

        ],
        'api' => [
            // API middleware
        ],
    ];

    protected $routeMiddleware = [
        // Route middleware
    ];
}
