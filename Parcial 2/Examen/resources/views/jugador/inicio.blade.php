@extends('layouts.app')

@section('title', 'Panel de jugador')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h2">Panel de jugador</h1>
            <p class="text-body-secondary mb-4">Bienvenido, {{ auth()->user()->name }}. Has accedido con tu cuenta de jugador.</p>
            <div class="d-flex flex-wrap gap-2 mb-4">
                <a href="{{ route('torneos.index') }}" class="btn btn-primary">Explorar torneos</a>
                <a href="{{ route('inscripciones.index') }}" class="btn btn-outline-primary">Mis torneos</a>
            </div>
            @include('partials.cuenta')
        </div>
    </div>
@endsection
