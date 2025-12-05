import { Bell, Check, CheckCheck, Trash2, X } from 'lucide-react';
import { useState } from 'react';
import { Button } from './ui/button';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from './ui/dropdown-menu';
import { ScrollArea } from './ui/scroll-area';
import { useNotifications, type Notificacion } from '@/hooks/use-notifications';
import { cn } from '@/lib/utils';

export function NotificationCenter() {
  const {
    notificaciones,
    noLeidas,
    loading,
    marcarComoLeida,
    marcarTodasComoLeidas,
    eliminarNotificacion,
  } = useNotifications();

  const [open, setOpen] = useState(false);

  const getIconoTipo = (tipo: string) => {
    switch (tipo) {
      case 'tarea_asignada':
        return '📋';
      case 'tarea_reasignada':
        return '🔄';
      case 'tarea_estado_cambiado':
        return '✅';
      default:
        return '🔔';
    }
  };

  const formatearFecha = (fecha: string) => {
    const date = new Date(fecha);
    const ahora = new Date();
    const diff = ahora.getTime() - date.getTime();
    const minutos = Math.floor(diff / 60000);
    const horas = Math.floor(diff / 3600000);
    const dias = Math.floor(diff / 86400000);

    if (minutos < 1) return 'Ahora';
    if (minutos < 60) return `Hace ${minutos} min`;
    if (horas < 24) return `Hace ${horas}h`;
    if (dias < 7) return `Hace ${dias}d`;
    return date.toLocaleDateString('es-ES', { day: '2-digit', month: 'short' });
  };

  const handleMarcarComoLeida = async (e: React.MouseEvent, notificacion: Notificacion) => {
    e.stopPropagation();
    if (!notificacion.leida) {
      await marcarComoLeida(notificacion.id);
    }
  };

  const handleEliminar = async (e: React.MouseEvent, id: number) => {
    e.stopPropagation();
    await eliminarNotificacion(id);
  };

  const handleNotificacionClick = async (notificacion: Notificacion) => {
    if (!notificacion.leida) {
      await marcarComoLeida(notificacion.id);
    }

    if (notificacion.datos_adicionales?.tarea_id) {
      window.location.href = `/admin/tareas/${notificacion.datos_adicionales.tarea_id}`;
    }
  };

  return (
    <DropdownMenu open={open} onOpenChange={setOpen}>
      <DropdownMenuTrigger asChild>
        <Button variant="ghost" size="icon" className="relative">
          <Bell className="h-5 w-5" />
          {noLeidas > 0 && (
            <span className="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs text-white">
              {noLeidas > 9 ? '9+' : noLeidas}
            </span>
          )}
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent align="end" className="w-[380px] p-0">
        <div className="flex items-center justify-between border-b px-4 py-3">
          <DropdownMenuLabel className="p-0 text-base font-semibold">
            Notificaciones
            {noLeidas > 0 && (
              <span className="ml-2 text-sm font-normal text-muted-foreground">
                ({noLeidas} sin leer)
              </span>
            )}
          </DropdownMenuLabel>
          {noLeidas > 0 && (
            <Button
              variant="ghost"
              size="sm"
              className="h-auto p-1 text-xs"
              onClick={() => marcarTodasComoLeidas()}
            >
              <CheckCheck className="mr-1 h-3 w-3" />
              Marcar todas
            </Button>
          )}
        </div>

        <ScrollArea className="max-h-[400px]">
          {loading ? (
            <div className="flex items-center justify-center p-8">
              <div className="h-6 w-6 animate-spin rounded-full border-2 border-primary border-t-transparent" />
            </div>
          ) : notificaciones.length === 0 ? (
            <div className="flex flex-col items-center justify-center p-8 text-center">
              <Bell className="mb-2 h-12 w-12 text-muted-foreground/50" />
              <p className="text-sm text-muted-foreground">No tienes notificaciones</p>
            </div>
          ) : (
            <div className="divide-y">
              {notificaciones.map((notificacion) => (
                <div
                  key={notificacion.id}
                  onClick={() => handleNotificacionClick(notificacion)}
                  className={cn(
                    'group flex cursor-pointer items-start gap-3 p-4 transition-colors hover:bg-muted/50',
                    !notificacion.leida && 'bg-primary/5'
                  )}
                >
                  <div className="mt-0.5 text-2xl">{getIconoTipo(notificacion.tipo)}</div>
                  <div className="flex-1 space-y-1">
                    <div className="flex items-start justify-between gap-2">
                      <p className="text-sm font-medium leading-none">{notificacion.titulo}</p>
                      {!notificacion.leida && (
                        <div className="mt-0.5 h-2 w-2 rounded-full bg-blue-500" />
                      )}
                    </div>
                    <p className="text-sm text-muted-foreground line-clamp-2">
                      {notificacion.mensaje}
                    </p>
                    <div className="flex items-center justify-between">
                      <p className="text-xs text-muted-foreground">
                        {formatearFecha(notificacion.created_at)}
                      </p>
                      <div className="flex gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                        {!notificacion.leida && (
                          <Button
                            variant="ghost"
                            size="icon"
                            className="h-6 w-6"
                            onClick={(e) => handleMarcarComoLeida(e, notificacion)}
                          >
                            <Check className="h-3 w-3" />
                          </Button>
                        )}
                        <Button
                          variant="ghost"
                          size="icon"
                          className="h-6 w-6 text-destructive"
                          onClick={(e) => handleEliminar(e, notificacion.id)}
                        >
                          <Trash2 className="h-3 w-3" />
                        </Button>
                      </div>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          )}
        </ScrollArea>
      </DropdownMenuContent>
    </DropdownMenu>
  );
}
