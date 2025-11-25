<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: base_path('routes/web.php'),
        api: base_path('routes/api.php'),
        commands: base_path('routes/console.php'),
    )

    ->withMiddleware(function (Middleware $middleware) {
    $middleware->prepend(\App\Http\Middleware\CorsFix::class);
})

    ->withMiddleware(function (Illuminate\Foundation\Configuration\Middleware $middleware): void {
    $middleware->alias([
        'auth' => \App\Http\Middleware\Authenticate::class,
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
})


    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
