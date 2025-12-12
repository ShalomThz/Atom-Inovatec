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
                                    ->description('Datos principales del proyecto')
                                    ->schema([
                                        Grid::make(['default' => 1, 'md' => 2])->schema([
                                            TextEntry::make('nombre')
                                                ->label('Nombre del Proyecto')
                                                ->size('lg')
                                                ->weight('bold')
                                                ->icon('heroicon-o-rectangle-stack')
                                                ->color('primary'),
                                            TextEntry::make('usuario.name')
                                                ->label('Creado por')
                                                ->icon('heroicon-o-user')
                                                ->iconColor('primary'),
                                        ]),
                                        Grid::make(['default' => 1, 'md' => 1])->schema([
                                            TextEntry::make('descripcion')
                                                ->label('Descripción')
                                                ->placeholder('Sin descripción')
                                                ->columnSpanFull(),
                                        ]),
                                        Grid::make(['default' => 1, 'md' => 2])->schema([
                                            TextEntry::make('usuario.email')
                                                ->label('Email del Creador')
                                                ->icon('heroicon-o-envelope')
                                                ->copyable()
                                                ->copyMessage('Email copiado')
                                                ->copyMessageDuration(1500)
                                                ->color('blue'),
                                        ]),
                                    ])
                                    ->compact(),
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
                                                    ->icon('heroicon-o-play'),
                                                TextEntry::make('fecha_fin')
                                                    ->label('Fecha de Fin')
                                                    ->date('d/m/Y')
                                                    ->placeholder('Sin fecha de fin')
                                                    ->icon('heroicon-o-flag'),
                                            ])
                                            ->columns(1),

                                        Section::make('Estado del Proyecto')
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

                        Tabs\Tab::make('Presupuesto')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        TextEntry::make('presupuesto')
                                            ->label('Presupuesto Total')
                                            ->money('USD')
                                            ->placeholder('Sin presupuesto definido')
                                            ->size('lg')
                                            ->weight('bold')
                                            ->icon('heroicon-o-currency-dollar')
                                            ->iconColor('success'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Tareas')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->badge(fn ($record) => $record->tareas->count())
                            ->schema([
                                RepeatableEntry::make('tareas')
                                    ->label('Lista de Tareas')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextEntry::make('nombre')
                                                    ->label('Nombre')
                                                    ->weight('bold')
                                                    ->size('md'),
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
                                                TextEntry::make('progreso')
                                                    ->label('Progreso')
                                                    ->suffix('%')
                                                    ->color(fn (int $state): string => match (true) {
                                                        $state === 100 => 'success',
                                                        $state >= 50 => 'warning',
                                                        default => 'gray',
                                                    }),
                                            ]),
                                        Grid::make(3)
                                            ->schema([
                                                TextEntry::make('usuario.name')
                                                    ->label('Asignado a')
                                                    ->icon('heroicon-o-user')
                                                    ->placeholder('Sin asignar'),
                                                TextEntry::make('fecha_inicio')
                                                    ->label('Inicio')
                                                    ->date('d/m/Y')
                                                    ->placeholder('-'),
                                                TextEntry::make('fecha_fin')
                                                    ->label('Fin')
                                                    ->date('d/m/Y')
                                                    ->placeholder('-'),
                                            ]),
                                        TextEntry::make('descripcion')
                                            ->label('Descripción')
                                            ->placeholder('Sin descripción')
                                            ->columnSpanFull(),
                                    ])
                                    ->contained(false)
                                    ->placeholder('No hay tareas asociadas a este proyecto'),
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
