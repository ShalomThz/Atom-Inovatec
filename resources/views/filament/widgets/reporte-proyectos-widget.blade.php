<x-filament-widgets::widget>
    <x-filament::section>
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
            <div style="flex: 1;">
                <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.25rem;">
                    📊 Reporte de Proyectos
                </h3>
                <p style="font-size: 0.875rem; opacity: 0.7;">
                    Genera un reporte en PDF con el estado y progreso de todos tus proyectos.
                </p>
            </div>
            <div>
                <x-filament::button
                    href="{{ route('reporte.proyectos.pdf') }}"
                    target="_blank"
                    tag="a"
                    icon="heroicon-o-arrow-down-tray"
                    color="primary"
                >
                    Descargar Reporte PDF
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
