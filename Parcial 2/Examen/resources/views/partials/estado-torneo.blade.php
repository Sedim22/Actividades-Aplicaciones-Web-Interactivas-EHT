@switch($torneo->estadoActual())
    @case('abierto')
        <span class="badge text-bg-success">Abierto</span>
        @break
    @case('lleno')
        <span class="badge text-bg-warning">Lleno</span>
        @break
    @case('vencido')
        <span class="badge text-bg-secondary">Cerrado por fecha</span>
        @break
    @default
        <span class="badge text-bg-secondary">Cerrado</span>
@endswitch
