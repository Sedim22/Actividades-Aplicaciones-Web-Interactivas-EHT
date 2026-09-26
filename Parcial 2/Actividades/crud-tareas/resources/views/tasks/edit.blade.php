<!doctype html>
<html lang="es" data-bs-theme="light">
    <head>
        <title>CRUD TAREAS - Editar tarea</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

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
            </div>
        </nav>

        <main class="container py-5">
            <h1 class="mb-4">Editar tarea</h1>

            <div class="col-lg-9 shadow p-3">
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('tasks.update', $tarea) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título</label>
                        <input
                            type="text"
                            class="form-control"
                            name="titulo"
                            id="titulo"
                            value="{{ old('titulo', $tarea->titulo) }}"
                            maxlength="255"
                            required
                        />
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción (OPCIONAL)</label>
                        <textarea class="form-control" name="descripcion" id="descripcion" rows="3" maxlength="2000">{{ old('descripcion', $tarea->descripcion) }}</textarea>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" name="estado" id="estado" required>
                                @foreach ($estados as $valor => $etiqueta)
                                    <option value="{{ $valor }}" @selected(old('estado', $tarea->estado) === $valor)>{{ $etiqueta }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="prioridad" class="form-label">Prioridad</label>
                            <select class="form-select" name="prioridad" id="prioridad" required>
                                @foreach ($prioridades as $valor => $etiqueta)
                                    <option value="{{ $valor }}" @selected(old('prioridad', $tarea->prioridad) === $valor)>{{ $etiqueta }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="vencimiento" class="form-label">Fecha de vencimiento (OPCIONAL)</label>
                        <input
                            type="date"
                            class="form-control"
                            name="vencimiento"
                            id="vencimiento"
                            value="{{ old('vencimiento', $tarea->vencimiento?->format('Y-m-d')) }}"
                        />
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        <a class="btn btn-outline-secondary" href="{{ route('tasks.index') }}">Cancelar</a>
                    </div>
                </form>
            </div>
        </main>
    </body>
</html>
