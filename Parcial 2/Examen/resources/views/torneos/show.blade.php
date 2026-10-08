@extends('layouts.app')

@section('title'){{ $torneo->nombre_torneo }}@endsection

@section('content')
    <a href="{{ route('torneos.index') }}" class="d-inline-block mb-3">Volver a torneos disponibles</a>
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <div class="mb-2">@include('partials.estado-torneo')</div>
            <h1 class="h2 text-break">{{ $torneo->nombre_torneo }}</h1>
            <p class="text-body-secondary mb-0">{{ \App\Models\Torneos::TIPOS[$torneo->tipo] ?? $torneo->tipo }}</p>
        </div>
        @if (auth()->user()?->rol === \App\Models\User::ROL_ADMIN)
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.torneos.inscripciones.index', $torneo) }}" class="btn btn-primary">Gestionar inscritos</a>
                <a href="{{ route('admin.torneos.edit', $torneo) }}" class="btn btn-outline-primary">Editar torneo</a>
            </div>
        @endif
    </div>

    @switch($torneo->estadoActual())
        @case('cerrado')
            <div class="alert alert-secondary" role="status">El administrador ha cerrado este torneo. Puedes consultar sus datos y participantes.</div>
            @break
        @case('vencido')
            <div class="alert alert-secondary" role="status">La fecha de este torneo ya llegó. Está cerrado a nuevas inscripciones.</div>
            @break
        @case('lleno')
            <div class="alert alert-warning" role="status">Este torneo está lleno. No quedan plazas disponibles.</div>
            @break
    @endswitch

    @if (auth()->user()?->rol === \App\Models\User::ROL_JUGADOR)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                @if ($inscrito)
                    <div>
                        <span class="badge text-bg-primary mb-2">Inscrito</span>
                        <p class="mb-0">Ya estás inscrito en este torneo.</p>
                    </div>
                    @if (! $torneo->estaVencida())
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                                data-bs-target="#cancelar-inscripcion-{{ $torneo->id }}">Cancelar mi inscripción</button>
                    @else
                        <p class="small text-body-secondary mb-0">El plazo para cancelar terminó al llegar la fecha del torneo.</p>
                    @endif
                @elseif ($torneo->estadoActual() === 'abierto')
                    <p class="mb-0">Hay plazas disponibles. Puedes unirte a este torneo.</p>
                    <form method="POST" action="{{ route('inscripciones.store', $torneo) }}">
                        @csrf
                        <button type="submit" class="btn btn-primary">Inscribirme</button>
                    </form>
                @else
                    <p class="mb-0 text-body-secondary">Este torneo no admite nuevas inscripciones.</p>
                @endif
            </div>
        </div>
        @if ($inscrito && ! $torneo->estaVencida())
            @include('inscripciones.cancelar')
        @endif
    @elseif (auth()->guest() && $torneo->estadoActual() === 'abierto')
        <div class="alert alert-info">Para inscribirte, <a href="{{ route('login') }}" class="alert-link">inicia sesión</a> o <a href="{{ route('registro') }}" class="alert-link">crea una cuenta de jugador</a>.</div>
    @endif

    <div class="row g-4">
        <div class="col-lg-7">
            <section class="card border-0 shadow-sm mb-4" aria-labelledby="datos-torneo">
                <div class="card-body p-4">
                    <h2 id="datos-torneo" class="h5 mb-3">Datos del torneo</h2>
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Fecha</dt>
                        <dd class="col-sm-7"><time datetime="{{ $torneo->fecha->format('Y-m-d') }}">{{ $torneo->fecha->format('d/m/Y') }}</time></dd>
                        <dt class="col-sm-5">Cupo total</dt>
                        <dd class="col-sm-7">{{ $torneo->cupo }} jugadores</dd>
                        <dt class="col-sm-5">Jugadores inscritos</dt>
                        <dd class="col-sm-7">{{ $torneo->inscripciones_count }}</dd>
                        <dt class="col-sm-5">Plazas libres</dt>
                        <dd class="col-sm-7 mb-0">{{ $torneo->plazasDisponibles() }}</dd>
                    </dl>
                </div>
            </section>
            <section class="card border-0 shadow-sm" aria-labelledby="descripcion-torneo">
                <div class="card-body p-4">
                    <h2 id="descripcion-torneo" class="h5 mb-3">Descripción</h2>
                    @if ($torneo->descripcion)
                        <div class="text-break">{!! nl2br(e($torneo->descripcion)) !!}</div>
                    @else
                        <p class="text-body-secondary mb-0">Este torneo no tiene descripción.</p>
                    @endif
                </div>
            </section>
        </div>
        <div class="col-lg-5">
            <section class="card border-0 shadow-sm" aria-labelledby="participantes-torneo">
                <div class="card-body p-4">
                    <h2 id="participantes-torneo" class="h5 mb-3">Participantes <span class="badge text-bg-light">{{ $participantes->count() }}</span></h2>
                    @if ($participantes->isEmpty())
                        <p class="text-body-secondary mb-0">Aún no hay participantes inscritos.</p>
                    @else
                        <ol class="list-group list-group-numbered list-group-flush">
                            @foreach ($participantes as $inscripcion)
                                <li class="list-group-item text-break">{{ $inscripcion->user->name }}</li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            </section>
        </div>
    </div>
@endsection
