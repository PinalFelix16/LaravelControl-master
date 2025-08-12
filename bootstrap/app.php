<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        api: __DIR__.'/../routes/api.php',

    )
    ->withMiddleware(function (Middleware $middleware) {
        // Globales
        $middleware->use([
            HandleCors::class, // <-- CORS nativo
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
