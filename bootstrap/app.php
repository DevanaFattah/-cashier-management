<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth'  => \App\Http\Middleware\Authenticate::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'role'  => \App\Http\Middleware\CheckRole::class,
        ]);

        // Exclude Midtrans webhook from CSRF — security handled by signature key validation
        // Exclude all /api/* routes — protected by session auth, not CSRF
        // Exclude login route so Postman can authenticate easily
        $middleware->validateCsrfTokens(except: [
            'midtrans/notification',
            'api/*',
            'login',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
