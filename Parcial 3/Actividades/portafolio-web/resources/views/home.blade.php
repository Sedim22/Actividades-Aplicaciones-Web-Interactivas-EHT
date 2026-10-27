<x-layouts.app title="Inicio | Portafolio">
    <div class="space-y-16">
        <!-- Sección de Información General / Hero -->
        <section class="flex flex-col md:flex-row items-center md:items-start gap-8 pt-4">
            <div class="relative w-32 h-32 md:w-40 md:h-40 rounded-full overflow-hidden border-2 border-indigo-500/20 bg-zinc-200 dark:bg-zinc-800 shrink-0 shadow-lg">
                <img
                    src="https://estudiantes.ing.uaslp.mx/escolar/images/E2DEB5E1-51A0-4ED6-A049-3A471BB08B73.jpg"
                    alt="Foto de perfil"
                    class="w-full h-full object-cover"
                />
            </div>

            <div class="space-y-4 text-center md:text-left flex-1">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Disponible para nuevos proyectos
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
                    Desarrollador Web y Videojuegos
                </h1>

                <p class="text-base sm:text-lg text-zinc-600 dark:text-zinc-400 max-w-2xl leading-relaxed">
                   Desarrollador Web FrontEnd con experiencia en el desarrollo de aplicaciones web modernas, escalables y responsivas. 
                   Desarrollador de Videojuegos con experiencia en el desarrollo de videojuegos para PC.
                   Me apasiona resolver problemas complejos mediante interfaces intuitivas y arquitecturas limpias.
                </p>

                <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 pt-2">
                    <a
                        href="#skills"
                        class="px-5 py-2.5 rounded-lg bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 text-sm font-semibold hover:bg-zinc-800 dark:hover:bg-zinc-200 transition shadow-sm"
                    >
                        Ver Habilidades
                    </a>
                    <a
                        href="mailto:eduardo.hdztorre@hotmail.com"
                        class="px-5 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-700 text-sm font-medium hover:bg-zinc-100 dark:hover:bg-zinc-800/60 transition"
                    >
                        Contactar
                    </a>
                </div>
            </div>
        </section>

        <!-- Sección de Skills / Tecnologías -->
        <section id="skills" class="space-y-6 pt-8 border-t border-zinc-200 dark:border-zinc-800">
            <div class="space-y-2">
                <h2 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                    Tecnologías y Habilidades
                </h2>
            </div>

            <!-- Grid de Componentes Livewire de Skills -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                <livewire:skill-card name="C#" image="https://upload.wikimedia.org/wikipedia/commons/4/4f/Csharp_Logo.png?utm_source=es.wikipedia.org&utm_campaign=index&utm_content=original" />
                <livewire:skill-card name="ASP.NET CORE" image="https://cdn.jsdelivr.net/gh/selfhst/icons/png/asp-net-core.png" />
                <livewire:skill-card name="Javascript" image="https://upload.wikimedia.org/wikipedia/commons/6/6a/JavaScript-logo.png?utm_source=commons.wikimedia.org&utm_campaign=index&utm_content=original" />
                <livewire:skill-card name="React" image="https://cdn.freebiesupply.com/logos/large/2x/react-1-logo-png-transparent.png" />
                <livewire:skill-card name="Tailwind" image="https://images.seeklogo.com/logo-png/35/1/tailwind-css-logo-png_seeklogo-354675.png" />
                <livewire:skill-card name="Godot" image="https://godotengine.org/assets/press/icon_color.png" />
            </div>
        </section>
    </div>
</x-layouts.app>

