<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TareaReasignacionHistorial extends Model
{
    protected $table = 'tarea_reasignacion_historial';

    protected $fillable = [
        'tarea_id',
        'usuario_anterior_id',
        'usuario_nuevo_id',
        'modificado_por_id',
        'motivo',
    ];

    public function tarea(): BelongsTo
    {
        return $this->belongsTo(Tarea::class);
    }

    public function usuarioAnterior(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_anterior_id');
    }

    public function usuarioNuevo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_nuevo_id');
    }

    public function modificadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modificado_por_id');
    }
}
