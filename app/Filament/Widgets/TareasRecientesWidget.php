<?php

namespace App\Filament\Widgets;

use App\Models\Tarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class TareasRecientesWidget extends TableWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Tarea::query()
                    ->with(['proyecto', 'usuario'])
                    ->latest()
                    ->limit(10)
            )
            ->heading('Tareas Recientes')
            ->columns([
                TextColumn::make('nombre')
                    ->label('Tarea')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-clipboard-document-list'),
                TextColumn::make('proyecto.nombre')
                    ->label('Proyecto')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-rectangle-stack')
                    ->color('primary'),
                TextColumn::make('usuario.name')
                    ->label('Creado por')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user'),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente' => 'gray',
                        'en_progreso' => 'warning',
                        'completado' => 'success',
                        'cancelado' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pendiente' => 'Pendiente',
                        'en_progreso' => 'En Progreso',
                        'completado' => 'Completado',
                        'cancelado' => 'Cancelado',
                        default => $state,
                    }),
                TextColumn::make('prioridad')
                    ->label('Prioridad')
                    ->badge()
                    ->color(fn (int $state): string => match ($state) {
                        1 => 'success',
                        2 => 'info',
                        3 => 'warning',
                        4 => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        1 => 'Baja',
                        2 => 'Media',
                        3 => 'Alta',
                        4 => 'Urgente',
                        default => 'Desconocida',
                    }),
                TextColumn::make('progreso')
                    ->label('Progreso')
                    ->suffix('%')
                    ->sortable()
                    ->color(fn (int $state): string => match (true) {
                        $state === 100 => 'success',
                        $state >= 50 => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('fecha_fin')
                    ->label('Fecha Límite')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('Sin fecha')
                    ->icon('heroicon-o-calendar'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
