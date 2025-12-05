<?php

namespace App\Filament\Resources\Tareas;

use App\Filament\Resources\Tareas\Pages\ManageTareas;
use App\Models\Tarea;
use App\Models\TareaReasignacionHistorial;
use App\Models\User;
use App\Services\NotificacionService;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TareaResource extends Resource
{
    protected static ?string $model = Tarea::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            return $query;
        }

        if ($user->hasRole('lider_proyecto')) {
            return $query->whereHas('proyecto', function (Builder $query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        if ($user->hasRole('desarrollador')) {
            return $query->where('user_id', $user->id);
        }

        return $query->whereRaw('1 = 0');
    }

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
                                    ->relationship(
                                        name: 'proyecto',
                                        titleAttribute: 'nombre',
                                        modifyQueryUsing: function (Builder $query) {
                                            $user = Auth::user();
                                            if ($user->hasRole('super_admin')) {
                                                return $query;
                                            }
                                            if ($user->hasRole('lider_proyecto')) {
                                                return $query->where('user_id', $user->id);
                                            }
                                            if ($user->hasRole('desarrollador')) {
                                                return $query->whereHas('tareas', function (Builder $q) use ($user) {
                                                    $q->where('user_id', $user->id);
                                                });
                                            }
                                            return $query->whereRaw('1 = 0');
                                        }
                                    )
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
                                Select::make('user_id')
                                    ->relationship(
                                        name: 'usuario',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query, ?Tarea $record): Builder => $record ? $query->where('id', '!=', $record->user_id) : $query
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->label('Asignado a')
                                    ->placeholder('Seleccione un usuario')
                                    ->disabled(fn () => Auth::user()->hasRole('desarrollador')),
                                Textarea::make('reasignacion_motivo')
                                    ->label('Motivo de Reasignación (si aplica)')
                                    ->rows(4)
                                    ->disabled(fn () => Auth::user()->hasRole('desarrollador')),
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
                                    ])
                                    ->compact(),
                            ]),
                        Tabs\Tab::make('Historial de Reasignación')
                            ->icon('heroicon-o-arrows-right-left')
                            ->badge(fn ($record) => $record->reasignacionHistorial()->count())
                            ->visible(fn ($record) => $record->reasignacionHistorial()->exists())
                            ->schema([
                                RepeatableEntry::make('reasignacionHistorial')
                                    ->label('')
                                    ->schema([
                                        TextEntry::make('created_at')
                                            ->label('Fecha del Cambio')
                                            ->dateTime('d/m/Y H:i:s'),
                                        TextEntry::make('modificadoPor.name')
                                            ->label('Cambio realizado por'),
                                        TextEntry::make('usuarioAnterior.name')
                                            ->label('Asignado Anteriormente a')
                                            ->placeholder('N/A'),
                                        TextEntry::make('usuarioNuevo.name')
                                            ->label('Asignado a'),
                                        TextEntry::make('motivo')
                                            ->label('Motivo del cambio')
                                            ->placeholder('Sin motivo especificado.'),
                                    ])
                                    ->grid(2)
                                    ->contained(false),
                            ]),
                        // Mantengo tu Tab de Auditoría también
                        Tabs\Tab::make('Auditoría')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('created_at')
                                            ->label('Fecha de Creación')
                                            ->dateTime('d/m/Y H:i:s'),
                                        TextEntry::make('updated_at')
                                            ->label('Última Actualización')
                                            ->dateTime('d/m/Y H:i:s'),
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
                'xl' => 2, // 2 Columnas para que se vea espacioso
            ])
            ->columns([
                Stack::make([
                    // HEADER: Borde izquierdo con color + Título + Badge Prioridad
                    Split::make([
                        Stack::make([
                            TextColumn::make('nombre')
                                ->weight('bold')
                                ->size('lg')
                                ->searchable(),
                        ])->space(1),
                        
                        TextColumn::make('prioridad')
                            ->badge()
                            ->color(fn (int $state): string => match ($state) {
                                1 => 'info',
                                2 => 'primary',
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
                    ])->extraAttributes(fn (Tarea $record): array => [
                        'class' => 'items-center', 
                        'style' => 'border-left: 4px solid ' . self::getPriorityColor($record->prioridad) . '; padding-left: 12px;',
                    ]),

                    // BODY: Proyecto y Estado
                    Stack::make([
                        Split::make([
                            TextColumn::make('proyecto.nombre')
                                ->color('gray')
                                ->size('sm')
                                ->icon('heroicon-o-rectangle-stack')
                                ->limit(30),
                            
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
                        ]),
                    ])->extraAttributes(['class' => 'py-3']),

                    // BARRA DE PROGRESO (Visualmente mejor que solo texto)
                    ViewColumn::make('progreso')
                        ->view('filament.tables.columns.progress-bar')
                        ->columnSpanFull(),

                    // FOOTER: Avatar y Fecha Fin
                    Split::make([
                        Stack::make([
                            ImageColumn::make('usuario.name')
                                ->circular()
                                ->size('xs')
                                ->defaultImageUrl(fn (Tarea $record): string => 'https://ui-avatars.com/api/?name=' . urlencode($record->usuario?->name ?? '?') . '&background=random'),
                            
                            TextColumn::make('usuario.name')
                                ->color('gray')
                                ->size('xs')
                                ->placeholder('Sin asignar'),
                        ])->visibleFrom('md'),

                        Stack::make([
                            TextColumn::make('fecha_fin')
                                ->icon('heroicon-o-calendar')
                                ->color('gray')
                                ->size('xs')
                                ->date('d M Y')
                                ->placeholder('Sin fecha'),
                        ])->alignment('end'),
                    ])
                    ->extraAttributes([
                        'class' => 'pt-3 mt-2 border-t border-gray-100 dark:border-gray-700 items-center',
                    ]),
                ])
                ->space(1)
                ->extraAttributes([
                    'class' => 'p-5 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 hover:shadow-md transition-shadow duration-300',
                ]),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                // AQUÍ ESTÁ TU LÓGICA PERSONALIZADA DE EDICIÓN CONSERVADA
                EditAction::make()
                    ->using(function (Model $record, array $data) {
                        $userIdAnterior = $record->getOriginal('user_id');
                        $userIdNuevo = $data['user_id'];
                        $estadoAnterior = $record->getOriginal('estado');
                        $estadoNuevo = $data['estado'] ?? $estadoAnterior;

                        // Detectar reasignación de tarea
                        if ($userIdAnterior != $userIdNuevo) {
                            TareaReasignacionHistorial::create([
                                'tarea_id' => $record->id,
                                'usuario_anterior_id' => $userIdAnterior,
                                'usuario_nuevo_id' => $userIdNuevo,
                                'modificado_por_id' => auth()->id(),
                                'motivo' => $data['reasignacion_motivo'] ?? null,
                            ]);

                            // Notificar reasignación
                            $usuarioAnterior = $userIdAnterior ? User::find($userIdAnterior) : null;
                            $usuarioNuevo = User::find($userIdNuevo);
                            NotificacionService::notificarTareaReasignada(
                                $record,
                                $usuarioAnterior,
                                $usuarioNuevo,
                                auth()->user(),
                                $data['reasignacion_motivo'] ?? null
                            );
                        }

                        // Detectar cambio de estado
                        if ($estadoAnterior != $estadoNuevo) {
                            NotificacionService::notificarCambioEstadoTarea(
                                $record,
                                $estadoAnterior,
                                $estadoNuevo,
                                auth()->user()
                            );
                        }

                        // Limpiamos el motivo antes de guardar la tarea (ya que no es columna de Tarea)
                        unset($data['reasignacion_motivo']);
                        $record->update($data);
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    // Helper para el color del borde (necesario para el diseño visual)
    private static function getPriorityColor(int $priority): string
    {
        return match ($priority) {
            1 => '#3b82f6', // Info
            2 => '#6366f1', // Primary
            3 => '#eab308', // Warning
            4 => '#ef4444', // Danger
            default => '#9ca3af',
        };
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTareas::route('/'),
        ];
    }
}