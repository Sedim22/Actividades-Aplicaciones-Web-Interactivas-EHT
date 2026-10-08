@extends('layouts.app')

@section('title', 'Mis torneos')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h2 mb-1">Mis torneos</h1>
            <p class="text-body-secondary mb-0">Consulta tus inscripciones. Puedes cancelarlas antes de la fecha del evento.</p>
        </div>
        <a href="{{ route('torneos.index') }}" class="btn btn-primary">Explorar torneos</a>
    </div>

    @if ($torneos->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center p-5">
                <h2 class="h4">Aún no estás inscrito en ningún torneo</h2>
                <p class="text-body-secondary">Explora los torneos disponibles y elige dónde participar.</p>
                <a href="{{ route('torneos.index') }}" class="btn btn-outline-primary">Ver torneos disponibles</a>
            </div>
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-2 g-4">
            @foreach ($torneos as $torneo)
                <div class="col">
                    <article class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge text-bg-primary">Inscrito</span>
                                @include('partials.estado-torneo')
                            </div>
                            <h2 class="h5 text-break">{{ $torneo->nombre_torneo }}</h2>
                            <p class="text-body-secondary small">{{ \App\Models\Torneos::TIPOS[$torneo->tipo] ?? $torneo->tipo }}</p>
                            <dl class="row small mb-3">
                                <dt class="col-5">Fecha</dt>
                                <dd class="col-7">{{ $torneo->fecha->format('d/m/Y') }}</dd>
                                <dt class="col-5">Inscritos</dt>
                                <dd class="col-7">{{ $torneo->inscripciones_count }} de {{ $torneo->cupo }}</dd>
                            </dl>
                            <div class="mt-auto d-flex flex-wrap gap-2">
                                <a href="{{ route('torneos.show', $torneo) }}" class="btn btn-outline-primary">Ver detalles</a>
                                @if (! $torneo->estaVencida())
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#cancelar-inscripcion-{{ $torneo->id }}">Cancelar inscripción</button>
                                @else
                                    <span class="small text-body-secondary align-self-center">Plazo de cancelación terminado</span>
                                @endif
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $torneos->links('partials.paginacion') }}</div>
        @foreach ($torneos as $torneo)
            @if (! $torneo->estaVencida())
                @include('inscripciones.cancelar')
            @endif
        @endforeach
    @endif
@endsection
