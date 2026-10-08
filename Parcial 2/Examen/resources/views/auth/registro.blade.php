@extends('layouts.app')

@section('title', 'Crear cuenta')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h3">Crea tu cuenta</h1>
                    <p class="text-body-secondary mb-4">Regístrate como jugador. Todos los campos son obligatorios.</p>
                    <form method="POST" action="{{ route('registro.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror" required maxlength="255" autocomplete="name" autofocus
                                   @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                            @error('name')<div id="name-error" class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                   class="form-control @error('email') is-invalid @enderror" required maxlength="255" autocomplete="email"
                                   @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                            @error('email')<div id="email-error" class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input id="password" type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror" required minlength="8" maxlength="72" autocomplete="new-password"
                                   aria-describedby="password-help @error('password') password-error @enderror"
                                   @error('password') aria-invalid="true" @enderror>
                            <div id="password-help" class="form-text">Usa entre 8 y 72 caracteres.</div>
                            @error('password')<div id="password-error" class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                   class="form-control @error('password_confirmation') is-invalid @enderror" required minlength="8" maxlength="72" autocomplete="new-password"
                                   @error('password_confirmation') aria-invalid="true" aria-describedby="confirmation-error" @enderror>
                            @error('password_confirmation')<div id="confirmation-error" class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Crear cuenta</button>
                    </form>
                    <p class="text-center mt-4 mb-0">¿Ya tienes una cuenta? <a href="{{ route('login') }}">Inicia sesión</a></p>
                </div>
            </div>
        </div>
    </div>
@endsection
