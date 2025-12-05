<?php

namespace App\Services;

use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class NotificacionService
{
    public static function crearNotificacion(
        User $usuario,
        string $tipo,
        string $titulo,
        string $mensaje,
        Model $notificable = null,
        array $datosAdicionales = []
    ): Notificacion {
        return Notificacion::create([
            'user_id' => $usuario->id,
            'tipo' => $tipo,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'notificable_type' => $notificable ? get_class($notificable) : null,
            'notificable_id' => $notificable?->id,
            'datos_adicionales' => $datosAdicionales,
        ]);
    }

    public static function notificarTareaAsignada($tarea, $asignadoPor = null): void
    {
        if (!$tarea->user_id) {
            return;
        }

        $usuario = User::find($tarea->user_id);
        if (!$usuario) {
            return;
        }

        $proyecto = $tarea->proyecto;
        $nombreAsignador = $asignadoPor ? $asignadoPor->name : 'el sistema';

        self::crearNotificacion(
            $usuario,
            'tarea_asignada',
            'Nueva tarea asignada',
            "Se te ha asignado la tarea '{$tarea->nombre}' en el proyecto '{$proyecto->nombre}' por {$nombreAsignador}",
            $tarea,
            [
                'tarea_id' => $tarea->id,
                'proyecto_id' => $proyecto->id,
                'asignado_por' => $asignadoPor?->id,
            ]
        );
    }

    public static function notificarTareaReasignada($tarea, $usuarioAnterior, $usuarioNuevo, $modificadoPor, $motivo = null): void
    {
        if (!$usuarioNuevo) {
            return;
        }

        $proyecto = $tarea->proyecto;
        $mensajeBase = "Se te ha reasignado la tarea '{$tarea->nombre}' en el proyecto '{$proyecto->nombre}'";

        if ($usuarioAnterior) {
            $mensajeBase .= " (anteriormente asignada a {$usuarioAnterior->name})";
        }

        if ($motivo) {
            $mensajeBase .= ". Motivo: {$motivo}";
        }

        self::crearNotificacion(
            $usuarioNuevo,
            'tarea_reasignada',
            'Tarea reasignada',
            $mensajeBase,
            $tarea,
            [
                'tarea_id' => $tarea->id,
                'proyecto_id' => $proyecto->id,
                'usuario_anterior_id' => $usuarioAnterior?->id,
                'modificado_por_id' => $modificadoPor->id,
                'motivo' => $motivo,
            ]
        );
    }

    public static function notificarCambioEstadoTarea($tarea, $estadoAnterior, $estadoNuevo, $modificadoPor): void
    {
        if (!$tarea->user_id || $tarea->user_id === $modificadoPor->id) {
            return;
        }

        $usuario = User::find($tarea->user_id);
        if (!$usuario) {
            return;
        }

        self::crearNotificacion(
            $usuario,
            'tarea_estado_cambiado',
            'Estado de tarea actualizado',
            "El estado de tu tarea '{$tarea->nombre}' cambió de '{$estadoAnterior}' a '{$estadoNuevo}' por {$modificadoPor->name}",
            $tarea,
            [
                'tarea_id' => $tarea->id,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $estadoNuevo,
                'modificado_por_id' => $modificadoPor->id,
            ]
        );
    }
}
