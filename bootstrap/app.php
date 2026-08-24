<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        health: '/healthz',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // API-only beta: no browser session or cookie middleware is enabled here.
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Laravel's default exception rendering is retained; production logging is deployment-owned.
    })
    ->create();
