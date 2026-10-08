<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistroRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function registrar(RegistroRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        // El rol siempre lo asigna el servidor, nunca el formulario.
        $usuario = User::create([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'password' => $datos['password'],
            'rol' => User::ROL_JUGADOR,
        ]);

        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->route('jugador.inicio')
            ->with('success', 'Tu cuenta de jugador se creó correctamente.');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $clave = 'login:'.hash('sha256', $datos['email'].'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($clave, 5)) {
            $segundos = RateLimiter::availableIn($clave);

            throw ValidationException::withMessages([
                'email' => "Demasiados intentos. Intenta de nuevo en {$segundos} segundos.",
            ]);
        }

        // Una cuenta solo puede iniciar sesión con uno de los roles admitidos.
        if (! Auth::attempt([...$datos, 'rol' => array_keys(User::ROLES)])) {
            RateLimiter::hit($clave, 60);

            throw ValidationException::withMessages([
                'email' => 'El correo electrónico o la contraseña son incorrectos.',
            ]);
        }

        RateLimiter::clear($clave);
        $request->session()->regenerate();

        return redirect()->route($request->user()->rol === User::ROL_ADMIN ? 'admin.inicio' : 'jugador.inicio')
            ->with('success', 'Has iniciado sesión correctamente.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Has cerrado sesión correctamente.');
    }

    public function panel(Request $request): RedirectResponse
    {
        return redirect()->route($request->user()->rol === User::ROL_ADMIN ? 'admin.inicio' : 'jugador.inicio');
    }
}
