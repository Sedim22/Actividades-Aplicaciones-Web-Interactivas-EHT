<!doctype html>
<html lang="es" data-bs-theme="light">
    <head>
        <title>CRUD TAREAS - Crear Tareas</title>
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
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    </head>

    <body>
       <nav class="navbar navbar-expand navbar-light" style="background-color: #e3f2fd;">
        <div class="nav navbar-nav">
            <a class="nav-item nav-link active" href="{{ route('tasks.index') }}" aria-current="page"
                > <h2 class="fw-bold">CRUD TAREAS</h2>  <span class="visually-hidden">(current)</span></a
            >
        </div>
       </nav>

       <div class="container p-5">
            <h2 class="fw-bold">Nueva Tarea</h2>
            <div class="w-75 shadow p-3">
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('tasks.store') }}">
                    @csrf

                    <div class="mb-3 form-row">
                        <label for="titulo" class="form-label">Título</label>
                        <input
                            type="text"
                            class="form-control"
                            name="titulo"
                            id="titulo"
                            value="{{ old('titulo') }}"
                            maxlength="255"
                            required
                        />
                    </div>
                    <div class="form-group form-row">
                        <label for="descripcion">Descripción (OPCIONAL)</label>
                        <textarea class="form-control" name="descripcion" id="descripcion" rows="3" maxlength="2000">{{ old('descripcion') }}</textarea>
                    </div>
                    <div class="form-row">
                        <div class="mb-3 col">
                            <label for="estado" class="form-label">Estado</label>
                            <select
                                class="form-select form-select-lg"
                                name="estado"
                                id="estado"
                                required
                            >
                                @foreach ($estados as $valor => $etiqueta)
                                    <option value="{{ $valor }}" @selected(old('estado', 'por_hacer') === $valor)>{{ $etiqueta }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col">
                            <label for="prioridad" class="form-label">Prioridad</label>
                            <select
                                class="form-select form-select-lg"
                                name="prioridad"
                                id="prioridad"
                                required
                            >
                                @foreach ($prioridades as $valor => $etiqueta)
                                    <option value="{{ $valor }}" @selected(old('prioridad', 'media') === $valor)>{{ $etiqueta }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col">
                            <label for="vencimiento" class="form-label">Fecha de vencimiento (OPCIONAL)</label>
                            <input
                                type="date"
                                class="form-control"
                                name="vencimiento"
                                id="vencimiento"
                                value="{{ old('vencimiento') }}"
                            />
                        </div>
                    </div>
                    <div class="form-row">
                        <button type="submit" class="btn btn-primary">Crear Tarea</button>
                    </div>
                </form>
            </div>
       </div>
       

        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
            crossorigin="anonymous"
        ></script>
    </body>
</html>
