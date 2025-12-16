<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-4">
            <div class="text-center">
                <h3 class="text-lg font-semibold mb-2">
                    📊 Reportes de Proyectos
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Descarga el reporte general de todos los proyectos según tus permisos.
                </p>
            </div>

            <div class="flex items-center justify-center pt-4">
                <x-filament::button
                    wire:click="descargarReporteGeneral"
                    color="primary"
                    size="lg"
                    icon="heroicon-o-document-arrow-down"
                >
                    Descargar Reporte General
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
