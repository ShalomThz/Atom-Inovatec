import { useEffect, useState } from 'react';
import axios from 'axios';

export interface Notificacion {
  id: number;
  user_id: number;
  tipo: string;
  titulo: string;
  mensaje: string;
  notificable_type: string | null;
  notificable_id: number | null;
  leida: boolean;
  leida_en: string | null;
  datos_adicionales: Record<string, any> | null;
  created_at: string;
  updated_at: string;
}

export interface NotificacionesData {
  notificaciones: Notificacion[];
  no_leidas: number;
}

export function useNotifications(pollingInterval = 30000) {
  const [notificaciones, setNotificaciones] = useState<Notificacion[]>([]);
  const [noLeidas, setNoLeidas] = useState(0);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const fetchNotifications = async () => {
    try {
      const response = await axios.get<NotificacionesData>('/api/notificaciones');
      setNotificaciones(response.data.notificaciones);
      setNoLeidas(response.data.no_leidas);
      setError(null);
    } catch (err) {
      setError('Error al cargar notificaciones');
      console.error('Error fetching notifications:', err);
    } finally {
      setLoading(false);
    }
  };

  const marcarComoLeida = async (id: number) => {
    try {
      await axios.post(`/api/notificaciones/${id}/marcar-leida`);
      setNotificaciones((prev) =>
        prev.map((n) => (n.id === id ? { ...n, leida: true, leida_en: new Date().toISOString() } : n))
      );
      setNoLeidas((prev) => Math.max(0, prev - 1));
    } catch (err) {
      console.error('Error marking notification as read:', err);
    }
  };

  const marcarTodasComoLeidas = async () => {
    try {
      await axios.post('/api/notificaciones/marcar-todas-leidas');
      setNotificaciones((prev) =>
        prev.map((n) => ({ ...n, leida: true, leida_en: new Date().toISOString() }))
      );
      setNoLeidas(0);
    } catch (err) {
      console.error('Error marking all notifications as read:', err);
    }
  };

  const eliminarNotificacion = async (id: number) => {
    try {
      await axios.delete(`/api/notificaciones/${id}`);
      setNotificaciones((prev) => prev.filter((n) => n.id !== id));
      const notificacion = notificaciones.find((n) => n.id === id);
      if (notificacion && !notificacion.leida) {
        setNoLeidas((prev) => Math.max(0, prev - 1));
      }
    } catch (err) {
      console.error('Error deleting notification:', err);
    }
  };

  useEffect(() => {
    fetchNotifications();

    const interval = setInterval(() => {
      fetchNotifications();
    }, pollingInterval);

    return () => clearInterval(interval);
  }, [pollingInterval]);

  return {
    notificaciones,
    noLeidas,
    loading,
    error,
    marcarComoLeida,
    marcarTodasComoLeidas,
    eliminarNotificacion,
    refetch: fetchNotifications,
  };
}
