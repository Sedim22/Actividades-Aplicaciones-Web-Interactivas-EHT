<div class="modal fade" id="baja-inscripcion-{{ $inscripcion->id }}" tabindex="-1"
     aria-labelledby="baja-titulo-{{ $inscripcion->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="baja-titulo-{{ $inscripcion->id }}">Dar de baja la inscripción</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p class="text-break">¿Quieres dar de baja a <strong>{{ $inscripcion->user->name }}</strong> de <strong>{{ $torneo->nombre_torneo }}</strong>?</p>
                <p class="mb-0">La inscripción se eliminará y su plaza quedará libre.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="{{ route('admin.torneos.inscripciones.destroy', [$torneo, $inscripcion]) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Sí, dar de baja</button>
                </form>
            </div>
        </div>
    </div>
</div>
