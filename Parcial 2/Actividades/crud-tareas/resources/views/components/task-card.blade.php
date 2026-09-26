@php
    $coloresEstado = [
        'por_hacer' => 'warning',
        'en_curso' => 'primary',
        'hecha' => 'success',
    ];
    $vencida = $tarea->estaVencida();
    $colorEstado = $coloresEstado[$tarea->estado];
    $colorTarjeta = $vencida ? 'danger' : $colorEstado;
@endphp

<article class="card shadow-sm mb-4 rounded-4 overflow-hidden {{ $vencida ? 'border-danger bg-danger-subtle' : 'border-'.$colorTarjeta.'-subtle' }}">
    <div class="bg-{{ $colorTarjeta }}" style="height: 4px;" aria-hidden="true"></div>

    <div class="card-body p-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div class="d-flex flex-wrap gap-2">
                <span class="badge rounded-pill text-bg-{{ $colorEstado }}">{{ $estados[$tarea->estado] }}</span>

                @if ($vencida)
                    <span class="badge rounded-pill text-bg-danger">Vencida</span>
                @endif
            </div>

            <a class="btn btn-outline-secondary btn-sm rounded-pill px-3" href="{{ route('tasks.edit', $tarea) }}">Editar</a>
        </div>

        <h3 class="card-title h5 fw-semibold text-break mb-2 {{ $vencida ? 'text-danger-emphasis' : '' }}">{{ $tarea->titulo }}</h3>

        @if ($tarea->descripcion)
            <p class="card-text small text-body-secondary text-break mb-3" style="white-space: pre-line;">{{ $tarea->descripcion }}</p>
        @endif

        <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
            <span class="badge rounded-pill bg-body-secondary text-body-secondary fw-medium">
                Prioridad: {{ $prioridades[$tarea->prioridad] }}
            </span>

            @if ($tarea->vencimiento)
                <span class="small {{ $vencida ? 'text-danger-emphasis fw-semibold' : 'text-body-secondary' }}">
                    {{ $vencida ? 'Venció:' : 'Vence:' }}
                    <time datetime="{{ $tarea->vencimiento->format('Y-m-d') }}">{{ $tarea->vencimiento->format('d/m/Y') }}</time>
                </span>
            @endif
        </div>
    </div>

    <div class="card-footer bg-transparent border-0 px-4 pt-0 pb-4">
        <form class="border-top pt-3" method="POST" action="{{ route('tasks.change-status', $tarea) }}">
            @csrf
            @method('PATCH')

            <fieldset>
                <legend class="float-none w-auto small fw-semibold text-body-secondary mb-2">Cambiar estado</legend>
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($estados as $valor => $etiqueta)
                        <button
                            type="submit"
                            name="estado"
                            value="{{ $valor }}"
                            class="btn btn-sm rounded-pill {{ $tarea->estado === $valor ? 'btn-'.$coloresEstado[$valor].' opacity-100' : 'btn-outline-'.$coloresEstado[$valor] }} {{ $valor === 'por_hacer' ? 'text-warning-emphasis' : '' }}"
                            @disabled($tarea->estado === $valor)
                        >{{ $etiqueta }}</button>
                    @endforeach
                </div>
            </fieldset>
        </form>
    </div>
</article>
