<div class="group flex flex-col bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl overflow-hidden shadow-xs hover:border-zinc-300 dark:hover:border-zinc-700 hover:shadow-md transition-all duration-300">
    {{-- Imagen del proyecto (opcional) --}}
    <div class="relative w-full h-48 bg-zinc-100 dark:bg-zinc-800/80 overflow-hidden flex items-center justify-center">
        @if ($image)
            <img
                src="{{ $image }}"
                alt="{{ $name }}"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                loading="lazy"
            />
        @else
            {{-- Placeholder elegante cuando no se incluye imagen --}}
            <div class="flex flex-col items-center gap-2 text-zinc-400 dark:text-zinc-500">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-xs uppercase tracking-wider font-medium">Sin captura previa</span>
            </div>
        @endif
    </div>

    {{-- Contenido del proyecto --}}
    <div class="flex flex-col flex-1 p-6 space-y-4">
        <div class="flex items-start justify-between gap-2">
            <h3 class="text-xl font-bold text-zinc-900 dark:text-zinc-50 tracking-tight">
                {{ $name }}
            </h3>

            @if ($url)
                <a
                    href="{{ $url }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 transition shrink-0 p-1 -m-1"
                    title="Visitar proyecto"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            @endif
        </div>

        <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed flex-1">
            {{ $description }}
        </p>

        {{-- Tecnologías usadas --}}
        @if (!empty($technologies))
            <div class="pt-2">
                <div class="flex flex-wrap gap-2">
                    @foreach ($technologies as $tech)
                        <span class="inline-flex items-center px-3 py-1 rounded-md text-xs sm:text-sm font-semibold bg-blue-600 text-white shadow-xs">
                            {{ $tech }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
