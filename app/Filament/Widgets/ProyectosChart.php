<?php

namespace App\Filament\Widgets;

use App\Models\Proyecto;
use Filament\Widgets\ChartWidget;

class ProyectosChart extends ChartWidget
{
    protected ?string $heading = 'Distribución de Proyectos por Estado';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $pendientes = Proyecto::where('estado', 'pendiente')->count();
        $enProgreso = Proyecto::where('estado', 'en_progreso')->count();
        $completados = Proyecto::where('estado', 'completado')->count();
        $cancelados = Proyecto::where('estado', 'cancelado')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Proyectos',
                    'data' => [$pendientes, $enProgreso, $completados, $cancelados],
                    'backgroundColor' => [
                        'rgb(156, 163, 175)', // gray - pendientes
                        'rgb(251, 191, 36)',  // amber - en progreso
                        'rgb(34, 197, 94)',   // green - completados
                        'rgb(239, 68, 68)',   // red - cancelados
                    ],
                ],
            ],
            'labels' => ['Pendientes', 'En Progreso', 'Completados', 'Cancelados'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
