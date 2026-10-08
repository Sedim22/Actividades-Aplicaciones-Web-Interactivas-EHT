@extends('layouts.app')

@section('title', 'Crear torneo')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <a href="{{ route('admin.torneos.index') }}" class="d-inline-block mb-3">Volver a torneos</a>
            <h1 class="h2">Crear torneo</h1>
            <p class="text-body-secondary mb-4">Define el juego, la fecha y las plazas de tu torneo.</p>
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.torneos.store') }}" novalidate>
                        @csrf
                        @include('admin.torneos.formulario')
                        <div class="d-flex flex-wrap gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">Crear torneo</button>
                            <a href="{{ route('admin.torneos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
