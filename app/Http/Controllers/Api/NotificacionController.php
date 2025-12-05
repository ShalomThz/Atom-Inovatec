<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notificacion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $usuario = $request->user();

        $notificaciones = Notificacion::where('user_id', $usuario->id)
            ->with('notificable')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $noLeidas = Notificacion::where('user_id', $usuario->id)
            ->noLeidas()
            ->count();

        return response()->json([
            'notificaciones' => $notificaciones,
            'no_leidas' => $noLeidas,
        ]);
    }

    public function noLeidas(Request $request): JsonResponse
    {
        $usuario = $request->user();

        $notificaciones = Notificacion::where('user_id', $usuario->id)
            ->noLeidas()
            ->with('notificable')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'notificaciones' => $notificaciones,
            'total' => $notificaciones->count(),
        ]);
    }

    public function marcarComoLeida(Request $request, int $id): JsonResponse
    {
        $usuario = $request->user();

        $notificacion = Notificacion::where('user_id', $usuario->id)
            ->findOrFail($id);

        $notificacion->marcarComoLeida();

        return response()->json([
            'mensaje' => 'Notificación marcada como leída',
            'notificacion' => $notificacion,
        ]);
    }

    public function marcarTodasComoLeidas(Request $request): JsonResponse
    {
        $usuario = $request->user();

        $count = Notificacion::where('user_id', $usuario->id)
            ->noLeidas()
            ->update([
                'leida' => true,
                'leida_en' => now(),
            ]);

        return response()->json([
            'mensaje' => 'Todas las notificaciones marcadas como leídas',
            'total' => $count,
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $usuario = $request->user();

        $notificacion = Notificacion::where('user_id', $usuario->id)
            ->findOrFail($id);

        $notificacion->delete();

        return response()->json([
            'mensaje' => 'Notificación eliminada',
        ]);
    }

    public function eliminarLeidas(Request $request): JsonResponse
    {
        $usuario = $request->user();

        $count = Notificacion::where('user_id', $usuario->id)
            ->where('leida', true)
            ->delete();

        return response()->json([
            'mensaje' => 'Notificaciones leídas eliminadas',
            'total' => $count,
        ]);
    }
}
