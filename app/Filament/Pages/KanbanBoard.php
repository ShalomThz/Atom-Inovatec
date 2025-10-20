<?php

namespace App\Filament\Pages;

use App\Models\Tarea;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class KanbanBoard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedViewColumns;

    protected static ?string $navigationLabel = 'Tablero Kanban';

    protected static ?string $title = 'Tablero Kanban';

    protected string $view = 'filament.pages.kanban-board';

    public function getTareas()
    {
        return Tarea::with(['proyecto', 'asignado'])
            ->orderBy('prioridad', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('estado')
            ->map(fn ($tareas) => $tareas->map(fn ($tarea) => [
                'id' => $tarea->id,
                'nombre' => $tarea->nombre,
                'descripcion' => $tarea->descripcion,
                'proyecto' => $tarea->proyecto?->nombre,
                'asignado' => $tarea->asignado?->name,
                'prioridad' => $tarea->prioridad,
                'progreso' => $tarea->progreso,
                'fecha_fin' => $tarea->fecha_fin?->format('d/m/Y'),
                'estado' => $tarea->estado,
            ]));
    }

    public function updateTareaEstado($tareaId, $nuevoEstado)
    {
        try {
            $tarea = Tarea::findOrFail($tareaId);

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
