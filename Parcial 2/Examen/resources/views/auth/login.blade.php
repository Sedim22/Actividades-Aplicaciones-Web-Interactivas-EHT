@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h3">Inicia sesión</h1>
                    <p class="text-body-secondary mb-4">Ingresa con tu correo y contraseña.</p>
                    <form method="POST" action="{{ route('login.store') }}" novalidate>
                        @csrf
                        <div class="mb-3">
                            <x-campo name="email" label="Correo electrónico" type="email" required
                                     maxlength="255" autocomplete="username" autofocus />
                        </div>
                        <div class="mb-4">
                            <x-campo name="password" label="Contraseña" type="password" required autocomplete="current-password" />
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Iniciar sesión</button>
                    </form>
                    <p class="text-center mt-4 mb-0">¿Aún no tienes cuenta? <a href="{{ route('registro') }}">Regístrate</a></p>
                </div>
            </div>
        </div>
    </div>
@endsection
