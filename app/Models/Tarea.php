<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tarea extends Model
{
    protected $fillable = [
        'proyecto_id',
        'nombre',
        'descripcion',
        'user_id',
        'estado',
        'fecha_inicio',
        'fecha_fin',
        'prioridad',
        'progreso',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    /**
     * Relación: Una tarea pertenece a un proyecto
     */
    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class);
    }

    /**
     * Relación: Una tarea pertenece a un usuario (creador)
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación: Una tarea tiene muchos registros de historial de reasignación
     */
    public function reasignacionHistorial(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TareaReasignacionHistorial::class)->latest();
    }

    /**
     * Relación: Una tarea tiene muchos registros de auditoría
     */
    public function auditorias(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TareaAuditoria::class)->latest();
    }
}
