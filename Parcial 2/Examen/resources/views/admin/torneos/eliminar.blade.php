<div class="modal fade" id="eliminar-torneo-{{ $torneo->id }}" tabindex="-1"
     aria-labelledby="eliminar-titulo-{{ $torneo->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="eliminar-titulo-{{ $torneo->id }}">Eliminar torneo</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p class="text-break">¿Quieres eliminar el torneo <strong>{{ $torneo->nombre_torneo }}</strong>?</p>
                <p class="mb-0">También se eliminarán sus {{ $torneo->inscripciones_count }} inscripciones. Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="{{ route('admin.torneos.destroy', $torneo) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Sí, eliminar torneo</button>
                </form>
            </div>
        </div>
    </div>
</div>
