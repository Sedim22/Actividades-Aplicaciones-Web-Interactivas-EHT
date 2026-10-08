<p class="small text-body-secondary">Todos los campos son obligatorios, excepto la descripción.</p>
<div class="row g-3">
    <div class="col-12">
        <x-campo name="nombre_torneo" label="Nombre del torneo" :value="$torneo->nombre_torneo"
                 required maxlength="255" autofocus />
    </div>
    <div class="col-md-6">
        <x-campo name="tipo" label="Juego o deporte" type="select" :value="$torneo->tipo"
                 :options="['' => 'Selecciona una opción'] + \App\Models\Torneos::TIPOS" required />
    </div>
    <div class="col-md-6">
        <x-campo name="fecha" label="Fecha del torneo" type="date" :value="$torneo->fecha?->format('Y-m-d')"
                 required :min="today()->addDay()->format('Y-m-d')" help="Selecciona una fecha posterior a hoy." />
    </div>
    <div class="col-md-6">
        <x-campo name="cupo" label="Cupo de jugadores" type="number" :value="$torneo->cupo"
                 required :min="max(2, $torneo->inscripciones_count ?? 0)" max="100" step="1"
                 help="Entre 2 y 100 jugadores. Cupo inicial: 16." />
    </div>
    <div class="col-md-6">
        <x-campo name="estado" label="Estado" type="select" :value="$torneo->estado"
                 :options="['' => 'Selecciona un estado'] + \App\Models\Torneos::ESTADOS" required
                 help="Un torneo abierto también necesita fecha futura y plazas libres para admitir inscripciones." />
    </div>
    <div class="col-12">
        <x-campo name="descripcion" label="Descripción (opcional)" type="textarea" :value="$torneo->descripcion"
                 rows="4" maxlength="10000" help="Incluye reglas, ubicación u otros detalles. Máximo 10,000 caracteres." />
    </div>
</div>
