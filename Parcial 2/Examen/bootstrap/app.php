<?php

use App\Http\Middleware\VerificarRol;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'rol' => VerificarRol::class,
        ]);

        $middleware->prependToPriorityList(SubstituteBindings::class, VerificarRol::class);

        $middleware->redirectGuestsTo(function (Request $request): string {
            $request->session()->flash('error', 'Debes iniciar sesión para acceder a esta página.');

            return route('login');
        });

        $middleware->redirectUsersTo(fn (Request $request): string => route('panel'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
