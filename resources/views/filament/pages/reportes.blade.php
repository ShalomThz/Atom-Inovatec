<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">
                📊 Filtros de Reporte
            </x-slot>
            
            <x-slot name="description">
                Selecciona los filtros que desees aplicar al reporte de proyectos y tareas.
            </x-slot>

            <form wire:submit.prevent>
                {{ $this->form }}
            </form>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                💡 Ayuda
            </x-slot>

            <div class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                <p><strong>Filtros de Proyecto:</strong> Filtra por proyecto específico, líder, estado, prioridad y fechas.</p>
                <p><strong>Filtros de Tareas:</strong> Filtra proyectos que contengan tareas asignadas a empleados específicos con cierto estado o prioridad.</p>
                <p><strong>Filtros Avanzados:</strong> Filtra por progreso, proyectos con retraso, y rangos de fechas.</p>
                <p><strong>Nota:</strong> Si no seleccionas ningún filtro, se generará un reporte completo según tu rol y permisos.</p>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
