@extends('layouts.app')

@section('title', 'Torneos disponibles')

@section('content')
    <div class="mb-4">
        <h1 class="h2">Torneos disponibles</h1>
        <p class="text-body-secondary mb-0">Encuentra torneos abiertos, con fecha futura y plazas libres. Los más próximos aparecen primero.</p>
    </div>

    @if ($torneos->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center p-5">
                <h2 class="h4">No hay torneos disponibles</h2>
                <p class="text-body-secondary mb-0">Por ahora no hay torneos abiertos con fecha futura y plazas libres. Vuelve a consultar más adelante.</p>
            </div>
        </div>
    @else
        <p class="small text-body-secondary">{{ $torneos->total() }} torneos disponibles</p>
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
            @foreach ($torneos as $torneo)
                <div class="col">
                    <article class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                <span class="small text-body-secondary">{{ \App\Models\Torneos::TIPOS[$torneo->tipo] ?? $torneo->tipo }}</span>
                                @include('partials.estado-torneo')
                            </div>
                            <h2 class="h5 text-break">{{ $torneo->nombre_torneo }}</h2>
                            <dl class="row small mt-3 mb-4">
                                <dt class="col-5">Fecha</dt>
                                <dd class="col-7"><time datetime="{{ $torneo->fecha->format('Y-m-d') }}">{{ $torneo->fecha->format('d/m/Y') }}</time></dd>
                                <dt class="col-5">Inscritos</dt>
                                <dd class="col-7">{{ $torneo->inscripciones_count }} de {{ $torneo->cupo }}</dd>
                                <dt class="col-5">Plazas libres</dt>
                                <dd class="col-7 fw-semibold text-success">{{ $torneo->plazasDisponibles() }}</dd>
                            </dl>
                            <a href="{{ route('torneos.show', $torneo) }}" class="btn btn-outline-primary mt-auto"
                               aria-label="Ver detalles de {{ $torneo->nombre_torneo }}">Ver detalles</a>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $torneos->links('partials.paginacion') }}</div>
    @endif
@endsection
