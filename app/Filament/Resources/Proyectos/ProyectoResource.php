<?php

namespace App\Filament\Resources\Proyectos;

use App\Filament\Resources\Proyectos\Pages\ManageProyectos;
use App\Models\Proyecto;
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
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Grid;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\ImageColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ProyectoResource extends Resource
{
    protected static ?string $model = Proyecto::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        // Si es super admin, ver todos los proyectos
        if ($user->hasRole('super_admin')) {
            return $query;
        }

        // Si es líder de proyecto, ver solo los proyectos que él creó
        if ($user->hasRole('lider_proyecto')) {
            return $query->where('user_id', $user->id);
        }

        // Si es desarrollador, ver solo proyectos donde tiene tareas asignadas
        if ($user->hasRole('desarrollador')) {
            return $query->whereHas('tareas', function (Builder $query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        // Por defecto, no mostrar nada si no tiene rol definido
        return $query->whereRaw('1 = 0');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Proyecto')
                    ->tabs([
                        Tabs\Tab::make('Información General')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextInput::make('nombre')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Textarea::make('descripcion')
                                    ->rows(4)
                                    ->columnSpanFull(),
                                Select::make('user_id')
                                    ->relationship('usuario', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->label('Creador del Proyecto')
                                    ->disabled(fn () => Auth::user()->hasRole('desarrollador')),
                            ]),

                        Tabs\Tab::make('Fechas y Estado')
                            ->icon('heroicon-o-calendar')
                            ->schema([
                                DatePicker::make('fecha_inicio')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y'),
                                DatePicker::make('fecha_fin')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->after('fecha_inicio'),
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

                        Tabs\Tab::make('Presupuesto')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                TextInput::make('presupuesto')
                                    ->numeric()
                                    ->prefix('$')
                                    ->maxValue(9999999999.99)
                                    ->step(0.01)
                                    ->placeholder('0.00'),
                            ]),

                        Tabs\Tab::make('Tareas')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                Repeater::make('tareas')
                                    ->relationship()
                                    ->schema([
                                        TextInput::make('nombre')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(2),
                                        Textarea::make('descripcion')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                        Select::make('user_id')
                                            ->relationship('usuario', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->label('Asignado a')
                                            ->disabled(fn () => Auth::user()->hasRole('desarrollador')),
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
                                        DatePicker::make('fecha_inicio')
                                            ->native(false)
                                            ->displayFormat('d/m/Y'),
                                        DatePicker::make('fecha_fin')
                                            ->native(false)
                                            ->displayFormat('d/m/Y')
                                            ->after('fecha_inicio'),
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
                                        TextInput::make('progreso')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->suffix('%'),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(0)
                                    ->addActionLabel('Agregar Tarea')
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['nombre'] ?? null),
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
                Tabs::make('Información del Proyecto')
                    ->tabs([
                        Tabs\Tab::make('General')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Información del Proyecto')
                                    ->description('Datos principales y resumen del proyecto')
                                    ->icon('heroicon-o-rectangle-stack')
                                    ->schema([
                                        TextEntry::make('nombre')
                                            ->label('Nombre del Proyecto')
                                            ->size('xl')
                                            ->weight('bold')
                                            ->icon('heroicon-o-rectangle-stack')
                                            ->iconColor('primary')
                                            ->color('primary')
                                            ->columnSpanFull(),
                                        Grid::make(['default' => 1, 'md' => 2])->schema([
                                            TextEntry::make('usuario.name')
                                                ->label('Creado por')
                                                ->icon('heroicon-o-user')
                                                ->iconColor('primary')
                                                ->size('md')
                                                ->weight('semibold'),
                                            TextEntry::make('usuario.email')
                                                ->label('Email del Creador')
                                                ->icon('heroicon-o-envelope')
                                                ->iconColor('gray')
                                                ->copyable()
                                                ->copyMessage('Email copiado')
                                                ->copyMessageDuration(1500)
                                                ->color('gray')
                                                ->size('sm'),
                                        ]),
                                        TextEntry::make('descripcion')
                                            ->label('Descripción')
                                            ->placeholder('Sin descripción')
                                            ->columnSpanFull()
                                            ->color('gray'),
                                    ])
                                    ->compact()
                                    ->collapsible(),

                                Grid::make(['default' => 1, 'md' => 3])->schema([
                                    Section::make('Tareas')
                                        ->icon('heroicon-o-clipboard-document-list')
                                        ->iconColor('primary')
                                        ->schema([
                                            TextEntry::make('tareas_count')
                                                ->label('Total de Tareas')
                                                ->state(fn ($record) => $record->tareas->count())
                                                ->badge()
                                                ->color('primary')
                                                ->size('lg')
                                                ->weight('bold'),
                                        ])
                                        ->compact(),

                                    Section::make('Progreso')
                                        ->icon('heroicon-o-chart-bar')
                                        ->iconColor('success')
                                        ->schema([
                                            TextEntry::make('progreso_general')
                                                ->label('Progreso General')
                                                ->state(fn ($record) => round($record->progreso_general, 0))
                                                ->suffix('%')
                                                ->badge()
                                                ->color(fn ($record): string => match (true) {
                                                    $record->progreso_general === 100 => 'success',
                                                    $record->progreso_general >= 75 => 'info',
                                                    $record->progreso_general >= 50 => 'warning',
                                                    default => 'gray',
                                                })
                                                ->size('lg')
                                                ->weight('bold'),
                                        ])
                                        ->compact(),

                                    Section::make('Completadas')
                                        ->icon('heroicon-o-check-circle')
                                        ->iconColor('success')
                                        ->schema([
                                            TextEntry::make('tareas_completadas')
                                                ->label('Tareas Completadas')
                                                ->state(fn ($record) => $record->tareas->where('estado', 'completada')->count())
                                                ->badge()
                                                ->color('success')
                                                ->size('lg')
                                                ->weight('bold'),
                                        ])
                                        ->compact(),
                                ]),

                                Section::make('Estadísticas de Tareas')
                                    ->icon('heroicon-o-chart-pie')
                                    ->description('Desglose del estado de las tareas del proyecto')
                                    ->schema([
                                        Grid::make(['default' => 2, 'md' => 4])->schema([
                                            TextEntry::make('tareas_pendientes')
                                                ->label('Pendientes')
                                                ->state(fn ($record) => $record->tareas->where('estado', 'pendiente')->count())
                                                ->badge()
                                                ->icon('heroicon-o-clock')
                                                ->color('gray'),
                                            TextEntry::make('tareas_en_progreso')
                                                ->label('En Progreso')
                                                ->state(fn ($record) => $record->tareas->where('estado', 'en_progreso')->count())
                                                ->badge()
                                                ->icon('heroicon-o-arrow-path')
                                                ->color('warning'),
                                            TextEntry::make('tareas_completadas_stat')
                                                ->label('Completadas')
                                                ->state(fn ($record) => $record->tareas->where('estado', 'completada')->count())
                                                ->badge()
                                                ->icon('heroicon-o-check-circle')
                                                ->color('success'),
                                            TextEntry::make('tareas_canceladas')
                                                ->label('Canceladas')
                                                ->state(fn ($record) => $record->tareas->where('estado', 'cancelada')->count())
                                                ->badge()
                                                ->icon('heroicon-o-x-circle')
                                                ->color('danger'),
                                        ]),
                                    ])
                                    ->compact()
                                    ->collapsible()
                                    ->collapsed(),
                            ]),

                        Tabs\Tab::make('Fechas y Estado')
                            ->icon('heroicon-o-calendar')
                            ->badge(fn ($record) => $record->estado === 'completado' ? '✓' : null)
                            ->schema([
                                Grid::make(['default' => 1, 'md' => 2])
                                    ->schema([
                                        Section::make('Cronograma del Proyecto')
                                            ->description('Fechas importantes del proyecto')
                                            ->icon('heroicon-o-calendar-days')
                                            ->iconColor('primary')
                                            ->schema([
                                                TextEntry::make('fecha_inicio')
                                                    ->label('Fecha de Inicio')
                                                    ->date('d/m/Y')
                                                    ->icon('heroicon-o-play')
                                                    ->iconColor('success')
                                                    ->size('md')
                                                    ->weight('semibold')
                                                    ->color('success')
                                                    ->helperText('Fecha en que se inició el proyecto'),
                                                TextEntry::make('fecha_fin')
                                                    ->label('Fecha de Finalización')
                                                    ->date('d/m/Y')
                                                    ->placeholder('Sin fecha de fin definida')
                                                    ->icon('heroicon-o-flag')
                                                    ->iconColor('danger')
                                                    ->size('md')
                                                    ->weight('semibold')
                                                    ->color('danger')
                                                    ->helperText('Fecha límite para completar el proyecto'),
                                                TextEntry::make('duracion')
                                                    ->label('Duración del Proyecto')
                                                    ->state(function ($record) {
                                                        if (!$record->fecha_inicio || !$record->fecha_fin) {
                                                            return 'No se puede calcular';
                                                        }
                                                        $inicio = \Carbon\Carbon::parse($record->fecha_inicio);
                                                        $fin = \Carbon\Carbon::parse($record->fecha_fin);
                                                        $dias = (int) $inicio->diffInDays($fin);
                                                        return $dias . ' días';
                                                    })
                                                    ->icon('heroicon-o-clock')
                                                    ->iconColor('primary')
                                                    ->badge()
                                                    ->color('info'),
                                            ])
                                            ->compact()
                                            ->columns(1),

                                        Section::make('Estado del Proyecto')
                                            ->description('Estado actual y prioridad')
                                            ->icon('heroicon-o-chart-bar-square')
                                            ->iconColor('warning')
                                            ->schema([
                                                TextEntry::make('estado')
                                                    ->label('Estado Actual')
                                                    ->badge()
                                                    ->size('lg')
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
                                                    })
                                                    ->icon(fn (string $state): string => match ($state) {
                                                        'completado' => 'heroicon-o-check-circle',
                                                        'en_progreso' => 'heroicon-o-arrow-path',
                                                        'cancelado' => 'heroicon-o-x-circle',
                                                        'pendiente' => 'heroicon-o-clock',
                                                        default => 'heroicon-o-minus',
                                                    })
                                                    ->helperText('Estado actual del proyecto'),
                                                TextEntry::make('prioridad')
                                                    ->label('Nivel de Prioridad')
                                                    ->badge()
                                                    ->size('lg')
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
                                                    })
                                                    ->icon(fn (int $state): string => match ($state) {
                                                        1 => 'heroicon-o-arrow-down',
                                                        2 => 'heroicon-o-minus',
                                                        3 => 'heroicon-o-arrow-up',
                                                        4 => 'heroicon-o-exclamation-triangle',
                                                        default => 'heroicon-o-minus',
                                                    })
                                                    ->helperText('Importancia relativa del proyecto'),
                                                TextEntry::make('dias_restantes')
                                                    ->label('Tiempo Restante')
                                                    ->state(function ($record) {
                                                        if (!$record->fecha_fin) {
                                                            return 'Sin fecha límite';
                                                        }
                                                        $hoy = \Carbon\Carbon::now();
                                                        $fin = \Carbon\Carbon::parse($record->fecha_fin);
                                                        if ($hoy->greaterThan($fin)) {
                                                            $dias = (int) $hoy->diffInDays($fin);
                                                            return $dias . ' días de retraso';
                                                        }
                                                        $dias = (int) $hoy->diffInDays($fin);
                                                        return $dias . ' días restantes';
                                                    })
                                                    ->badge()
                                                    ->color(function ($record): string {
                                                        if (!$record->fecha_fin) return 'gray';
                                                        $hoy = \Carbon\Carbon::now();
                                                        $fin = \Carbon\Carbon::parse($record->fecha_fin);
                                                        if ($hoy->greaterThan($fin)) return 'danger';
                                                        $dias = (int) $hoy->diffInDays($fin);
                                                        if ($dias <= 7) return 'warning';
                                                        return 'success';
                                                    })
                                                    ->icon(fn ($record) => !$record->fecha_fin ? 'heroicon-o-minus' : (\Carbon\Carbon::now()->greaterThan(\Carbon\Carbon::parse($record->fecha_fin)) ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-clock'))
                                                    ->helperText('Días restantes hasta la fecha límite'),
                                            ])
                                            ->compact()
                                            ->columns(1),
                                    ]),
                            ]),

                        Tabs\Tab::make('Presupuesto')
                            ->icon('heroicon-o-banknotes')
                            ->badge(fn ($record) => $record->presupuesto ? '$' : null)
                            ->schema([
                                Section::make('Información Presupuestaria')
                                    ->description('Detalles del presupuesto asignado al proyecto')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->schema([
                                        Grid::make(['default' => 1, 'md' => 2])->schema([
                                            TextEntry::make('presupuesto')
                                                ->label('Presupuesto Total Asignado')
                                                ->money('USD')
                                                ->placeholder('Sin presupuesto definido')
                                                ->size('xl')
                                                ->weight('bold')
                                                ->icon('heroicon-o-banknotes')
                                                ->iconColor('success')
                                                ->color('success')
                                                ->helperText('Monto total asignado para este proyecto'),
                                            TextEntry::make('created_at')
                                                ->label('Presupuesto Aprobado el')
                                                ->date('d/m/Y')
                                                ->icon('heroicon-o-calendar-days')
                                                ->iconColor('primary')
                                                ->helperText('Fecha de aprobación del presupuesto'),
                                        ]),
                                    ])
                                    ->compact()
                                    ->collapsible(),

                                Section::make('Información Adicional')
                                    ->description('Contexto sobre el uso del presupuesto')
                                    ->icon('heroicon-o-information-circle')
                                    ->schema([
                                        TextEntry::make('presupuesto_info')
                                            ->label('')
                                            ->state(fn ($record) => $record->presupuesto
                                                ? 'El presupuesto se gestiona y controla para asegurar que los recursos se utilicen de manera eficiente durante el desarrollo del proyecto.'
                                                : 'Este proyecto no tiene un presupuesto definido. Se recomienda asignar un presupuesto para un mejor control de costos.')
                                            ->color(fn ($record) => $record->presupuesto ? 'gray' : 'warning')
                                            ->columnSpanFull(),
                                    ])
                                    ->compact()
                                    ->collapsible()
                                    ->collapsed(),
                            ]),

                        Tabs\Tab::make('Tareas')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->badge(fn ($record) => $record->tareas->count())
                            ->schema([
                                Section::make('Lista de Tareas del Proyecto')
                                    ->description('Todas las tareas asociadas a este proyecto organizadas por prioridad y estado')
                                    ->icon('heroicon-o-clipboard-document-list')
                                    ->schema([
                                        RepeatableEntry::make('tareas')
                                            ->label('')
                                            ->schema([
                                                // Header de la tarjeta con nombre y prioridad
                                                Grid::make(['default' => 1])->schema([
                                                    TextEntry::make('nombre')
                                                        ->label('')
                                                        ->weight('bold')
                                                        ->size('lg')
                                                        ->icon('heroicon-o-clipboard-document')
                                                        ->iconColor('primary')
                                                        ->color('primary')
                                                        ->columnSpanFull(),
                                                ]),

                                                // Badges de estado y prioridad
                                                Grid::make(['default' => 2, 'md' => 4])->schema([
                                                    TextEntry::make('estado')
                                                        ->label('Estado')
                                                        ->badge()
                                                        ->size('md')
                                                        ->weight('semibold')
                                                        ->color(fn (string $state): string => match ($state) {
                                                            'pendiente' => 'gray',
                                                            'en_progreso' => 'warning',
                                                            'completada' => 'success',
                                                            'cancelada' => 'danger',
                                                            default => 'gray',
                                                        })
                                                        ->formatStateUsing(fn (string $state): string => match ($state) {
                                                            'pendiente' => 'Pendiente',
                                                            'en_progreso' => 'En Progreso',
                                                            'completada' => 'Completada',
                                                            'cancelada' => 'Cancelada',
                                                            default => $state,
                                                        })
                                                        ->icon(fn (string $state): string => match ($state) {
                                                            'completada' => 'heroicon-o-check-circle',
                                                            'en_progreso' => 'heroicon-o-arrow-path',
                                                            'cancelada' => 'heroicon-o-x-circle',
                                                            'pendiente' => 'heroicon-o-clock',
                                                            default => 'heroicon-o-minus',
                                                        }),
                                                    TextEntry::make('prioridad')
                                                        ->label('Prioridad')
                                                        ->badge()
                                                        ->size('md')
                                                        ->weight('semibold')
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
                                                            default => 'Desc.',
                                                        })
                                                        ->icon(fn (int $state): string => match ($state) {
                                                            1 => 'heroicon-o-arrow-down',
                                                            2 => 'heroicon-o-minus',
                                                            3 => 'heroicon-o-arrow-up',
                                                            4 => 'heroicon-o-exclamation-triangle',
                                                            default => 'heroicon-o-minus',
                                                        }),
                                                    TextEntry::make('progreso')
                                                        ->label('Progreso')
                                                        ->suffix('%')
                                                        ->badge()
                                                        ->size('md')
                                                        ->weight('semibold')
                                                        ->color(fn (int $state): string => match (true) {
                                                            $state === 100 => 'success',
                                                            $state >= 75 => 'info',
                                                            $state >= 50 => 'warning',
                                                            default => 'gray',
                                                        })
                                                        ->icon('heroicon-o-chart-bar'),
                                                    TextEntry::make('dias_para_vencer')
                                                        ->label('Vencimiento')
                                                        ->state(function ($record) {
                                                            if (!$record->fecha_fin) return 'Sin fecha límite';
                                                            $hoy = \Carbon\Carbon::now();
                                                            $fin = \Carbon\Carbon::parse($record->fecha_fin);
                                                            if ($hoy->greaterThan($fin)) {
                                                                return 'Vencida';
                                                            }
                                                            $dias = (int) $hoy->diffInDays($fin);
                                                            if ($dias === 0) return 'Vence hoy';
                                                            if ($dias === 1) return 'Vence mañana';
                                                            return $dias . ' días';
                                                        })
                                                        ->badge()
                                                        ->size('md')
                                                        ->color(function ($record): string {
                                                            if (!$record->fecha_fin) return 'gray';
                                                            $hoy = \Carbon\Carbon::now();
                                                            $fin = \Carbon\Carbon::parse($record->fecha_fin);
                                                            if ($hoy->greaterThan($fin)) return 'danger';
                                                            $dias = (int) $hoy->diffInDays($fin);
                                                            if ($dias <= 3) return 'danger';
                                                            if ($dias <= 7) return 'warning';
                                                            return 'success';
                                                        })
                                                        ->icon(fn ($record) => !$record->fecha_fin ? 'heroicon-o-minus' : (\Carbon\Carbon::now()->greaterThan(\Carbon\Carbon::parse($record->fecha_fin)) ? 'heroicon-o-exclamation-circle' : 'heroicon-o-clock')),
                                                ]),

                                                // Separador visual
                                                Section::make()
                                                    ->schema([
                                                        // Información del asignado
                                                        Grid::make(['default' => 1, 'md' => 2])->schema([
                                                            TextEntry::make('usuario.name')
                                                                ->label('Asignado a')
                                                                ->icon('heroicon-o-user-circle')
                                                                ->iconColor('primary')
                                                                ->placeholder('Sin asignar')
                                                                ->color('primary')
                                                                ->weight('semibold')
                                                                ->size('md'),
                                                            TextEntry::make('usuario.email')
                                                                ->label('Correo electrónico')
                                                                ->icon('heroicon-o-envelope')
                                                                ->iconColor('gray')
                                                                ->placeholder('Sin correo')
                                                                ->size('sm')
                                                                ->color('gray')
                                                                ->copyable()
                                                                ->copyMessage('Email copiado')
                                                                ->copyMessageDuration(1500),
                                                        ]),

                                                        // Fechas
                                                        Grid::make(['default' => 1, 'md' => 2])->schema([
                                                            TextEntry::make('fecha_inicio')
                                                                ->label('Inicia')
                                                                ->date('d/m/Y')
                                                                ->placeholder('Sin fecha de inicio')
                                                                ->icon('heroicon-o-calendar')
                                                                ->iconColor('success')
                                                                ->color('success')
                                                                ->weight('medium'),
                                                            TextEntry::make('fecha_fin')
                                                                ->label('Finaliza')
                                                                ->date('d/m/Y')
                                                                ->placeholder('Sin fecha de fin')
                                                                ->icon('heroicon-o-calendar')
                                                                ->iconColor('danger')
                                                                ->color('danger')
                                                                ->weight('medium'),
                                                        ]),

                                                        // Descripción
                                                        TextEntry::make('descripcion')
                                                            ->label('Descripción de la tarea')
                                                            ->placeholder('Sin descripción disponible')
                                                            ->columnSpanFull()
                                                            ->color('gray')
                                                            ->size('sm')
                                                            ->lineClamp(3),
                                                    ])
                                                    ->compact(),
                                            ])
                                            ->contained(true)
                                            ->placeholder('No hay tareas asociadas a este proyecto'),
                                    ])
                                    ->collapsible(),
                            ]),

                        Tabs\Tab::make('Observaciones')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->badge(fn ($record) =>
                                $record->tareas->sum(fn($t) =>
                                    $t->reasignacionHistorial->count() + $t->auditorias->count()
                                ) + 2 // +2 por created_at y updated_at del proyecto
                            )
                            ->schema([
                                Section::make('Información del Proyecto')
                                    ->description('Fechas importantes del proyecto')
                                    ->icon('heroicon-o-information-circle')
                                    ->iconColor('primary')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextEntry::make('created_at')
                                                ->label('Proyecto Creado')
                                                ->dateTime('d/m/Y H:i:s')
                                                ->icon('heroicon-o-calendar-days')
                                                ->iconColor('success')
                                                ->weight('bold')
                                                ->color('success')
                                                ->helperText('Fecha y hora de creación del proyecto'),
                                            TextEntry::make('updated_at')
                                                ->label('Última Modificación')
                                                ->dateTime('d/m/Y H:i:s')
                                                ->icon('heroicon-o-calendar-days')
                                                ->iconColor('warning')
                                                ->weight('bold')
                                                ->color('warning')
                                                ->helperText('Fecha y hora de la última actualización')
                                                ->since(),
                                        ]),
                                    ])
                                    ->compact()
                                    ->collapsible(),

                                Section::make('Observaciones y Cambios en las Tareas')
                                    ->description('Registro de todas las reasignaciones y observaciones realizadas en las tareas del proyecto')
                                    ->icon('heroicon-o-document-text')
                                    ->iconColor('warning')
                                    ->schema([
                                        RepeatableEntry::make('tareas')
                                            ->label('')
                                            ->schema([
                                                TextEntry::make('nombre')
                                                    ->label('Tarea')
                                                    ->weight('bold')
                                                    ->size('md')
                                                    ->icon('heroicon-o-clipboard-document-list')
                                                    ->color('primary')
                                                    ->columnSpanFull(),

                                                RepeatableEntry::make('reasignacionHistorial')
                                                    ->label('Reasignaciones')
                                                    ->schema([
                                                        Grid::make(['default' => 1])->schema([
                                                            TextEntry::make('created_at')
                                                                ->label('')
                                                                ->dateTime('d/m/Y H:i:s')
                                                                ->icon('heroicon-o-calendar-days')
                                                                ->iconColor('primary')
                                                                ->weight('bold')
                                                                ->size('sm')
                                                                ->color('primary'),
                                                            Grid::make(3)->schema([
                                                                TextEntry::make('modificadoPor.name')
                                                                    ->label('Modificado por')
                                                                    ->icon('heroicon-o-user-circle')
                                                                    ->size('sm'),
                                                                TextEntry::make('usuarioAnterior.name')
                                                                    ->label('Reasignado de')
                                                                    ->placeholder('Sin asignación previa')
                                                                    ->icon('heroicon-o-arrow-left-circle')
                                                                    ->iconColor('gray')
                                                                    ->color('gray')
                                                                    ->size('sm'),
                                                                TextEntry::make('usuarioNuevo.name')
                                                                    ->label('Reasignado a')
                                                                    ->icon('heroicon-o-arrow-right-circle')
                                                                    ->iconColor('success')
                                                                    ->color('success')
                                                                    ->weight('bold')
                                                                    ->size('sm'),
                                                            ]),
                                                            TextEntry::make('motivo')
                                                                ->label('Motivo de reasignación')
                                                                ->placeholder('No se especificó un motivo')
                                                                ->columnSpanFull()
                                                                ->color('gray')
                                                                ->size('sm'),
                                                        ]),
                                                    ])
                                                    ->visible(fn ($record) => $record->reasignacionHistorial->isNotEmpty())
                                                    ->placeholder('')
                                                    ->contained(false),

                                                RepeatableEntry::make('auditorias')
                                                    ->label('Observaciones de Seguimiento')
                                                    ->schema([
                                                        Grid::make(['default' => 1])->schema([
                                                            TextEntry::make('created_at')
                                                                ->label('')
                                                                ->dateTime('d/m/Y H:i:s')
                                                                ->icon('heroicon-o-calendar-days')
                                                                ->iconColor('warning')
                                                                ->weight('bold')
                                                                ->size('sm')
                                                                ->color('warning'),
                                                            TextEntry::make('usuario.name')
                                                                ->label('Observación realizada por')
                                                                ->icon('heroicon-o-user-circle')
                                                                ->color('primary')
                                                                ->size('sm'),
                                                            TextEntry::make('observacion')
                                                                ->label('Detalle')
                                                                ->columnSpanFull()
                                                                ->placeholder('Sin detalle')
                                                                ->size('sm'),
                                                        ]),
                                                    ])
                                                    ->visible(fn ($record) => $record->auditorias->isNotEmpty())
                                                    ->placeholder('')
                                                    ->contained(false),
                                            ])
                                            ->contained(true)
                                            ->placeholder('No hay observaciones ni cambios registrados para este proyecto'),
                                    ])
                                    ->collapsible(),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 2,
                'xl' => 2,
            ])
            ->columns([
                Stack::make([
                    Split::make([
                        Stack::make([
                            TextColumn::make('nombre')
                                ->weight('bold')
                                ->size('lg')
                                ->searchable()
                                ->icon('heroicon-o-briefcase'), // Project icon
                        ])->space(1),
                        
                        TextColumn::make('prioridad')
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
                                default => 'Desc.',
                            }),
                    ])->extraAttributes(fn (Proyecto $record): array => [
                        'class' => 'items-center', 
                        'style' => 'border-left: 4px solid ' . self::getPriorityColor($record->prioridad) . '; padding-left: 12px;',
                    ]),

                    Stack::make([
                        Split::make([
                             TextColumn::make('estado')
                                ->badge()
                                ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state)))
                                ->color(fn (string $state): string => match ($state) {
                                    'pendiente' => 'gray',
                                    'en_progreso' => 'warning',
                                    'completado' => 'success',
                                    'cancelado' => 'danger',
                                    default => 'gray',
                                }),
                            TextColumn::make('tasks_count') // Show tasks count
                                ->counts('tareas')
                                ->label('Tareas')
                                ->icon('heroicon-o-clipboard-document-list')
                                ->color('gray')
                                ->size('sm'),
                        ]),
                    ])->extraAttributes(['class' => 'py-3']),

                    Split::make([
                        TextColumn::make('usuario.name') // Creator's name
                            ->color('gray')
                            ->size('xs')
                            ->icon('heroicon-o-user')
                            ->placeholder('Sin creador'),
                        
                        TextColumn::make('fecha_fin')
                            ->icon('heroicon-o-calendar')
                            ->color('gray')
                            ->size('xs')
                            ->date('d M Y')
                            ->placeholder('Sin fecha'),
                    ])
                    ->extraAttributes([
                        'class' => 'pt-3 mt-2 border-t border-gray-100 dark:border-gray-700 items-center',
                    ]),
                ])
                ->space(1)
                ->extraAttributes(function (Proyecto $record): array {
                    $baseClasses = 'p-5 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 hover:shadow-lg transition-shadow duration-300';
                    $colorClasses = match ($record->prioridad) {
                        1 => 'bg-green-50 dark:bg-green-950/20',
                        2 => 'bg-blue-50 dark:bg-blue-950/20',
                        3 => 'bg-yellow-50 dark:bg-yellow-950/20',
                        4 => 'bg-red-50 dark:bg-red-950/20',
                        default => 'bg-white dark:bg-gray-900',
                    };
                    return ['class' => "{$baseClasses} {$colorClasses}"];
                }),
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
            'index' => ManageProyectos::route('/'),
        ];
    }

    private static function getPriorityColor(int $priority): string
    {
        return match ($priority) {
            1 => '#22c55e', // Success (Baja)
            2 => '#3b82f6', // Info (Media)
            3 => '#eab308', // Warning (Alta)
            4 => '#ef4444', // Danger (Urgente)
            default => '#9ca3af', // Gray
        };
    }
}
