<?php

use App\Filament\Pages\KanbanBoard;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

// Kanban Board API endpoint
Route::middleware(['auth'])->group(function () {
    Route::post('/admin/kanban-board/update-tarea', function () {
        $kanbanBoard = new KanbanBoard();
        $result = $kanbanBoard->updateTareaEstado(
            request()->input('tarea_id'),
            request()->input('nuevo_estado')
        );
        return response()->json($result);
    })->name('kanban.update-tarea');

    // Reporte PDF
    Route::get('/admin/reporte/proyectos/pdf', [\App\Http\Controllers\ReporteController::class, 'generarPdfProyectos'])
        ->name('reporte.proyectos.pdf');

    // Notificaciones API
    Route::prefix('api/notificaciones')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\NotificacionController::class, 'index'])->name('api.notificaciones.index');
        Route::get('/no-leidas', [\App\Http\Controllers\Api\NotificacionController::class, 'noLeidas'])->name('api.notificaciones.no-leidas');
        Route::post('/{id}/marcar-leida', [\App\Http\Controllers\Api\NotificacionController::class, 'marcarComoLeida'])->name('api.notificaciones.marcar-leida');
        Route::post('/marcar-todas-leidas', [\App\Http\Controllers\Api\NotificacionController::class, 'marcarTodasComoLeidas'])->name('api.notificaciones.marcar-todas-leidas');
        Route::delete('/{id}', [\App\Http\Controllers\Api\NotificacionController::class, 'destroy'])->name('api.notificaciones.destroy');
        Route::delete('/leidas/eliminar', [\App\Http\Controllers\Api\NotificacionController::class, 'eliminarLeidas'])->name('api.notificaciones.eliminar-leidas');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
