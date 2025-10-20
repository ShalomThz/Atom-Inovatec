<x-filament-panels::page>
    {{-- Contenedor del Kanban Board --}}
    <div id="kanban-board-root"
         data-tareas="{{ json_encode($tareas) }}"
         class="kanban-container">
        {{-- Estado de carga mientras React se inicializa --}}
        <div class="flex items-center justify-center p-8">
            <div class="text-gray-500">Cargando tablero Kanban...</div>
        </div>
    </div>

    @push('scripts')
    @vite('resources/js/kanban-init.tsx')
    @endpush

    @push('styles')
    @vite('resources/css/kanban.css')
    @endpush
</x-filament-panels::page>
