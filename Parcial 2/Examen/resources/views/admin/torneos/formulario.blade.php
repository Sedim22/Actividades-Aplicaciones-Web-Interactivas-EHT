<p class="small text-body-secondary">Todos los campos son obligatorios, excepto la descripción.</p>
<div class="row g-3">
    <div class="col-12">
        <label for="nombre_torneo" class="form-label">Nombre del torneo</label>
        <input id="nombre_torneo" type="text" name="nombre_torneo" value="{{ old('nombre_torneo', $torneo->nombre_torneo) }}"
               class="form-control @error('nombre_torneo') is-invalid @enderror" required maxlength="255" autofocus
               @error('nombre_torneo') aria-invalid="true" aria-describedby="nombre-error" @enderror>
        @error('nombre_torneo')<div id="nombre-error" class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="tipo" class="form-label">Juego o deporte</label>
        <select id="tipo" name="tipo" class="form-select @error('tipo') is-invalid @enderror" required
                @error('tipo') aria-invalid="true" aria-describedby="tipo-error" @enderror>
            <option value="">Selecciona una opción</option>
            @foreach (\App\Models\Torneos::TIPOS as $valor => $etiqueta)
                <option value="{{ $valor }}" @selected(old('tipo', $torneo->tipo) === $valor)>{{ $etiqueta }}</option>
            @endforeach
        </select>
        @error('tipo')<div id="tipo-error" class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="fecha" class="form-label">Fecha del torneo</label>
        <input id="fecha" type="date" name="fecha" value="{{ old('fecha', $torneo->fecha?->format('Y-m-d')) }}"
               class="form-control @error('fecha') is-invalid @enderror" required min="{{ today()->addDay()->format('Y-m-d') }}"
               aria-describedby="fecha-ayuda @error('fecha') fecha-error @enderror" @error('fecha') aria-invalid="true" @enderror>
        <div id="fecha-ayuda" class="form-text">Selecciona una fecha posterior a hoy.</div>
        @error('fecha')<div id="fecha-error" class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="cupo" class="form-label">Cupo de jugadores</label>
        <input id="cupo" type="number" name="cupo" value="{{ old('cupo', $torneo->cupo) }}"
               class="form-control @error('cupo') is-invalid @enderror" required min="{{ max(2, $torneo->inscripciones_count ?? 0) }}" max="100" step="1"
               aria-describedby="cupo-ayuda @error('cupo') cupo-error @enderror" @error('cupo') aria-invalid="true" @enderror>
        <div id="cupo-ayuda" class="form-text">Entre 2 y 100 jugadores. Cupo inicial: 16.</div>
        @error('cupo')<div id="cupo-error" class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="estado" class="form-label">Estado</label>
        <select id="estado" name="estado" class="form-select @error('estado') is-invalid @enderror" required
                aria-describedby="estado-ayuda @error('estado') estado-error @enderror" @error('estado') aria-invalid="true" @enderror>
            @foreach (\App\Models\Torneos::ESTADOS as $valor => $etiqueta)
                <option value="{{ $valor }}" @selected(old('estado', $torneo->estado) === $valor)>{{ $etiqueta }}</option>
            @endforeach
        </select>
        <div id="estado-ayuda" class="form-text">Un torneo abierto también necesita fecha futura y plazas libres para admitir inscripciones.</div>
        @error('estado')<div id="estado-error" class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label for="descripcion" class="form-label">Descripción <span class="text-body-secondary">(opcional)</span></label>
        <textarea id="descripcion" name="descripcion" rows="4" maxlength="10000"
                  class="form-control @error('descripcion') is-invalid @enderror"
                  aria-describedby="descripcion-ayuda @error('descripcion') descripcion-error @enderror"
                  @error('descripcion') aria-invalid="true" @enderror>{{ old('descripcion', $torneo->descripcion) }}</textarea>
        <div id="descripcion-ayuda" class="form-text">Incluye reglas, ubicación u otros detalles. Máximo 10,000 caracteres.</div>
        @error('descripcion')<div id="descripcion-error" class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
