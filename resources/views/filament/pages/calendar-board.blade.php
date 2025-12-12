<x-filament-panels::page>
    <div id="calendar-view-root"
         data-tareas='@json($tareas)'
         class="calendar-view-container">
        <div class="flex items-center justify-center p-8">
            <div class="text-gray-500">Cargando calendario...</div>
        </div>
    </div>

    @push('scripts')
    @vite('resources/js/calendar-init.tsx')
    @endpush

    @push('styles')
    @vite('resources/css/calendar.css')
    @endpush
</x-filament-panels::page>
