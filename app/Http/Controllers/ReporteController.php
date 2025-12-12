<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Tarea;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function generarPdfProyectos()
    {
        $user = Auth::user();
        $query = Proyecto::with(['tareas.usuario', 'usuario']);

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
            ->get();

        $proyectosData = $proyectos->map(fn ($proyecto) => [
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

        // === ESTADÍSTICAS GENERALES ===
        $totalProyectos = $proyectos->count();
        $proyectosCompletados = $proyectos->where('estado', 'completado')->count();
        $proyectosEnProgreso = $proyectos->where('estado', 'en_progreso')->count();
        $proyectosPendientes = $proyectos->where('estado', 'pendiente')->count();
        $proyectosAtrasados = $proyectos->filter(fn($p) => $p->esta_retrasado)->count();

        // Calcular todas las tareas de los proyectos filtrados
        $todasLasTareas = $proyectos->flatMap(fn($p) => $p->tareas);
        $totalTareas = $todasLasTareas->count();
        $tareasCompletadas = $todasLasTareas->where('estado', 'completada')->count();
        $tareasPendientes = $todasLasTareas->where('estado', 'pendiente')->count();
        $tareasEnProgreso = $todasLasTareas->where('estado', 'en_progreso')->count();

        // === PROYECTOS ATRASADOS ===
        $proyectosAtrasadosDetalle = $proyectos->filter(fn($p) => $p->esta_retrasado)
            ->map(fn($p) => [
                'nombre' => $p->nombre,
                'lider' => $p->usuario?->name,
                'fecha_fin' => $p->fecha_fin?->format('d/m/Y'),
                'progreso' => $p->progreso_general,
                'dias_retraso' => (int) now()->diffInDays($p->fecha_fin),
            ])
            ->values();

        // === EMPLEADOS CON TAREAS ===
        $empleadosStats = User::whereHas('tareas', function($q) use ($proyectos) {
                $proyectoIds = $proyectos->pluck('id');
                $q->whereIn('proyecto_id', $proyectoIds);
            })
            ->withCount([
                'tareas as total_tareas' => function($q) use ($proyectos) {
                    $q->whereIn('proyecto_id', $proyectos->pluck('id'));
                },
                'tareas as tareas_completadas' => function($q) use ($proyectos) {
                    $q->whereIn('proyecto_id', $proyectos->pluck('id'))
                      ->where('estado', 'completada');
                },
                'tareas as tareas_pendientes' => function($q) use ($proyectos) {
                    $q->whereIn('proyecto_id', $proyectos->pluck('id'))
                      ->where('estado', 'pendiente');
                },
                'tareas as tareas_atrasadas' => function($q) use ($proyectos) {
                    $q->whereIn('proyecto_id', $proyectos->pluck('id'))
                      ->where('estado', '!=', 'completada')
                      ->where('fecha_fin', '<', now());
                },
            ])
            ->get()
            ->map(fn($emp) => [
                'nombre' => $emp->name,
                'email' => $emp->email,
                'rol' => $emp->roles->pluck('name')->first() ?? 'Sin rol',
                'total_tareas' => $emp->total_tareas,
                'tareas_completadas' => $emp->tareas_completadas,
                'tareas_pendientes' => $emp->tareas_pendientes,
                'tareas_atrasadas' => $emp->tareas_atrasadas,
                'porcentaje_completadas' => $emp->total_tareas > 0
                    ? round(($emp->tareas_completadas / $emp->total_tareas) * 100, 1)
                    : 0,
            ]);

        // Empleados destacados (100% completadas o >80%)
        $empleadosDestacados = $empleadosStats
            ->filter(fn($e) => $e['porcentaje_completadas'] >= 80 && $e['total_tareas'] > 0)
            ->sortByDesc('porcentaje_completadas')
            ->values();

        // Empleados con tareas pendientes/atrasadas
        $empleadosPendientes = $empleadosStats
            ->filter(fn($e) => $e['tareas_pendientes'] > 0 || $e['tareas_atrasadas'] > 0)
            ->sortByDesc('tareas_atrasadas')
            ->values();

        $pdf = Pdf::loadView('pdf.reporte-proyectos', [
            'proyectos' => $proyectosData,
            'fecha_generacion' => now()->format('d/m/Y H:i:s'),
            'usuario' => $user->name,
            'usuario_email' => $user->email,
            'usuario_rol' => $user->roles->pluck('name')->first() ?? 'Usuario',
            'dirigido_a' => $user->name,

            // Estadísticas generales
            'total_proyectos' => $totalProyectos,
            'proyectos_completados' => $proyectosCompletados,
            'proyectos_en_progreso' => $proyectosEnProgreso,
            'proyectos_pendientes' => $proyectosPendientes,
            'proyectos_atrasados_count' => $proyectosAtrasados,
            'total_tareas' => $totalTareas,
            'tareas_completadas' => $tareasCompletadas,
            'tareas_pendientes' => $tareasPendientes,
            'tareas_en_progreso' => $tareasEnProgreso,
            'porcentaje_cumplimiento' => $totalTareas > 0 ? round(($tareasCompletadas / $totalTareas) * 100, 1) : 0,

            // Datos analíticos
            'proyectos_atrasados' => $proyectosAtrasadosDetalle,
            'empleados_destacados' => $empleadosDestacados,
            'empleados_pendientes' => $empleadosPendientes,
        ]);

        return $pdf->download('reporte-proyectos-' . now()->format('Y-m-d') . '.pdf');
    }
}
