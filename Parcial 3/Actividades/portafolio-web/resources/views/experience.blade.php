<x-layouts.app title="Experiencia | Portafolio">
    <div class="space-y-10">
        
        <div class="space-y-2">
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
                Experiencia Laboral
            </h1>
        </div>

    
        <div class="space-y-8 max-w-3xl">
            
        <livewire:experience-card
                role="Hackathon PHINIA - Desarrollador Front End"
                date="Junio 2026"
                description="Desarrollo e implementación de interfaces gráficas para un sistema de predicción de precios en tiempo real, integrandolo a una api backend de ML a través de la tecnoogía ASP.Net Core."
            />

         <livewire:experience-card
                role="Hackathon IA UASLP"
                date="Marzo 2026"
                description="Desarrollo e implementación de interfaces gráficas para un sistema de bolsa de trabajo para estudiantes, egresados y empleadores con React y Tailwind CSS."
            />
            
        </div>
    </div>
</x-layouts.app>

