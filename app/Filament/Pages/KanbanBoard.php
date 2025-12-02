<?php

namespace App\Filament\Pages;

use App\Models\Tarea;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class KanbanBoard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedViewColumns;

    protected static ?string $navigationLabel = 'Tablero Kanban';

    protected static ?string $title = 'Tablero Kanban';

    protected string $view = 'filament.pages.kanban-board';

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

        return $query
            ->orderBy('prioridad', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('estado')
            ->map(fn ($tareas) => $tareas->map(fn ($tarea) => [
                'id' => $tarea->id,
                'nombre' => $tarea->nombre,
                'descripcion' => $tarea->descripcion,
                'proyecto' => $tarea->proyecto?->nombre,
                'asignado' => $tarea->usuario?->name,
                'prioridad' => $tarea->prioridad,
                'progreso' => $tarea->progreso,
                'fecha_fin' => $tarea->fecha_fin?->format('d/m/Y'),
                'estado' => $tarea->estado,
            ]));
    }

    public function updateTareaEstado($tareaId, $nuevoEstado)
    {
        try {
            $tarea = Tarea::with('proyecto')->findOrFail($tareaId);
            $user = Auth::user();

            // Verificar permisos según el rol
            $puedeActualizar = false;

            if ($user->hasRole('super_admin')) {
                // Super admin puede actualizar cualquier tarea
                $puedeActualizar = true;
            } elseif ($user->hasRole('lider_proyecto')) {
                // Líder puede actualizar tareas de sus proyectos
                $puedeActualizar = $tarea->proyecto && $tarea->proyecto->user_id === $user->id;
            } elseif ($user->hasRole('desarrollador')) {
                // Desarrollador solo puede actualizar sus propias tareas
                $puedeActualizar = $tarea->user_id === $user->id;
            }

            if (!$puedeActualizar) {
                return [
                    'success' => false,
                    'message' => 'No tiene permisos para actualizar esta tarea'
                ];
            }

            // Asegurar que el estado sea una cadena válida
            $estadoValido = (string) $nuevoEstado;

            $tarea->estado = $estadoValido;

            // Si se mueve a completada, actualizar progreso al 100%
            if ($estadoValido === 'completada') {
                $tarea->progreso = 100;
            } elseif ($estadoValido === 'en_progreso' && $tarea->progreso === 0) {
                $tarea->progreso = 10;
            }

            $tarea->save();

            // No hacer dispatch para evitar recargas de Livewire
            // Return success response
            return [
                'success' => true,
                'message' => 'Tarea actualizada correctamente'
            ];

        } catch (\Exception $e) {
            \Log::error('Error actualizando tarea', [
                'id' => $tareaId,
                'estado' => $nuevoEstado,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    protected function getViewData(): array
    {
        return [
            'tareas' => $this->getTareas(),
        ];
    }
}
