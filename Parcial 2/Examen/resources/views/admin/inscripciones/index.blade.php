@extends('layouts.app')

@section('title', 'Gestionar inscritos')

@section('content')
    <a href="{{ route('admin.torneos.index') }}" class="d-inline-block mb-3">Volver a gestionar torneos</a>
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h2">Gestionar inscritos</h1>
            <p class="text-body-secondary text-break mb-2">{{ $torneo->nombre_torneo }} · {{ $torneo->fecha->format('d/m/Y') }}</p>
            @include('partials.estado-torneo')
        </div>
        <a href="{{ route('torneos.show', $torneo) }}" class="btn btn-outline-primary">Ver detalle del torneo</a>
    </div>
    <p><strong>{{ $torneo->inscripciones_count }} de {{ $torneo->cupo }}</strong> jugadores inscritos · <strong>{{ $torneo->plazasDisponibles() }}</strong> plazas libres.</p>

    @if ($inscripciones->isEmpty())
        <div class="alert alert-info" role="status">Este torneo todavía no tiene inscripciones.</div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <caption class="visually-hidden">Inscripciones del torneo {{ $torneo->nombre_torneo }}</caption>
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4">Jugador</th>
                            <th scope="col">Correo electrónico</th>
                            <th scope="col">Fecha de inscripción</th>
                            <th scope="col" class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inscripciones as $inscripcion)
                            <tr>
                                <th scope="row" class="ps-4 text-break">{{ $inscripcion->user->name }}</th>
                                <td class="text-break">{{ $inscripcion->user->email }}</td>
                                <td class="text-nowrap">{{ $inscripcion->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-end pe-4">
                                    <button type="button" class="btn btn-outline-danger btn-sm text-nowrap" data-bs-toggle="modal"
                                            data-bs-target="#baja-inscripcion-{{ $inscripcion->id }}"
                                            aria-label="Dar de baja a {{ $inscripcion->user->name }}">Dar de baja</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @foreach ($inscripciones as $inscripcion)
            @include('admin.inscripciones.baja')
        @endforeach
    @endif
@endsection
