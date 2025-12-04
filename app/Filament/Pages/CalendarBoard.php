<?php

namespace App\Filament\Pages;

use App\Models\Tarea;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CalendarBoard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static ?string $navigationLabel = 'Calendario de Tareas';

    protected static ?string $title = 'Calendario de Tareas';

    protected string $view = 'filament.pages.calendar-board';

    public function getTareas()
    {
        $user = Auth::user();
        $query = Tarea::with(['proyecto', 'usuario']);

        // Aplicar filtros según el rol del usuario
        if ($user->hasRole('super_admin')) {
            // Super admin ve todas las tareas
        } elseif ($user->hasRole('lider_proyecto')) {
            // Líder ve tareas de los proyectos que él creó
            $query->whereHas('proyecto', function (Builder $q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } elseif ($user->hasRole('desarrollador')) {
            // Desarrollador ve solo sus propias tareas
            $query->where('user_id', $user->id);
        } else {
            // Si no tiene rol, no ver nada
            $query->whereRaw('1 = 0');
        }

        // Filtrar solo tareas que tengan fecha de inicio o fecha de fin
        $query->where(function (Builder $q) {
            $q->whereNotNull('fecha_inicio')
              ->orWhereNotNull('fecha_fin');
        });

        return $query
            ->orderBy('fecha_inicio', 'asc')
            ->orderBy('fecha_fin', 'asc')
            ->get()
            ->map(fn ($tarea) => [
                'id' => $tarea->id,
                'nombre' => $tarea->nombre,
                'descripcion' => $tarea->descripcion,
                'proyecto' => $tarea->proyecto?->nombre,
                'asignado' => $tarea->usuario?->name,
                'prioridad' => $tarea->prioridad,
                'progreso' => $tarea->progreso,
                'fecha_inicio' => $tarea->fecha_inicio?->format('Y-m-d'),
                'fecha_fin' => $tarea->fecha_fin?->format('Y-m-d'),
                'estado' => $tarea->estado,
            ])
            ->values();
    }

    protected function getViewData(): array
    {
        return [
            'tareas' => $this->getTareas(),
        ];
    }
}
