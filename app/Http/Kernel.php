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
            \App\Http\Middleware\VerifyCsrfToken::class,
        ],
        'api' => [
            // API middleware
        ],
    ];

    protected $routeMiddleware = [
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'staff' => \App\Http\Middleware\StaffMiddleware::class,
        'doctor' => \App\Http\Middleware\DoctorMiddleware::class,
    ];
}
