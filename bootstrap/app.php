<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware aliases
        $middleware->alias([
            'is_admin' => \App\Http\Middleware\IsAdmin::class,
            'force.https' => \App\Http\Middleware\ForceHttps::class,
            'require.2fa' => \App\Http\Middleware\Require2FASetup::class,
        ]);
        
        // Global middleware for web group
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\ForceHttps::class,
            \App\Http\Middleware\SessionTimeout::class,
        ]);
        
        // Priority middleware
        $middleware->priority([
            \App\Http\Middleware\ForceHttps::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();