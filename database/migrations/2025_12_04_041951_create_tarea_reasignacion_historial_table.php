<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Tarea;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tarea_reasignacion_historial', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Tarea::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'usuario_anterior_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignIdFor(User::class, 'usuario_nuevo_id')->constrained('users')->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'modificado_por_id')->constrained('users')->cascadeOnDelete();
            $table->text('motivo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarea_reasignacion_historial');
    }
};