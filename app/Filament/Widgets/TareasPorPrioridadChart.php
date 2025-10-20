<?php

namespace App\Filament\Widgets;

use App\Models\Tarea;
use Filament\Widgets\ChartWidget;

class TareasPorPrioridadChart extends ChartWidget
{
    protected ?string $heading = 'Tareas por Prioridad';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $baja = Tarea::where('prioridad', 1)->count();
        $media = Tarea::where('prioridad', 2)->count();
        $alta = Tarea::where('prioridad', 3)->count();
        $urgente = Tarea::where('prioridad', 4)->count();

        return [
            'datasets' => [
                [
                    'label' => 'Número de Tareas',
                    'data' => [$baja, $media, $alta, $urgente],
                    'backgroundColor' => [
                        'rgb(34, 197, 94)',   // green - baja
                        'rgb(59, 130, 246)',  // blue - media
                        'rgb(251, 191, 36)',  // amber - alta
                        'rgb(239, 68, 68)',   // red - urgente
                    ],
                    'borderColor' => [
                        'rgb(34, 197, 94)',
                        'rgb(59, 130, 246)',
                        'rgb(251, 191, 36)',
                        'rgb(239, 68, 68)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Baja', 'Media', 'Alta', 'Urgente'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
