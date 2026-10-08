<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Inicio') | Torneos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body class="bg-body-tertiary d-flex flex-column min-vh-100">
    <a href="#contenido" class="visually-hidden-focusable p-3">Saltar al contenido</a>
    <nav class="navbar navbar-expand-lg bg-dark" data-bs-theme="dark" aria-label="Navegación principal">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('inicio') }}">Torneos</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navegacion"
                    aria-controls="navegacion" aria-expanded="false" aria-label="Abrir menú">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navegacion">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('torneos.index') }}">Torneos</a></li>
                    @auth
                        <li class="nav-item"><a class="nav-link" href="{{ route('panel') }}">Mi panel</a></li>
                        @if (auth()->user()->rol === \App\Models\User::ROL_ADMIN)
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.torneos.index') }}">Gestionar torneos</a></li>
                        @elseif (auth()->user()->rol === \App\Models\User::ROL_JUGADOR)
                            <li class="nav-item"><a class="nav-link" href="{{ route('inscripciones.index') }}">Mis torneos</a></li>
                        @endif
                    @endauth
                </ul>
                <div class="d-flex flex-wrap align-items-center gap-2 py-2 py-lg-0">
                    @guest
                        <a class="btn btn-outline-light btn-sm" href="{{ route('login') }}">Iniciar sesión</a>
                        <a class="btn btn-primary btn-sm" href="{{ route('registro') }}">Crear cuenta</a>
                    @else
                        <span class="navbar-text me-2 text-break">{{ auth()->user()->name }}</span>
                        <span class="badge text-bg-primary">{{ \App\Models\User::ROLES[auth()->user()->rol] ?? 'Sin rol' }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm">Cerrar sesión</button>
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <main id="contenido" class="container py-4 py-md-5 flex-grow-1">
        @if (session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>

    <footer class="border-top bg-white py-3">
        <div class="container text-body-secondary small">Sistema de torneos</div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
