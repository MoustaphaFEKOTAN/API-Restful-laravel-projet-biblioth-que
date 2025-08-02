<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
   ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auteur' => \App\Http\Middleware\EnsureUserIsAuthor::class,
            'admin'  => \App\Http\Middleware\CheckIfIsADMIN::class,
        ]);

      

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
