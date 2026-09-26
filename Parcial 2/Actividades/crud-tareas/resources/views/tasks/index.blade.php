<!doctype html>
<html lang="es" data-bs-theme="light">
    <head>
        <title>CRUD TAREAS - Mis tareas</title>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <!-- Bootstrap CSS v5.3.8 -->
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
            crossorigin="anonymous"
        />
    </head>

    <body>
        <nav class="navbar navbar-expand navbar-light" style="background-color: #e3f2fd;">
            <div class="container">
                <a class="navbar-brand fw-bold" href="{{ route('tasks.index') }}">CRUD TAREAS</a>
                <a class="btn btn-primary" href="{{ route('tasks.create') }}">Nueva tarea</a>
            </div>
        </nav>

        <main class="container py-5">
            <h1 class="mb-4">Mis tareas</h1>

            @if (session('exito'))
                <div class="alert alert-success" role="status">{{ session('exito') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-4">
                @foreach ($estados as $estado => $etiqueta)
                    <section class="col-md-4">
                        <h2 class="h4">{{ $etiqueta }}</h2>

                        @forelse ($tareasPorEstado->get($estado, collect()) as $tarea)
                            <x-task-card :tarea="$tarea" :prioridades="$prioridades" :estados="$estados" />
                        @empty
                            <p class="text-body-secondary">No hay tareas en este estado.</p>
                        @endforelse
                    </section>
                @endforeach
            </div>
        </main>

        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
            crossorigin="anonymous"
        ></script>
    </body>
</html>
