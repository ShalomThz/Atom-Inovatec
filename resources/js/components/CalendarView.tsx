import * as React from 'react';
import { useState, useMemo } from 'react';

interface Tarea {
    id: number;
    nombre: string;
    descripcion: string;
    proyecto: string | null;
    asignado: string | null;
    prioridad: string;
    progreso: number;
    fecha_inicio: string | null;
    fecha_fin: string | null;
    estado: string;
}

interface CalendarViewProps {
    tareas: Tarea[];
}

const DIAS_SEMANA = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
const MESES = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
];

const CalendarView: React.FC<CalendarViewProps> = ({ tareas }) => {
    const [currentDate, setCurrentDate] = useState(new Date());
    const [selectedDate, setSelectedDate] = useState<Date | null>(null);

    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();

    const firstDayOfMonth = new Date(year, month, 1);
    const lastDayOfMonth = new Date(year, month + 1, 0);
    const daysInMonth = lastDayOfMonth.getDate();
    const startingDayOfWeek = firstDayOfMonth.getDay();

    const goToPreviousMonth = () => {
        setCurrentDate(new Date(year, month - 1, 1));
        setSelectedDate(null);
    };

    const goToNextMonth = () => {
        setCurrentDate(new Date(year, month + 1, 1));
        setSelectedDate(null);
    };

    const goToToday = () => {
        setCurrentDate(new Date());
        setSelectedDate(new Date());
    };

    const getTareasForDate = (date: Date): Tarea[] => {
        const dateStr = date.toISOString().split('T')[0];
        return tareas.filter(tarea => {
            const fechaInicio = tarea.fecha_inicio ? new Date(tarea.fecha_inicio).toISOString().split('T')[0] : null;
            const fechaFin = tarea.fecha_fin ? new Date(tarea.fecha_fin).toISOString().split('T')[0] : null;

            return fechaInicio === dateStr || fechaFin === dateStr;
        });
    };

    const getTareaTypeForDate = (date: Date, tarea: Tarea): 'inicio' | 'fin' | 'ambas' => {
        const dateStr = date.toISOString().split('T')[0];
        const fechaInicio = tarea.fecha_inicio ? new Date(tarea.fecha_inicio).toISOString().split('T')[0] : null;
        const fechaFin = tarea.fecha_fin ? new Date(tarea.fecha_fin).toISOString().split('T')[0] : null;

        if (fechaInicio === dateStr && fechaFin === dateStr) return 'ambas';
        if (fechaInicio === dateStr) return 'inicio';
        return 'fin';
    };

    const calendarDays = useMemo(() => {
        const days: (Date | null)[] = [];

        for (let i = 0; i < startingDayOfWeek; i++) {
            days.push(null);
        }

        for (let day = 1; day <= daysInMonth; day++) {
            days.push(new Date(year, month, day));
        }

        return days;
    }, [year, month, daysInMonth, startingDayOfWeek]);

    const isToday = (date: Date | null): boolean => {
        if (!date) return false;
        const today = new Date();
        return date.toDateString() === today.toDateString();
    };

    const isSelected = (date: Date | null): boolean => {
        if (!date || !selectedDate) return false;
        return date.toDateString() === selectedDate.toDateString();
    };

    const getPriorityColor = (prioridad: string): string => {
        if (!prioridad) return 'bg-gray-500 dark:bg-gray-600';

        const prioridadStr = String(prioridad).toLowerCase();
        switch (prioridadStr) {
            case 'alta':
                return 'bg-red-500 dark:bg-red-600';
            case 'media':
                return 'bg-yellow-500 dark:bg-yellow-600';
            case 'baja':
                return 'bg-green-500 dark:bg-green-600';
            default:
                return 'bg-gray-500 dark:bg-gray-600';
        }
    };

    const getEstadoColor = (estado: string): string => {
        if (!estado) return 'bg-gray-100 dark:bg-gray-800/50 border-gray-500';

        const estadoStr = String(estado).toLowerCase();
        switch (estadoStr) {
            case 'completada':
                return 'bg-green-100 dark:bg-green-900/30 border-green-500';
            case 'en_progreso':
                return 'bg-amber-100 dark:bg-amber-900/30 border-amber-500';
            case 'pendiente':
                return 'bg-gray-100 dark:bg-gray-800/50 border-gray-500';
            default:
                return 'bg-gray-100 dark:bg-gray-800/50 border-gray-500';
        }
    };

    const selectedDateTareas = selectedDate ? getTareasForDate(selectedDate) : [];

    return (
        <div className="calendar-container">
            <div className="calendar-header">
                <div className="flex items-center gap-4">
                    <h2 className="text-2xl font-bold text-gray-900 dark:text-white">
                        {MESES[month]} {year}
                    </h2>
                    <button
                        onClick={goToToday}
                        className="px-3 py-1.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors"
                    >
                        Hoy
                    </button>
                </div>
                <div className="flex gap-2">
                    <button
                        onClick={goToPreviousMonth}
                        className="calendar-nav-btn"
                        aria-label="Mes anterior"
                    >
                        ‹
                    </button>
                    <button
                        onClick={goToNextMonth}
                        className="calendar-nav-btn"
                        aria-label="Mes siguiente"
                    >
                        ›
                    </button>
                </div>
            </div>

            <div className="calendar-grid">
                <div className="calendar-weekdays">
                    {DIAS_SEMANA.map(dia => (
                        <div key={dia} className="calendar-weekday">
                            {dia}
                        </div>
                    ))}
                </div>

                <div className="calendar-days">
                    {calendarDays.map((date, index) => {
                        if (!date) {
                            return <div key={`empty-${index}`} className="calendar-day-empty" />;
                        }

                        const tareasDelDia = getTareasForDate(date);
                        const isCurrentDay = isToday(date);
                        const isSelectedDay = isSelected(date);

                        return (
                            <div
                                key={date.toISOString()}
                                className={`calendar-day ${isCurrentDay ? 'calendar-day-today' : ''} ${isSelectedDay ? 'calendar-day-selected' : ''}`}
                                onClick={() => setSelectedDate(date)}
                            >
                                <div className="calendar-day-number">
                                    {date.getDate()}
                                </div>
                                <div className="calendar-day-tasks">
                                    {tareasDelDia.slice(0, 3).map(tarea => {
                                        const tipo = getTareaTypeForDate(date, tarea);
                                        return (
                                            <div
                                                key={`${tarea.id}-${tipo}`}
                                                className={`calendar-task-badge ${getPriorityColor(tarea.prioridad)}`}
                                                title={`${tarea.nombre} - ${tipo === 'inicio' ? 'Inicio' : tipo === 'fin' ? 'Vencimiento' : 'Inicio y Vencimiento'}`}
                                            >
                                                <span className="calendar-task-type">
                                                    {tipo === 'inicio' ? '▶' : tipo === 'fin' ? '⏹' : '⏺'}
                                                </span>
                                                <span className="calendar-task-name">
                                                    {tarea.nombre}
                                                </span>
                                            </div>
                                        );
                                    })}
                                    {tareasDelDia.length > 3 && (
                                        <div className="calendar-task-more">
                                            +{tareasDelDia.length - 3} más
                                        </div>
                                    )}
                                </div>
                            </div>
                        );
                    })}
                </div>
            </div>

            {selectedDate && (
                <div className="calendar-details">
                    <h3 className="text-lg font-semibold mb-4 text-gray-900 dark:text-white">
                        Tareas del {selectedDate.getDate()} de {MESES[selectedDate.getMonth()]}
                    </h3>
                    {selectedDateTareas.length === 0 ? (
                        <p className="text-gray-500 dark:text-gray-400">
                            No hay tareas para este día.
                        </p>
                    ) : (
                        <div className="space-y-3">
                            {selectedDateTareas.map(tarea => {
                                const tipo = getTareaTypeForDate(selectedDate, tarea);
                                return (
                                    <div
                                        key={tarea.id}
                                        className={`calendar-task-card ${getEstadoColor(tarea.estado)}`}
                                    >
                                        <div className="flex items-start justify-between">
                                            <div className="flex-1">
                                                <div className="flex items-center gap-2 mb-2">
                                                    <span className={`calendar-priority-badge ${getPriorityColor(tarea.prioridad)}`}>
                                                        {tarea.prioridad}
                                                    </span>
                                                    <span className="text-xs text-gray-600 dark:text-gray-400">
                                                        {tipo === 'inicio' ? '▶ Inicio' : tipo === 'fin' ? '⏹ Vencimiento' : '⏺ Inicio y Vencimiento'}
                                                    </span>
                                                </div>
                                                <h4 className="font-semibold text-gray-900 dark:text-white mb-1">
                                                    {tarea.nombre}
                                                </h4>
                                                {tarea.descripcion && (
                                                    <p className="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                                        {tarea.descripcion}
                                                    </p>
                                                )}
                                                <div className="flex flex-wrap gap-3 text-xs text-gray-600 dark:text-gray-400">
                                                    {tarea.proyecto && (
                                                        <span>📁 {tarea.proyecto}</span>
                                                    )}
                                                    {tarea.asignado && (
                                                        <span>👤 {tarea.asignado}</span>
                                                    )}
                                                    <span>📊 {tarea.progreso}%</span>
                                                </div>
                                                {tarea.fecha_inicio && tarea.fecha_fin && (
                                                    <div className="mt-2 text-xs text-gray-500 dark:text-gray-500">
                                                        {new Date(tarea.fecha_inicio).toLocaleDateString('es-ES')} → {new Date(tarea.fecha_fin).toLocaleDateString('es-ES')}
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    )}
                </div>
            )}
        </div>
    );
};

export default CalendarView;
