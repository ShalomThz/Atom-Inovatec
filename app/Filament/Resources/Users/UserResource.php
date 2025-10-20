<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section as FormSection;
use Filament\Schemas\Components\Tabs as FormTabs;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FormTabs::make('Usuario')
                    ->tabs([
                        FormTabs\Tab::make('Información Personal')
                            ->icon('heroicon-o-user')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre Completo')
                                    ->required()
                                    ->maxLength(255)
                                    ->autocomplete('name')
                                    ->placeholder('Ingrese el nombre completo')
                                    ->columnSpan(2),
                                TextInput::make('email')
                                    ->label('Correo Electrónico')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->autocomplete('email')
                                    ->placeholder('usuario@ejemplo.com')
                                    ->columnSpan(2),
                                Select::make('roles')
                                    ->label('Rol del Usuario')
                                    ->relationship('roles', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->placeholder('Seleccione uno o más roles')
                                    ->helperText('Puede asignar múltiples roles al usuario')
                                    ->columnSpan(2),
                            ])->columns(2),

                        FormTabs\Tab::make('Seguridad')
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                TextInput::make('password')
                                    ->label('Contraseña')
                                    ->password()
                                    ->required(fn ($context) => $context === 'create')
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->minLength(8)
                                    ->maxLength(255)
                                    ->autocomplete('new-password')
                                    ->placeholder('Mínimo 8 caracteres')
                                    ->helperText('Dejar en blanco para mantener la contraseña actual'),
                                TextInput::make('password_confirmation')
                                    ->label('Confirmar Contraseña')
                                    ->password()
                                    ->required(fn ($context) => $context === 'create')
                                    ->dehydrated(false)
                                    ->maxLength(255)
                                    ->autocomplete('new-password')
                                    ->same('password')
                                    ->placeholder('Confirme la contraseña'),
                            ])->columns(2),

                        FormTabs\Tab::make('Verificación')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                FormSection::make('Verificación de Email')
                                    ->description('Estado de verificación del correo electrónico')
                                    ->schema([
                                        DateTimePicker::make('email_verified_at')
                                            ->label('Email Verificado en')
                                            ->native(false)
                                            ->displayFormat('d/m/Y H:i:s')
                                            ->placeholder('No verificado'),
                                    ]),

                                FormSection::make('Autenticación de Dos Factores')
                                    ->description('Configuración de 2FA')
                                    ->schema([
                                        DateTimePicker::make('two_factor_confirmed_at')
                                            ->label('2FA Confirmado en')
                                            ->native(false)
                                            ->displayFormat('d/m/Y H:i:s')
                                            ->placeholder('No configurado'),
                                    ])
                                    ->collapsed(),
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
                Tabs::make('Información del Usuario')
                    ->tabs([
                        Tabs\Tab::make('Perfil')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make('Información Personal')
                                    ->description('Datos del perfil del usuario')
                                    ->schema([
                                        Grid::make(['default' => 1, 'md' => 2])->schema([
                                            TextEntry::make('name')
                                                ->label('Nombre Completo')
                                                ->icon('heroicon-o-user-circle')
                                                ->color('primary')
                                                ->size('lg')
                                                ->weight('bold'),
                                            TextEntry::make('email')
                                                ->label('Correo Electrónico')
                                                ->icon('heroicon-o-envelope')
                                                ->copyable()
                                                ->copyMessage('Email copiado')
                                                ->color('blue'),
                                        ]),
                                        Grid::make(['default' => 1, 'md' => 1])->schema([
                                            TextEntry::make('roles.name')
                                                ->label('Roles Asignados')
                                                ->badge()
                                                ->color('info')
                                                ->icon('heroicon-o-shield-check')
                                                ->separator(',')
                                                ->placeholder('Sin roles asignados')
                                                ->formatStateUsing(fn ($state) => ucfirst($state)),
                                        ]),
                                    ])
                                    ->compact(),
                            ]),

                        Tabs\Tab::make('Verificación y Seguridad')
                            ->icon('heroicon-o-shield-check')
                            ->badge(fn ($record) => $record->email_verified_at ? '✓' : null)
                            ->schema([
                                Section::make('Estado de Verificación')
                                    ->description('Verificación de email y autenticación')
                                    ->schema([
                                        Grid::make(['default' => 1, 'md' => 2])->schema([
                                            TextEntry::make('email_verified_at')
                                                ->label('Email Verificado')
                                                ->dateTime('d/m/Y H:i:s')
                                                ->placeholder('No verificado')
                                                ->icon('heroicon-o-check-badge')
                                                ->color(fn ($state) => $state ? 'success' : 'warning')
                                                ->formatStateUsing(fn ($state) => $state ? $state : 'Pendiente de verificación')
                                                ->badge(),
                                            TextEntry::make('two_factor_confirmed_at')
                                                ->label('Autenticación 2FA')
                                                ->dateTime('d/m/Y H:i:s')
                                                ->placeholder('No configurado')
                                                ->icon('heroicon-o-shield-check')
                                                ->color(fn ($state) => $state ? 'success' : 'gray')
                                                ->formatStateUsing(fn ($state) => $state ? 'Activo desde ' . $state : 'No configurado')
                                                ->badge(),
                                        ]),
                                    ])
                                    ->compact(),
                            ]),

                        Tabs\Tab::make('Proyectos')
                            ->icon('heroicon-o-rectangle-stack')
                            ->badge(fn ($record) => $record->proyectos->count())
                            ->schema([
                                RepeatableEntry::make('proyectos')
                                    ->label('Proyectos Creados')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            TextEntry::make('nombre')
                                                ->label('Nombre del Proyecto')
                                                ->weight('bold')
                                                ->size('md')
                                                ->icon('heroicon-o-rectangle-stack'),
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
                                        ]),
                                        Grid::make(3)->schema([
                                            TextEntry::make('fecha_inicio')
                                                ->label('Fecha Inicio')
                                                ->date('d/m/Y')
                                                ->icon('heroicon-o-play'),
                                            TextEntry::make('fecha_fin')
                                                ->label('Fecha Fin')
                                                ->date('d/m/Y')
                                                ->placeholder('Sin fecha')
                                                ->icon('heroicon-o-flag'),
                                            TextEntry::make('presupuesto')
                                                ->label('Presupuesto')
                                                ->money('USD')
                                                ->placeholder('No definido'),
                                        ]),
                                    ])
                                    ->contained(false)
                                    ->placeholder('No ha creado ningún proyecto'),
                            ]),

                        Tabs\Tab::make('Tareas Asignadas')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->badge(fn ($record) => $record->tareasAsignadas->count())
                            ->schema([
                                RepeatableEntry::make('tareasAsignadas')
                                    ->label('Tareas Asignadas')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            TextEntry::make('nombre')
                                                ->label('Tarea')
                                                ->weight('bold')
                                                ->icon('heroicon-o-clipboard-document-list'),
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
                                        Grid::make(2)->schema([
                                            TextEntry::make('proyecto.nombre')
                                                ->label('Proyecto')
                                                ->icon('heroicon-o-rectangle-stack')
                                                ->color('primary'),
                                            TextEntry::make('fecha_fin')
                                                ->label('Fecha Límite')
                                                ->date('d/m/Y')
                                                ->placeholder('Sin fecha')
                                                ->icon('heroicon-o-calendar'),
                                        ]),
                                    ])
                                    ->contained(false)
                                    ->placeholder('No tiene tareas asignadas'),
                            ]),

                        Tabs\Tab::make('Sistema')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Section::make('Información del Sistema')
                                    ->description('Registro de fechas de creación y modificación')
                                    ->schema([
                                        Grid::make(['default' => 1, 'md' => 3])->schema([
                                            TextEntry::make('id')
                                                ->label('ID de Usuario')
                                                ->numeric()
                                                ->icon('heroicon-o-hashtag')
                                                ->fontFamily('mono')
                                                ->copyable()
                                                ->color('gray'),
                                            TextEntry::make('created_at')
                                                ->label('Fecha de Creación')
                                                ->dateTime('d/m/Y H:i:s')
                                                ->icon('heroicon-o-plus-circle')
                                                ->color('green'),
                                            TextEntry::make('updated_at')
                                                ->label('Última Actualización')
                                                ->dateTime('d/m/Y H:i:s')
                                                ->icon('heroicon-o-pencil-square')
                                                ->color('orange')
                                                ->since(),
                                        ]),
                                    ])
                                    ->compact()
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
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('two_factor_confirmed_at')
                    ->dateTime()
                    ->sortable(),
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
            'index' => ManageUsers::route('/'),
        ];
    }
}
