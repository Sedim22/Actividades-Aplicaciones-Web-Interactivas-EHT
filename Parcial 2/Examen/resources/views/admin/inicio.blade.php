@extends('layouts.app')

@section('title', 'Panel de administrador')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h2">Panel de administrador</h1>
            <p class="text-body-secondary mb-4">Bienvenido, {{ auth()->user()->name }}. Has accedido con tu cuenta de administrador.</p>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h2 class="h5">Gestión de torneos</h2>
                    <p class="text-body-secondary">Crea torneos, modifica sus datos y administra su cupo y estado.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.torneos.index') }}" class="btn btn-primary">Gestionar torneos</a>
                        <a href="{{ route('admin.torneos.create') }}" class="btn btn-outline-primary">Crear torneo</a>
                    </div>
                </div>
            </div>
            @include('partials.cuenta')
        </div>
    </div>
@endsection
