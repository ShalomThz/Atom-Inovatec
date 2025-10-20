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
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
