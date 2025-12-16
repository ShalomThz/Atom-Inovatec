<?php

namespace App\Filament\Pages;

use App\Models\Proyecto;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Reportes extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;
    protected static ?string $navigationLabel = 'Reportes';
    protected static ?string $title = 'Generador de Reportes';
    protected static ?int $navigationSort = 50;
    protected string $view = 'filament.pages.reportes';
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('proyecto_id')->label('Filtrar por Proyecto')->options(fn() => Proyecto::pluck('nombre', 'id'))->searchable()->placeholder('Todos los proyectos'),
            Select::make('user_id')->label('Filtrar por Líder de Proyecto')->options(fn() => User::whereHas('roles', function($q) { $q->where('name', 'lider_proyecto'); })->pluck('name', 'id'))->searchable()->placeholder('Todos los líderes'),
            Select::make('estado_proyecto')->label('Estado del Proyecto')->options(['pendiente' => 'Pendiente', 'en_progreso' => 'En Progreso', 'completado' => 'Completado', 'cancelado' => 'Cancelado'])->placeholder('Todos los estados'),
            Select::make('prioridad')->label('Prioridad del Proyecto')->options(['baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta', 'urgente' => 'Urgente'])->placeholder('Todas las prioridades'),
            Select::make('empleado_id')->label('Filtrar por Empleado (Tareas)')->options(fn() => User::whereHas('roles', function($q) { $q->whereIn('name', ['desarrollador', 'lider_proyecto']); })->pluck('name', 'id'))->searchable()->placeholder('Todos los empleados'),
            Select::make('estado_tarea')->label('Estado de Tareas')->options(['pendiente' => 'Pendiente', 'en_progreso' => 'En Progreso', 'completada' => 'Completada', 'bloqueada' => 'Bloqueada'])->placeholder('Todos los estados'),
            Select::make('prioridad_tarea')->label('Prioridad de Tareas')->options(['baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta', 'urgente' => 'Urgente'])->placeholder('Todas las prioridades'),
            DatePicker::make('fecha_desde')->label('Fecha Inicio Desde')->placeholder('Sin filtro'),
            DatePicker::make('fecha_hasta')->label('Fecha Fin Hasta')->after('fecha_desde')->placeholder('Sin filtro'),
            Select::make('con_retraso')->label('Proyectos con Retraso')->options(['1' => 'Solo Proyectos Retrasados', '0' => 'Solo Proyectos a Tiempo'])->placeholder('Todos'),
            Select::make('progreso_min')->label('Progreso Mínimo')->options(['0' => '0%', '25' => '25%', '50' => '50%', '75' => '75%', '90' => '90%'])->placeholder('Sin mínimo'),
            Select::make('progreso_max')->label('Progreso Máximo')->options(['10' => '10%', '25' => '25%', '50' => '50%', '75' => '75%', '100' => '100%'])->placeholder('Sin máximo'),
        ];
    }

    protected function getFormStatePath(): string { return 'data'; }
    protected function getFormColumns(): int { return 3; }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')->label('Previsualizar PDF')->icon('heroicon-o-eye')->color('gray')->action(function () { $params = array_filter($this->form->getState()); $params['output'] = 'stream'; $url = route('reporte.proyectos.pdf', $params); $this->js("window.open('$url', '_blank')"); }),
            Action::make('download')->label('Descargar PDF')->icon('heroicon-o-arrow-down-tray')->color('primary')->action(function () { $params = array_filter($this->form->getState()); $params['output'] = 'download'; $url = route('reporte.proyectos.pdf', $params); $this->js("window.location.href = '$url'"); }),
        ];
    }
}
