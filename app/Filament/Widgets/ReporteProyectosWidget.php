<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ReporteProyectosWidget extends Widget
{
    protected string $view = 'filament.widgets.reporte-proyectos-widget';

    protected int | string | array $columnSpan = 'full';

    public function descargarReporteGeneral()
    {
        $url = route('reporte.proyectos.pdf', ['output' => 'download']);
        $this->js("window.location.href = '$url'");
    }
}
