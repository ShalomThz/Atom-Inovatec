<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TareaAuditoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'tarea_id',
        'user_id',
        'observacion',
    ];

    public function tarea(): BelongsTo
    {
        return $this->belongsTo(Tarea::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
