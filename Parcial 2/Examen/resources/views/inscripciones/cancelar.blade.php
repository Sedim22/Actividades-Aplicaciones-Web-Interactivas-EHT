<div class="modal fade" id="cancelar-inscripcion-{{ $torneo->id }}" tabindex="-1"
     aria-labelledby="cancelar-titulo-{{ $torneo->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="cancelar-titulo-{{ $torneo->id }}">Cancelar inscripción</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p class="text-break">¿Quieres cancelar tu inscripción en <strong>{{ $torneo->nombre_torneo }}</strong>?</p>
                <p class="mb-0">Tu plaza quedará libre. Si quieres volver a inscribirte, el torneo deberá estar disponible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Conservar inscripción</button>
                <form method="POST" action="{{ route('inscripciones.destroy', $torneo) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Sí, cancelar inscripción</button>
                </form>
            </div>
        </div>
    </div>
</div>
