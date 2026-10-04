<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectUsersTo(fn () => route('recetas.index'));

        // En las rutas privadas, comprobar la sesión antes del token CSRF.
        // Las peticiones de usuarios autenticados siguen requiriendo ese token.
        $middleware->appendToPriorityList(AuthenticatesRequests::class, ValidateCsrfToken::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Acceso no autorizado. Debes iniciar sesión.'], 401);
            }

            return response()->view('errors.401', [], 401);
        });
    })->create();
