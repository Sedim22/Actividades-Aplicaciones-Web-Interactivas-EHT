<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <h2 class="h5 mb-3">Datos de tu cuenta</h2>
        <dl class="row mb-0">
            <dt class="col-sm-4">Nombre</dt>
            <dd class="col-sm-8 text-break">{{ auth()->user()->name }}</dd>
            <dt class="col-sm-4">Correo electrónico</dt>
            <dd class="col-sm-8 text-break">{{ auth()->user()->email }}</dd>
            <dt class="col-sm-4">Rol</dt>
            <dd class="col-sm-8 mb-0"><span class="badge text-bg-primary">{{ \App\Models\User::ROLES[auth()->user()->rol] }}</span></dd>
        </dl>
    </div>
</div>
