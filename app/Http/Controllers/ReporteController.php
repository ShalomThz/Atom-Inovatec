<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReporteController extends Controller
{
    public function generarPdfProyectos()
    {
        $user = Auth::user();
        $query = Proyecto::with(['tareas', 'usuario']);

        // Aplicar filtros según el rol del usuario
        if ($user->hasRole('super_admin')) {
            // Super admin ve todos los proyectos
        } elseif ($user->hasRole('lider_proyecto')) {
            // Líder ve solo sus proyectos
            $query->where('user_id', $user->id);
        } elseif ($user->hasRole('desarrollador')) {
            // Desarrollador ve proyectos donde tiene tareas asignadas
            $query->whereHas('tareas', function (Builder $q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } else {
            // Si no tiene rol, no ver nada
            $query->whereRaw('1 = 0');
        }

        $proyectos = $query
            ->orderBy('estado', 'asc')
            ->orderBy('prioridad', 'desc')
            ->get()
            ->map(fn ($proyecto) => [
                'id' => $proyecto->id,
                'nombre' => $proyecto->nombre,
                'descripcion' => $proyecto->descripcion,
                'lider' => $proyecto->usuario?->name,
                'estado' => $proyecto->estado,
                'prioridad' => $proyecto->prioridad,
                'fecha_inicio' => $proyecto->fecha_inicio?->format('d/m/Y'),
                'fecha_fin' => $proyecto->fecha_fin?->format('d/m/Y'),
                'presupuesto' => $proyecto->presupuesto,
                'progreso_general' => $proyecto->progreso_general,
                'porcentaje_completadas' => $proyecto->porcentaje_tareas_completadas,
                'esta_retrasado' => $proyecto->esta_retrasado,
                'estadisticas' => $proyecto->tareas_estadisticas,
            ]);

        $pdf = Pdf::loadView('pdf.reporte-proyectos', [
            'proyectos' => $proyectos,
            'fecha_generacion' => now()->format('d/m/Y H:i:s'),
            'usuario' => $user->name,
        ]);

        return $pdf->download('reporte-proyectos-' . now()->format('Y-m-d') . '.pdf');
    }
}
