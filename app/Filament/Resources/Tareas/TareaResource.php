<?php

namespace App\Filament\Resources\Tareas;

use App\Filament\Resources\Tareas\Pages\ManageTareas;
use App\Models\Tarea;
use App\Models\TareaReasignacionHistorial;
use App\Models\User;
use App\Services\NotificacionService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
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
                                    ->helperText('Ingrese un valor entre 0 y 100')
                                    ->live(onBlur: true)
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
                                    ->description('Datos principales y resumen de la tarea')
                                    ->icon('heroicon-o-clipboard-document')
                                    ->schema([
                                        TextEntry::make('nombre')
                                            ->label('Nombre de la Tarea')
                                            ->size('xl')
                                            ->weight('bold')
                                            ->icon('heroicon-o-clipboard-document-list')
                                            ->iconColor('primary')
                                            ->color('primary')
                                            ->columnSpanFull(),
                                        Grid::make(['default' => 1, 'md' => 2])->schema([
                                            TextEntry::make('proyecto.nombre')
                                                ->label('Proyecto Asociado')
                                                ->icon('heroicon-o-rectangle-stack')
                                                ->iconColor('primary')
                                                ->weight('semibold')
                                                ->size('md')
                                                ->color('primary'),
                                            TextEntry::make('usuario.name')
                                                ->label('Asignado a')
                                                ->icon('heroicon-o-user-circle')
                                                ->iconColor('success')
                                                ->placeholder('Sin asignar')
                                                ->weight('semibold')
                                                ->size('md')
                                                ->color('success'),
                                        ]),
                                        TextEntry::make('usuario.email')
                                            ->label('Correo electrónico')
                                            ->icon('heroicon-o-envelope')
                                            ->iconColor('gray')
                                            ->placeholder('Sin correo')
                                            ->copyable()
                                            ->copyMessage('Email copiado')
                                            ->copyMessageDuration(1500)
                                            ->color('gray')
                                            ->size('sm'),
                                        TextEntry::make('descripcion')
                                            ->label('Descripción')
                                            ->placeholder('Sin descripción')
                                            ->columnSpanFull()
                                            ->color('gray')
                                            ->lineClamp(3),
                                    ])
                                    ->compact()
                                    ->collapsible(),

                                Section::make('Estado y Métricas')
                                    ->description('Estado actual y progreso de la tarea')
                                    ->icon('heroicon-o-chart-bar-square')
                                    ->schema([
                                        Grid::make(['default' => 2, 'md' => 4])->schema([
                                            TextEntry::make('estado')
                                                ->label('Estado')
                                                ->badge()
                                                ->size('lg')
                                                ->weight('semibold')
                                                ->formatStateUsing(fn (string $state): string => match ($state) {
                                                    'pendiente' => 'Pendiente',
                                                    'en_progreso' => 'En Progreso',
                                                    'completada' => 'Completada',
                                                    'cancelada' => 'Cancelada',
                                                    default => ucfirst($state),
                                                })
                                                ->color(fn (string $state): string => match ($state) {
                                                    'pendiente' => 'gray',
                                                    'en_progreso' => 'warning',
                                                    'completada' => 'success',
                                                    'cancelada' => 'danger',
                                                    default => 'gray',
                                                })
                                                ->icon(fn (string $state): string => match ($state) {
                                                    'pendiente' => 'heroicon-o-clock',
                                                    'en_progreso' => 'heroicon-o-arrow-path',
                                                    'completada' => 'heroicon-o-check-circle',
                                                    'cancelada' => 'heroicon-o-x-circle',
                                                    default => 'heroicon-o-question-mark-circle',
                                                }),
                                            TextEntry::make('prioridad')
                                                ->label('Prioridad')
                                                ->badge()
                                                ->size('lg')
                                                ->weight('semibold')
                                                ->formatStateUsing(fn (int $state): string => match ($state) {
                                                    1 => 'Baja',
                                                    2 => 'Media',
                                                    3 => 'Alta',
                                                    4 => 'Urgente',
                                                    default => 'Desconocida',
                                                })
                                                ->color(fn (int $state): string => match ($state) {
                                                    1 => 'success',
                                                    2 => 'info',
                                                    3 => 'warning',
                                                    4 => 'danger',
                                                    default => 'gray',
                                                })
                                                ->icon(fn (int $state): string => match ($state) {
                                                    1 => 'heroicon-o-arrow-down',
                                                    2 => 'heroicon-o-minus',
                                                    3 => 'heroicon-o-arrow-up',
                                                    4 => 'heroicon-o-exclamation-triangle',
                                                    default => 'heroicon-o-question-mark-circle',
                                                }),
                                            TextEntry::make('progreso')
                                                ->label('Progreso')
                                                ->suffix('%')
                                                ->badge()
                                                ->size('lg')
                                                ->weight('semibold')
                                                ->icon('heroicon-o-chart-bar')
                                                ->color(fn (int $state): string => match (true) {
                                                    $state === 100 => 'success',
                                                    $state >= 75 => 'info',
                                                    $state >= 50 => 'warning',
                                                    default => 'gray',
                                                }),
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
                                                ->size('lg')
                                                ->weight('semibold')
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
                                    ])
                                    ->compact(),

                                Section::make('Cronograma')
                                    ->description('Fechas de inicio y finalización de la tarea')
                                    ->icon('heroicon-o-calendar-days')
                                    ->iconColor('primary')
                                    ->schema([
                                        Grid::make(['default' => 1, 'md' => 3])->schema([
                                            TextEntry::make('fecha_inicio')
                                                ->label('Inicia')
                                                ->icon('heroicon-o-calendar')
                                                ->iconColor('success')
                                                ->date('d/m/Y')
                                                ->placeholder('No definida')
                                                ->weight('medium')
                                                ->color('success')
                                                ->size('md'),
                                            TextEntry::make('fecha_fin')
                                                ->label('Finaliza')
                                                ->icon('heroicon-o-calendar')
                                                ->iconColor('danger')
                                                ->date('d/m/Y')
                                                ->placeholder('No definida')
                                                ->weight('medium')
                                                ->color('danger')
                                                ->size('md'),
                                            TextEntry::make('duracion')
                                                ->label('Duración')
                                                ->state(function ($record) {
                                                    if (!$record->fecha_inicio || !$record->fecha_fin) {
                                                        return 'N/A';
                                                    }
                                                    $inicio = \Carbon\Carbon::parse($record->fecha_inicio);
                                                    $fin = \Carbon\Carbon::parse($record->fecha_fin);
                                                    $dias = (int) $inicio->diffInDays($fin);
                                                    return $dias . ' días';
                                                })
                                                ->icon('heroicon-o-clock')
                                                ->iconColor('primary')
                                                ->badge()
                                                ->color('info')
                                                ->size('md'),
                                        ]),
                                    ])
                                    ->compact()
                                    ->collapsible()
                                    ->collapsed(),
                            ]),
                        Tabs\Tab::make('Historial de Reasignación')
                            ->icon('heroicon-o-arrows-right-left')
                            ->badge(fn ($record) => $record->reasignacionHistorial()->count())
                            ->visible(fn ($record) => $record->reasignacionHistorial()->exists())
                            ->schema([
                                Section::make('Reasignaciones de la Tarea')
                                    ->description('Registro cronológico de todas las reasignaciones')
                                    ->icon('heroicon-o-clock')
                                    ->iconColor('primary')
                                    ->schema([
                                        RepeatableEntry::make('reasignacionHistorial')
                                            ->label('')
                                            ->schema([
                                                Grid::make(['default' => 1])->schema([
                                                    TextEntry::make('created_at')
                                                        ->label('')
                                                        ->dateTime('d/m/Y H:i:s')
                                                        ->icon('heroicon-o-calendar-days')
                                                        ->iconColor('primary')
                                                        ->weight('bold')
                                                        ->size('md')
                                                        ->color('primary'),
                                                    Grid::make(['default' => 1, 'md' => 3])->schema([
                                                        TextEntry::make('modificadoPor.name')
                                                            ->label('Modificado por')
                                                            ->icon('heroicon-o-user-circle')
                                                            ->iconColor('primary')
                                                            ->size('sm')
                                                            ->weight('medium'),
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
                                            ->contained(true)
                                            ->placeholder('No hay historial de reasignaciones'),
                                    ])
                                    ->collapsible(),
                            ]),

                        Tabs\Tab::make('Historial de Seguimiento')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->badge(fn ($record) => $record->auditorias()->count())
                            ->visible(fn ($record) => $record->auditorias()->exists())
                            ->schema([
                                Section::make('Observaciones de Seguimiento')
                                    ->description('Registro cronológico de todas las observaciones')
                                    ->icon('heroicon-o-document-text')
                                    ->iconColor('warning')
                                    ->schema([
                                        RepeatableEntry::make('auditorias')
                                            ->label('')
                                            ->schema([
                                                Grid::make(['default' => 1])->schema([
                                                    TextEntry::make('created_at')
                                                        ->label('')
                                                        ->dateTime('d/m/Y H:i:s')
                                                        ->icon('heroicon-o-calendar-days')
                                                        ->iconColor('warning')
                                                        ->weight('bold')
                                                        ->size('md')
                                                        ->color('warning'),
                                                    TextEntry::make('usuario.name')
                                                        ->label('Observación realizada por')
                                                        ->icon('heroicon-o-user-circle')
                                                        ->iconColor('primary')
                                                        ->color('primary')
                                                        ->weight('medium')
                                                        ->size('sm'),
                                                    TextEntry::make('observacion')
                                                        ->label('Detalle de la observación')
                                                        ->columnSpanFull()
                                                        ->placeholder('Sin detalle')
                                                        ->color('gray')
                                                        ->size('sm'),
                                                ]),
                                            ])
                                            ->contained(true)
                                            ->placeholder('No hay observaciones registradas'),
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

                    ViewColumn::make('progreso')
                        ->view('filament.tables.columns.progress-bar')
                        ->columnSpanFull(),

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
                Action::make('addObservation')
                    ->label('Seguimiento')
                    ->icon('heroicon-o-chat-bubble-bottom-center-text')
                    ->color('info')
                    ->modalHeading('Añadir Observación de Seguimiento')
                    ->modalSubmitActionLabel('Guardar Observación')
                    ->form([
                        Textarea::make('observacion')
                            ->label('Observación')
                            ->required(),
                    ])
                    ->action(function (Tarea $record, array $data) {
                        $record->auditorias()->create([
                            'user_id' => auth()->id(),
                            'observacion' => $data['observacion'],
                        ]);
                        Notification::make()
                            ->title('Observación guardada con éxito')
                            ->success()
                            ->send();
                    }),
                ViewAction::make(),
                EditAction::make()
                    ->using(function (Model $record, array $data) {
                        $userIdAnterior = $record->getOriginal('user_id');
                        $userIdNuevo = $data['user_id'];
                        $estadoAnterior = $record->getOriginal('estado');
                        $estadoNuevo = $data['estado'] ?? $estadoAnterior;

                        if ($userIdAnterior != $userIdNuevo) {
                            TareaReasignacionHistorial::create([
                                'tarea_id' => $record->id,
                                'usuario_anterior_id' => $userIdAnterior,
                                'usuario_nuevo_id' => $userIdNuevo,
                                'modificado_por_id' => auth()->id(),
                                'motivo' => $data['reasignacion_motivo'] ?? null,
                            ]);

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

                        if ($estadoAnterior != $estadoNuevo) {
                            NotificacionService::notificarCambioEstadoTarea(
                                $record,
                                $estadoAnterior,
                                $estadoNuevo,
                                auth()->user()
                            );
                        }

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