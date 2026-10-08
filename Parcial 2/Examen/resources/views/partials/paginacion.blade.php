@if ($paginator->hasPages())
    <nav class="d-flex flex-wrap justify-content-between align-items-center gap-2" aria-label="Páginas de torneos">
        <p class="small text-body-secondary mb-0">Mostrando {{ $paginator->firstItem() }} a {{ $paginator->lastItem() }} de {{ $paginator->total() }} torneos</p>
        <ul class="pagination mb-0">
            @if ($paginator->onFirstPage())
                <li class="page-item disabled"><span class="page-link">Anterior</span></li>
            @else
                <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">Anterior</a></li>
            @endif
            <li class="page-item active" aria-current="page"><span class="page-link">{{ $paginator->currentPage() }}</span></li>
            @if ($paginator->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Siguiente</a></li>
            @else
                <li class="page-item disabled"><span class="page-link">Siguiente</span></li>
            @endif
        </ul>
    </nav>
@endif
