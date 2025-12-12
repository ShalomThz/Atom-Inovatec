<?php

namespace App\Console\Commands;

use App\Models\Tarea;
use App\Services\NotificacionService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendDueDateReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-due-date-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía notificaciones para tareas con fecha de vencimiento próxima.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Buscando tareas con vencimiento próximo...');

        $diasDeAviso = 3;
        $hoy = Carbon::today();
        $fechaLimite = $hoy->copy()->addDays($diasDeAviso);

        $tareas = Tarea::whereNotNull('user_id')
            ->whereNotNull('fecha_fin')
            ->whereBetween('fecha_fin', [$hoy, $fechaLimite])
            ->whereNotIn('estado', ['completado', 'cancelado'])
            ->get();

        if ($tareas->isEmpty()) {
            $this->info('No se encontraron tareas próximas a vencer.');
            return;
        }

        $this->info("Se encontraron {$tareas->count()} tareas. Enviando notificaciones...");

        foreach ($tareas as $tarea) {
            $diasRestantes = $hoy->diffInDays($tarea->fecha_fin->startOfDay(), false);
            NotificacionService::notificarVencimientoProximo($tarea, $diasRestantes);
        }

        $this->info('Notificaciones de vencimiento enviadas correctamente.');
    }
}
