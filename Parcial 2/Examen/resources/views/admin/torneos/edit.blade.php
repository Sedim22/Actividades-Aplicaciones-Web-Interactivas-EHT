@extends('layouts.app')

@section('title', 'Editar torneo')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <a href="{{ route('admin.torneos.index') }}" class="d-inline-block mb-3">Volver a torneos</a>
            <h1 class="h2">Editar torneo</h1>
            <p class="text-body-secondary text-break mb-4">{{ $torneo->nombre_torneo }}</p>
            <div class="alert alert-info" role="status">
                Jugadores inscritos: <strong>{{ $torneo->inscripciones_count }}</strong>.
                El cupo no puede ser menor que el número de inscritos.
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.torneos.update', $torneo) }}">
                        @csrf
                        @method('PUT')
                        @include('admin.torneos.formulario')
                        <div class="d-flex flex-wrap gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                            <a href="{{ route('admin.torneos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
