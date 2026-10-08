@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h3">Inicia sesión</h1>
                    <p class="text-body-secondary mb-4">Ingresa con tu correo y contraseña.</p>
                    <form method="POST" action="{{ route('login.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                   class="form-control @error('email') is-invalid @enderror" required maxlength="255" autocomplete="username" autofocus
                                   @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                            @error('email')<div id="email-error" class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Contraseña</label>
                            <input id="password" type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password"
                                   @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                            @error('password')<div id="password-error" class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Iniciar sesión</button>
                    </form>
                    <p class="text-center mt-4 mb-0">¿Aún no tienes cuenta? <a href="{{ route('registro') }}">Regístrate</a></p>
                </div>
            </div>
        </div>
    </div>
@endsection
