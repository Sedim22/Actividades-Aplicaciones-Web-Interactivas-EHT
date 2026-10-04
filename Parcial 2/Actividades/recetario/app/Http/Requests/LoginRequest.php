<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:72'],
        ];
    }

    public function authenticate(): void
    {
        $key = Str::transliterate(Str::lower($this->string('name')->toString())).'|'.$this->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'name' => 'Demasiados intentos. Inténtalo de nuevo en '.RateLimiter::availableIn($key).' segundos.',
            ]);
        }

        if (! Auth::attempt($this->only('name', 'password'))) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'name' => 'El nombre de usuario o la contraseña son incorrectos.',
            ]);
        }

        RateLimiter::clear($key);
    }
}
