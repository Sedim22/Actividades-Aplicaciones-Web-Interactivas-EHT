<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mi recetario') · Recetario casero</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col">
    <a href="#contenido" class="sr-only focus:not-sr-only focus:absolute focus:z-10 focus:bg-white focus:p-4">Saltar al contenido</a>
    <header class="border-b border-stone-200 bg-white">
        <nav aria-label="Navegación principal" class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-5 py-5 sm:px-8">
            <a href="{{ route('inicio') }}" class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-800 text-white">
                    <svg aria-hidden="true" width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 5c-3-2-6-2-9-1v15c3-1 6-1 9 1 3-2 6-2 9-1V4c-3-1-6-1-9 1Zm0 0v15M6 8h3M6 12h3m6-4h3m-3 4h3"/></svg>
                </span>
                <span class="font-serif text-xl font-bold tracking-tight">Recetario <span class="font-normal text-emerald-800">casero</span></span>
            </a>
            <div class="flex flex-wrap items-center gap-3 text-sm sm:gap-5">
                @auth
                    <a href="{{ route('recetas.index') }}" class="text-link">Mis recetas</a>
                    <span class="max-w-40 truncate text-stone-500" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="btn btn-secondary" type="submit">Cerrar sesión</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-link">Iniciar sesión</a>
                    <a href="{{ route('register') }}" class="btn btn-secondary">Crear cuenta</a>
                @endauth
            </div>
        </nav>
    </header>

    <main id="contenido" class="mx-auto w-full max-w-6xl flex-1 px-5 py-8 sm:px-8 sm:py-12">
        @if (session('success'))
            <div role="status" class="mb-7 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-900">{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>

    <footer class="mx-auto w-full max-w-6xl px-5 pb-7 text-sm text-stone-500 sm:px-8">
        <div class="flex flex-wrap justify-between gap-2 border-t border-stone-200 pt-5">
            <span>Recetario casero</span>
            <span>Las recetas que quieres volver a preparar.</span>
        </div>
    </footer>
</body>
</html>
