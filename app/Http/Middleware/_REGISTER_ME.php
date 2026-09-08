<?php

// In bootstrap/app.php, inside ->withMiddleware(function (Middleware $middleware) { ... })
// add this line to register the 'admin' alias used in routes/api.php:

use App\Http\Middleware\EnsureUserIsAdmin;

/*
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(...)
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
        ]);
    })
    ->withExceptions(...)
    ->create();
*/

// --- If you're on Laravel 10 or earlier (app/Http/Kernel.php) instead, add
// the same line to the $middlewareAliases array there:
//
// protected $middlewareAliases = [
//     ...
//     'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
// ];
