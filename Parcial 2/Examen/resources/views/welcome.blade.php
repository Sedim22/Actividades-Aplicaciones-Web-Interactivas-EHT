@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
    <div class="row align-items-center g-5">
        <div class="col-lg-7">
            <span class="badge text-bg-primary mb-3">Comunidad de jugadores</span>
            <h1 class="display-5 fw-bold">Tu próxima competencia empieza aquí.</h1>
            <p class="lead text-body-secondary mt-3">Explora los torneos disponibles y conoce a sus participantes. Accede a tu cuenta o regístrate para formar parte de la comunidad.</p>
            <div class="d-flex flex-wrap gap-2 mt-4">
                <a class="btn btn-success btn-lg" href="{{ route('torneos.index') }}">Ver torneos</a>
                @guest
                    <a class="btn btn-primary btn-lg" href="{{ route('registro') }}">Crear mi cuenta</a>
                    <a class="btn btn-outline-secondary btn-lg" href="{{ route('login') }}">Iniciar sesión</a>
                @else
                    <a class="btn btn-primary btn-lg" href="{{ route('panel') }}">Ir a mi panel</a>
                @endguest
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h4">Un espacio para competir</h2>
                    <p class="text-body-secondary mb-0">Fútbol, básquetbol, videojuegos y más. Crea tu cuenta de jugador con tu nombre, correo y contraseña.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
