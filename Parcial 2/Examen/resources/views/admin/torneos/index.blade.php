@extends('layouts.app')

@section('title', 'Gestionar torneos')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h2 mb-1">Gestionar torneos</h1>
            <p class="text-body-secondary mb-0">Todos tus torneos, incluidos los cerrados, llenos y pasados.</p>
        </div>
        <a href="{{ route('admin.torneos.create') }}" class="btn btn-primary">Crear torneo</a>
    </div>

    @if ($torneos->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center p-5">
                <h2 class="h4">No hay torneos para mostrar</h2>
                <p class="text-body-secondary">Crea un torneo para comenzar.</p>
                <a href="{{ route('admin.torneos.create') }}" class="btn btn-outline-primary">Crear mi primer torneo</a>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <caption class="visually-hidden">Listado de torneos ordenados por fecha</caption>
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4">Torneo</th>
                            <th scope="col">Juego o deporte</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Cupo</th>
                            <th scope="col">Estado</th>
                            <th scope="col" class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($torneos as $torneo)
                            <tr>
                                <th scope="row" class="ps-4 text-break">
                                    <a href="{{ route('admin.torneos.edit', $torneo) }}" class="text-decoration-none">{{ $torneo->nombre_torneo }}</a>
                                </th>
                                <td>{{ \App\Models\Torneos::TIPOS[$torneo->tipo] ?? $torneo->tipo }}</td>
                                <td class="text-nowrap">{{ $torneo->fecha->format('d/m/Y') }}</td>
                                <td class="text-nowrap">
                                    <span class="fw-semibold">{{ $torneo->inscripciones_count }} / {{ $torneo->cupo }}</span>
                                    <span class="d-block small text-body-secondary">{{ $torneo->plazasDisponibles() }} plazas libres</span>
                                </td>
                                <td>
                                    @include('partials.estado-torneo')
                                </td>
                                <td class="pe-4">
                                    <div class="d-flex flex-wrap justify-content-end gap-2">
                                        <a href="{{ route('admin.torneos.inscripciones.index', $torneo) }}" class="btn btn-outline-primary btn-sm"
                                           aria-label="Gestionar inscritos de {{ $torneo->nombre_torneo }}">Inscritos</a>
                                        <a href="{{ route('torneos.show', $torneo) }}" class="btn btn-outline-secondary btn-sm"
                                           aria-label="Ver {{ $torneo->nombre_torneo }}">Ver</a>
                                        <a href="{{ route('admin.torneos.edit', $torneo) }}" class="btn btn-outline-primary btn-sm"
                                           aria-label="Editar {{ $torneo->nombre_torneo }}">Editar</a>
                                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#eliminar-torneo-{{ $torneo->id }}" aria-label="Eliminar {{ $torneo->nombre_torneo }}">Eliminar</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $torneos->links('partials.paginacion') }}</div>

        @foreach ($torneos as $torneo)
            @include('admin.torneos.eliminar')
        @endforeach
    @endif
@endsection
