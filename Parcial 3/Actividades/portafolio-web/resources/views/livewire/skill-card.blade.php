<div class="flex items-center gap-3 p-3.5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xs hover:border-zinc-300 dark:hover:border-zinc-700 transition duration-200">
    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 shrink-0 overflow-hidden">
        @if ($image)
            <img src="{{ $image }}" alt="{{ $name }}" class="w-7 h-7 object-contain" loading="lazy" />
        @else
            {{-- Placeholder para cuando no se provee imagen --}}
            <svg class="w-5 h-5 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
            </svg>
        @endif
    </div>
    <span class="text-sm font-medium text-zinc-800 dark:text-zinc-200">
        {{ $name }}
    </span>
</div>
