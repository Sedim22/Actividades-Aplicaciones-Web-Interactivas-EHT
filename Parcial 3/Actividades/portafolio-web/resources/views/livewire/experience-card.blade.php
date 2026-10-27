<div class="relative pl-8 sm:pl-10 group">
    {{-- Línea vertical y punto de timeline --}}
    <div class="absolute left-0 top-1.5 w-3.5 h-3.5 rounded-full bg-blue-600 ring-4 ring-blue-100 dark:ring-blue-950/60 shadow-xs"></div>
    <div class="absolute left-[6.5px] top-5 -bottom-8 w-0.5 bg-zinc-200 dark:bg-zinc-800 group-last:hidden"></div>

    {{-- Contenido de la experiencia --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-xs hover:border-zinc-300 dark:hover:border-zinc-700 transition duration-200 space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 sm:gap-4">
            {{-- Nombre del rol: en negritas y letra grande --}}
            <h3 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
                {{ $role }}
            </h3>

            {{-- Fecha --}}
            <span class="inline-flex items-center text-xs sm:text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 px-3 py-1 rounded-full w-fit">
                {{ $date }}
            </span>
        </div>

        {{-- Descripción --}}
        <p class="text-sm sm:text-base text-zinc-600 dark:text-zinc-400 leading-relaxed">
            {{ $description }}
        </p>
    </div>
</div>
