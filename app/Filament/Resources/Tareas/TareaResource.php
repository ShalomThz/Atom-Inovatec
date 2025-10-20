<?php

namespace App\Filament\Resources\Tareas;

use App\Filament\Resources\Tareas\Pages\ManageTareas;
use App\Models\Tarea;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TareaResource extends Resource
{
    protected static ?string $model = Tarea::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tarea')
                    ->tabs([
                        Tabs\Tab::make('Información General')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Select::make('proyecto_id')
                                    ->relationship('proyecto', 'nombre')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->label('Proyecto')
                                    ->columnSpanFull(),
                                TextInput::make('nombre')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Textarea::make('descripcion')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('Asignación')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Select::make('asignado_a')
                                    ->relationship('asignado', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label('Asignado a')
                                    ->placeholder('Seleccione un usuario'),
                            ]),

                        Tabs\Tab::make('Fechas y Estado')
                            ->icon('heroicon-o-calendar')
                            ->schema([
                                DatePicker::make('fecha_inicio')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->label('Fecha de Inicio'),
                                DatePicker::make('fecha_fin')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->after('fecha_inicio')
                                    ->label('Fecha de Fin'),
                                Select::make('estado')
                                    ->required()
                                    ->options([
                                        'pendiente' => 'Pendiente',
                                        'en_progreso' => 'En Progreso',
                                        'completado' => 'Completado',
                                        'cancelado' => 'Cancelado',
                                    ])
                                    ->default('pendiente')
                                    ->native(false),
                                Select::make('prioridad')
                                    ->required()
                                    ->options([
                                        1 => 'Baja',
                                        2 => 'Media',
                                        3 => 'Alta',
                                        4 => 'Urgente',
                                    ])
                                    ->default(1)
                                    ->native(false),
                            ])->columns(2),

                        Tabs\Tab::make('Progreso')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                TextInput::make('progreso')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->suffix('%')
                                    ->label('Porcentaje de Progreso')
                                    ->helperText('Ingrese un valor entre 0 y 100'),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Información de la Tarea')
                    ->tabs([
                        Tabs\Tab::make('General')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Información de la Tarea')
                                    ->description('Datos principales de la tarea')
                                    ->schema([
                                        Grid::make(['default' => 1, 'md' => 2])->schema([
                                            TextEntry::make('nombre')
                                                ->label('Nombre de la Tarea')
                                                ->size('lg')
                                                ->weight('bold')
                                                ->icon('heroicon-o-clipboard-document-list')
                                                ->color('primary'),
                                            TextEntry::make('proyecto.nombre')
                                                ->label('Proyecto Asociado')
                                                ->icon('heroicon-o-rectangle-stack')
                                                ->iconColor('primary')
                                                ->weight('bold'),
                                        ]),
                                        Grid::make(['default' => 1, 'md' => 1])->schema([
                                            TextEntry::make('descripcion')
                                                ->label('Descripción')
                                                ->placeholder('Sin descripción')
                                                ->columnSpanFull(),
                                        ]),
                                        Grid::make(['default' => 1, 'md' => 2])->schema([
                                            TextEntry::make('proyecto.estado')
                                                ->label('Estado del Proyecto')
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
                                        ]),
                                    ])
                                    ->compact(),
                            ]),

                        Tabs\Tab::make('Asignación')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make('Usuario Asignado')
                                    ->icon('heroicon-o-user-circle')
                                    ->schema([
                                        TextEntry::make('asignado.name')
                                            ->label('Nombre')
                                            ->placeholder('Sin asignar')
                                            ->icon('heroicon-o-user')
                                            ->iconColor('primary')
                                            ->size('md'),
                                        TextEntry::make('asignado.email')
                                            ->label('Email')
                                            ->placeholder('Sin asignar')
                                            ->icon('heroicon-o-envelope')
                                            ->copyable()
                                            ->copyMessage('Email copiado')
                                            ->copyMessageDuration(1500),
                                    ])
                                    ->columns(2),
                            ]),

                        Tabs\Tab::make('Fechas y Estado')
                            ->icon('heroicon-o-calendar')
                            ->badge(fn ($record) => $record->estado === 'completado' ? '✓' : null)
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Section::make('Cronograma')
                                            ->icon('heroicon-o-calendar-days')
                                            ->schema([
                                                TextEntry::make('fecha_inicio')
                                                    ->label('Fecha de Inicio')
                                                    ->date('d/m/Y')
                                                    ->placeholder('Sin fecha de inicio')
                                                    ->icon('heroicon-o-play')
                                                    ->iconColor('success'),
                                                TextEntry::make('fecha_fin')
                                                    ->label('Fecha de Fin')
                                                    ->date('d/m/Y')
                                                    ->placeholder('Sin fecha de fin')
                                                    ->icon('heroicon-o-flag')
                                                    ->iconColor('danger'),
                                            ])
                                            ->columns(1),

                                        Section::make('Estado y Prioridad')
                                            ->icon('heroicon-o-chart-bar-square')
                                            ->schema([
                                                TextEntry::make('estado')
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
                                                TextEntry::make('prioridad')
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
                                            ])
                                            ->columns(1),
                                    ]),
                            ]),

                        Tabs\Tab::make('Progreso')
                            ->icon('heroicon-o-chart-bar')
                            ->badge(fn ($record) => $record->progreso . '%')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        TextEntry::make('progreso')
                                            ->label('Porcentaje de Progreso')
                                            ->suffix('%')
                                            ->size('lg')
                                            ->weight('bold')
                                            ->color(fn (int $state): string => match (true) {
                                                $state === 100 => 'success',
                                                $state >= 75 => 'info',
                                                $state >= 50 => 'warning',
                                                $state >= 25 => 'gray',
                                                default => 'danger',
                                            })
                                            ->icon(fn (int $state): string => match (true) {
                                                $state === 100 => 'heroicon-o-check-circle',
                                                $state >= 50 => 'heroicon-o-clock',
                                                default => 'heroicon-o-exclamation-circle',
                                            }),
                                    ]),
                            ]),

                        Tabs\Tab::make('Auditoría')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('created_at')
                                            ->label('Fecha de Creación')
                                            ->dateTime('d/m/Y H:i:s')
                                            ->icon('heroicon-o-plus-circle')
                                            ->iconColor('success'),
                                        TextEntry::make('updated_at')
                                            ->label('Última Actualización')
                                            ->dateTime('d/m/Y H:i:s')
                                            ->icon('heroicon-o-pencil-square')
                                            ->iconColor('warning')
                                            ->since(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('proyecto.id')
                    ->searchable(),
                TextColumn::make('nombre')
                    ->searchable(),
                TextColumn::make('asignado_a')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('estado')
                    ->searchable(),
                TextColumn::make('fecha_inicio')
                    ->date()
                    ->sortable(),
                TextColumn::make('fecha_fin')
                    ->date()
                    ->sortable(),
                TextColumn::make('prioridad')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('progreso')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTareas::route('/'),
        ];
    }
}
