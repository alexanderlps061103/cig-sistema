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
    ->withMiddleware(function (Middleware $middleware): void {
        // Registro de alias para tus middlewares
        $middleware->alias([
            'check.role'    => \App\Http\Middleware\CheckRole::class,
            'check.aprobado'=> \App\Http\Middleware\CheckAprobado::class,
        ]);

        // Opcional: si quisieras agregarlos a un grupo específico, por ejemplo:
        // $middleware->web(append: [
        //     \App\Http\Middleware\CheckRole::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
