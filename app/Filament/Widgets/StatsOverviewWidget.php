<?php

namespace App\Filament\Widgets;

use App\Models\Proyecto;
use App\Models\Tarea;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseStatsOverviewWidget
{
    protected function getStats(): array
    {
        $proyectosTotal = Proyecto::count();
        $proyectosEnProgreso = Proyecto::where('estado', 'en_progreso')->count();
        $proyectosCompletados = Proyecto::where('estado', 'completado')->count();

        $tareasTotal = Tarea::count();
        $tareasPendientes = Tarea::where('estado', 'pendiente')->count();
        $tareasCompletadas = Tarea::where('estado', 'completada')->count();

        $usuariosTotal = User::count();

        return [
            Stat::make('Total de Proyectos', $proyectosTotal)
                ->description($proyectosEnProgreso . ' en progreso')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->color('primary'),

            Stat::make('Proyectos Completados', $proyectosCompletados)
                ->description('Del total de proyectos')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Total de Tareas', $tareasTotal)
                ->description($tareasPendientes . ' pendientes')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->chart([3, 5, 7, 9, 6, 4, 8, 10])
                ->color('warning'),

            Stat::make('Tareas Completadas', $tareasCompletadas)
                ->description('Del total de tareas')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Usuarios Activos', $usuariosTotal)
                ->description('En el sistema')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            Stat::make('Tasa de Completación',
                $proyectosTotal > 0
                    ? round(($proyectosCompletados / $proyectosTotal) * 100, 1) . '%'
                    : '0%'
            )
                ->description('De proyectos')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success'),
        ];
    }
}
