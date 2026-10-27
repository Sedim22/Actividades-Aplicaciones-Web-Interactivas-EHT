<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Portafolio' }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-zinc-50 text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100 flex flex-col antialiased selection:bg-indigo-500 selection:text-white">
        <!-- Barra de Navegación -->
        <header class="sticky top-0 z-40 backdrop-blur-md bg-white/80 dark:bg-zinc-950/80 border-b border-zinc-200 dark:border-zinc-800">
            <div class="max-w-5xl mx-auto px-6 h-16 flex items-center justify-between">
                <a href="{{ route('home') }}" class="font-bold text-lg tracking-tight hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                    Eduardo Hernández Torres
                </a>
                <nav class="flex items-center gap-6 text-sm font-medium">
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition' }}">
                        Inicio
                    </a>
                    <a href="{{ route('experience') }}" class="{{ request()->routeIs('experience') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition' }}">
                        Experiencia
                    </a>
                    <a href="{{ route('projects') }}" class="{{ request()->routeIs('projects') ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition' }}">
                        Proyectos
                    </a>
                </nav>
            </div>
        </header>

        <!-- Contenido Principal -->
        <main class="flex-1 max-w-5xl w-full mx-auto px-6 py-12">
            {{ $slot }}
        </main>

        <!-- Pie de Página -->
        <footer class="border-t border-zinc-200 dark:border-zinc-800 py-6 text-center text-xs text-zinc-500 dark:text-zinc-400">
            <p>© {{ date('Y') }} Mi Portafolio. Construido con Laravel, Livewire y Tailwind CSS.</p>
        </footer>

        @livewireScripts
    </body>
</html>
