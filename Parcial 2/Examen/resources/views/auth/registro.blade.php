@extends('layouts.app')

@section('title', 'Crear cuenta')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h3">Crea tu cuenta</h1>
                    <p class="text-body-secondary mb-4">Regístrate como jugador. Todos los campos son obligatorios.</p>
                    <form method="POST" action="{{ route('registro.store') }}" novalidate>
                        @csrf
                        <div class="mb-3">
                            <x-campo name="name" label="Nombre" required maxlength="255" autocomplete="name" autofocus />
                        </div>
                        <div class="mb-3">
                            <x-campo name="email" label="Correo electrónico" type="email" required maxlength="255" autocomplete="email" />
                        </div>
                        <div class="mb-3">
                            <x-campo name="password" label="Contraseña" type="password" required minlength="8" maxlength="72"
                                     autocomplete="new-password" help="Usa entre 8 y 72 caracteres." />
                        </div>
                        <div class="mb-4">
                            <x-campo name="password_confirmation" label="Confirmar contraseña" type="password"
                                     required minlength="8" maxlength="72" autocomplete="new-password" />
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Crear cuenta</button>
                    </form>
                    <p class="text-center mt-4 mb-0">¿Ya tienes una cuenta? <a href="{{ route('login') }}">Inicia sesión</a></p>
                </div>
            </div>
        </div>
    </div>
@endsection
