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
        'asignado_a',
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
     * Relación: Una tarea puede estar asignada a un usuario
     */
    public function asignado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }
}
