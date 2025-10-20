<?php

use App\Models\Proyecto;
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
        Schema::create('tareas', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Proyecto::class);
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->foreignIdFor(\App\Models\User::class);
            $table->enum('estado', ['pendiente', 'en_progreso', 'completada', 'cancelada'])->default('pendiente');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->integer('prioridad')->default(1); // 1: baja, 2: media, 3: alta
            $table->integer('progreso')->default(0); // 0-100
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tareas');
    }
};
