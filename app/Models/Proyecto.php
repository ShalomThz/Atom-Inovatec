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
}
