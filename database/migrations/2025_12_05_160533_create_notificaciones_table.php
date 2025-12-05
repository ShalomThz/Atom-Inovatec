<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('tipo'); // 'tarea_asignada', 'tarea_reasignada', 'proyecto_actualizado', etc.
            $table->string('titulo');
            $table->text('mensaje');
            $table->morphs('notificable'); // Para relacionar con Tarea, Proyecto, etc.
            $table->boolean('leida')->default(false);
            $table->timestamp('leida_en')->nullable();
            $table->json('datos_adicionales')->nullable(); // Para datos extra como IDs, URLs, etc.
            $table->timestamps();

            $table->index(['user_id', 'leida']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
