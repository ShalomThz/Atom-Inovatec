<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proyecto extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'user_id',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'presupuesto',
        'prioridad',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'presupuesto' => 'decimal:2',
    ];

    /**
     * Relación: Un proyecto pertenece a un usuario (creador)
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación: Un proyecto tiene muchas tareas
     */
    public function tareas(): HasMany
    {
        return $this->hasMany(Tarea::class);
    }

    /**
     * Calcula el progreso general del proyecto basado en sus tareas
     */
    public function getProgresoGeneralAttribute(): float
    {
        $tareas = $this->tareas;

        if ($tareas->isEmpty()) {
            return 0;
        }

        $progresoTotal = $tareas->sum('progreso');
        return round($progresoTotal / $tareas->count(), 2);
    }

    /**
     * Cuenta las tareas por estado
     */
    public function getTareasEstadisticasAttribute(): array
    {
        $tareas = $this->tareas;

        return [
            'total' => $tareas->count(),
            'pendientes' => $tareas->where('estado', 'pendiente')->count(),
            'en_progreso' => $tareas->where('estado', 'en_progreso')->count(),
            'completadas' => $tareas->where('estado', 'completada')->count(),
            'canceladas' => $tareas->where('estado', 'cancelada')->count(),
        ];
    }

    /**
     * Verifica si el proyecto está retrasado
     */
    public function getEstaRetrasadoAttribute(): bool
    {
        if (!$this->fecha_fin) {
            return false;
        }

        $hoy = now()->startOfDay();
        $fechaFin = $this->fecha_fin->startOfDay();

        // Está retrasado si la fecha de fin ya pasó y el proyecto no está completado
        return $fechaFin < $hoy && $this->estado !== 'completado';
    }

    /**
     * Obtiene el porcentaje de tareas completadas
     */
    public function getPorcentajeTareasCompletadasAttribute(): float
    {
        $tareas = $this->tareas;

        if ($tareas->isEmpty()) {
            return 0;
        }

        $completadas = $tareas->where('estado', 'completada')->count();
        return round(($completadas / $tareas->count()) * 100, 2);
    }
}
