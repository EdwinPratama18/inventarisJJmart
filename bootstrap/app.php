<?php

use App\Http\Middleware\PeriksaPeran;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // CORS: izinkan frontend Vue.js
        $middleware->statefulApi();

        // Alias middleware peran
        $middleware->alias([
            'periksa.peran' => PeriksaPeran::class,
        ]);

        // Agar API mengembalikan JSON saat unauthenticated
        $middleware->redirectGuestsTo(fn (Request $request) => ($request->expectsJson() || $request->is('api/*')) ? null : route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
