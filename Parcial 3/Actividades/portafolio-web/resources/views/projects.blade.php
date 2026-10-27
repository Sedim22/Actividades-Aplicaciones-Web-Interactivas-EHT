<x-layouts.app title="Proyectos | Portafolio">
    <div class="space-y-10">
        <!-- Encabezado de la Sección de Proyectos -->
        <div class="space-y-2">
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
                Mis Proyectos
            </h1>
            <p class="text-base text-zinc-600 dark:text-zinc-400 max-w-2xl">
                Una recopilación de aplicaciones, herramientas y soluciones que he diseñado y construido recientemente.
            </p>
        </div>

        <!-- Grid de Proyectos con Livewire ProjectCard -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <livewire:project-card
                name="Escape The Dungeon"
                description="Juego Dungeon Crawler desarrollado en el framework Raylib para la game jam Dungeon Crawler Jam 2026"
                :technologies="['C#','Raylib']"
                url="https://github.com/sedim1/Escape-The-Dungeon"
                image="https://github.com/sedim1/Escape-The-Dungeon/raw/master/Screenshots/screenshot1.png"
            />

             <livewire:project-card
                name="SPACE DASH"
                description="Juego desarrollado en Godot para la game ham Code For A Cause Jam 2025"
                :technologies="['Godot']"
                url="https://github.com/sedim1/SPACE-DASH"
            />

             
    </div>
</x-layouts.app>

