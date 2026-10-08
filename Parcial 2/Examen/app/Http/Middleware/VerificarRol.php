<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarRol
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $usuario = $request->user();
        $rol = $usuario ? $usuario->rol : 'guest';

        if (in_array($rol, $roles, true)) {
            return $next($request);
        }

        if (! $usuario) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para acceder a esta página.');
        }

        abort_unless(array_key_exists($rol, User::ROLES), 403, 'Tu cuenta no tiene un rol válido.');

        return redirect()->route('inicio')
            ->with('error', 'No tienes permiso para acceder a esta página.');
    }
}
