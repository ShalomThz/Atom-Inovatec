import * as React from 'react';
// Estilos específicos del calendario
import '../../css/calendar.css';
import { useState, useMemo, useEffect } from 'react';
import { Button } from './ui/button';
import { Folder, User, TrendingUp, Calendar, Clock, CheckCircle, XCircle, Hourglass } from 'lucide-react';

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

/**
 * Convierte una fecha string (YYYY-MM-DD) a Date usando timezone local
 * Evita el problema de timezone al interpretar fechas como UTC
 */
const parseLocalDate = (dateString: string | null): Date | null => {
    if (!dateString) return null;

    // Validar formato de fecha
    const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
    if (!dateRegex.test(dateString)) {
        console.warn(`Formato de fecha inválido: "${dateString}". Se esperaba YYYY-MM-DD.`);
        return null;
    }

    // Parsear como fecha local (no UTC)
    const [year, month, day] = dateString.split('-').map(Number);
    return new Date(year, month - 1, day);
};

/**
 * Convierte una Date a string YYYY-MM-DD en timezone local
 */
const formatLocalDate = (date: Date): string => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

const TaskHoverCard = ({ task, position }: { task: Tarea; position: { x: number; y: number } }) => {
    const estadoInfo = getEstadoInfo(task.estado);

    return (
        <div
            className="task-hover-card"
            style={{
                position: 'fixed',
                left: `${position.x}px`,
                top: `${position.y}px`,
                zIndex: 9999,
            }}
        >
            <div className="task-hover-header">
                <h3 className="task-hover-title">{task.nombre}</h3>
                <span className={`task-hover-estado ${estadoInfo.className}`}>
                    {estadoInfo.icon} {estadoInfo.text}
                </span>
            </div>

            {task.descripcion && (
                <p className="task-hover-descripcion">{task.descripcion}</p>
            )}

            <div className="task-hover-info">
                <div className="task-hover-info-item">
                    <Folder className="task-hover-icon" />
                    <span>{task.proyecto || 'N/A'}</span>
                </div>
                <div className="task-hover-info-item">
                    <User className="task-hover-icon" />
                    <span>{task.asignado || 'Sin asignar'}</span>
                </div>
                <div className="task-hover-info-item">
                    <TrendingUp className="task-hover-icon" />
                    <span>{task.progreso}% completado</span>
                </div>
                <div className="task-hover-info-item">
                    <span className={`task-hover-priority ${getPriorityColor(task.prioridad, 'bg')} ${getPriorityColor(task.prioridad, 'text')}`}>
                        {String(task.prioridad).charAt(0).toUpperCase()}
                    </span>
                    <span>Prioridad {task.prioridad}</span>
                </div>
            </div>

            {(task.fecha_inicio || task.fecha_fin) && (
                <div className="task-hover-dates">
                    {task.fecha_inicio && (
                        <div className="task-hover-date-item">
                            <Calendar className="task-hover-icon" />
                            <strong>Inicio:</strong> {parseLocalDate(task.fecha_inicio)?.toLocaleDateString('es-ES')}
                        </div>
                    )}
                    {task.fecha_fin && (
                        <div className="task-hover-date-item">
                            <Calendar className="task-hover-icon" />
                            <strong>Fin:</strong> {parseLocalDate(task.fecha_fin)?.toLocaleDateString('es-ES')}
                        </div>
                    )}
                </div>
            )}
        </div>
    );
};

const getPriorityColor = (prioridad: string, type: 'bg' | 'text' | 'border') => {
    const p = String(prioridad).toLowerCase();
    const colors = {
        alta: { bg: 'bg-red-100 dark:bg-red-900/30', text: 'text-red-700 dark:text-red-300', border: 'border-red-500' },
        media: { bg: 'bg-yellow-100 dark:bg-yellow-900/30', text: 'text-yellow-700 dark:text-yellow-300', border: 'border-yellow-500' },
        baja: { bg: 'bg-green-100 dark:bg-green-900/30', text: 'text-green-700 dark:text-green-300', border: 'border-green-500' },
        default: { bg: 'bg-gray-100 dark:bg-gray-700', text: 'text-gray-700 dark:text-gray-300', border: 'border-gray-500' }
    };
    return (colors[p] || colors.default)[type];
};

const getEstadoInfo = (estado: string): { icon: React.ReactNode, text: string, className: string } => {
    const e = String(estado).toLowerCase();
    const info = {
        completada: { icon: <CheckCircle className="w-4 h-4" />, text: 'Completado', className: 'text-green-600 dark:text-green-400' },
        en_progreso: { icon: <Hourglass className="w-4 h-4" />, text: 'En Progreso', className: 'text-amber-600 dark:text-amber-400' },
        cancelada: { icon: <XCircle className="w-4 h-4" />, text: 'Cancelado', className: 'text-red-600 dark:text-red-400' },
        pendiente: { icon: <Clock className="w-4 h-4" />, text: 'Pendiente', className: 'text-gray-600 dark:text-gray-400' },
    };

    // Log warning if estado is not recognized
    if (!info[e]) {
        console.warn(`Estado no reconocido: "${estado}". Usando "pendiente" por defecto.`);
    }

    return info[e] || info.pendiente;
};

const CalendarView: React.FC<CalendarViewProps> = ({ tareas }) => {
    const [currentDate, setCurrentDate] = useState(new Date());
    const [selectedDate, setSelectedDate] = useState<Date | null>(null);
    const [hoveredTask, setHoveredTask] = useState<{ task: Tarea; position: { x: number; y: number } } | null>(null);

    const handleDayClick = (date: Date) => {
        setSelectedDate(date);
    };

    const handleTaskMouseEnter = (tarea: Tarea, e: React.MouseEvent) => {
        const rect = (e.target as HTMLElement).getBoundingClientRect();
        setHoveredTask({
            task: tarea,
            position: {
                x: rect.right + 10, // 10px a la derecha del badge
                y: rect.top,
            }
        });
    };

    const handleTaskMouseLeave = () => {
        setHoveredTask(null);
    };

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
        const dateStr = formatLocalDate(date);
        return tareas.filter(tarea => {
            // Validar que las fechas sean válidas antes de comparar
            const fechaInicioDate = parseLocalDate(tarea.fecha_inicio);
            const fechaFinDate = parseLocalDate(tarea.fecha_fin);

            const fechaInicio = fechaInicioDate ? formatLocalDate(fechaInicioDate) : null;
            const fechaFin = fechaFinDate ? formatLocalDate(fechaFinDate) : null;

            return fechaInicio === dateStr || fechaFin === dateStr;
        });
    };

    const getTareaTypeForDate = (date: Date, tarea: Tarea): 'inicio' | 'fin' | 'ambas' => {
        const dateStr = formatLocalDate(date);

        const fechaInicioDate = parseLocalDate(tarea.fecha_inicio);
        const fechaFinDate = parseLocalDate(tarea.fecha_fin);

        const fechaInicio = fechaInicioDate ? formatLocalDate(fechaInicioDate) : null;
        const fechaFin = fechaFinDate ? formatLocalDate(fechaFinDate) : null;

        if (fechaInicio === dateStr && fechaFin === dateStr) return 'ambas';
        if (fechaInicio === dateStr) return 'inicio';
        return 'fin';
    };

    const calendarDays = useMemo(() => {
        const days: (Date | null)[] = [];
        for (let i = 0; i < startingDayOfWeek; i++) days.push(null);
        for (let day = 1; day <= daysInMonth; day++) days.push(new Date(year, month, day));
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

    return (
        <div className="calendar-view-container">
            <div className="calendar-container">
                <div className="calendar-header">
                    <div className="flex items-center gap-4">
                        <h2 className="text-2xl font-bold text-gray-900 dark:text-white">{MESES[month]} {year}</h2>
                        <Button onClick={goToToday} size="sm">Hoy</Button>
                    </div>
                    <div className="flex gap-2">
                        <Button onClick={goToPreviousMonth} variant="outline" size="icon" aria-label="Mes anterior"><span className="text-xl">‹</span></Button>
                        <Button onClick={goToNextMonth} variant="outline" size="icon" aria-label="Mes siguiente"><span className="text-xl">›</span></Button>
                    </div>
                </div>
                <div className="calendar-grid">
                    <div className="calendar-weekdays">{DIAS_SEMANA.map(dia => <div key={dia} className="calendar-weekday">{dia}</div>)}</div>
                    <div className="calendar-days">
                        {calendarDays.map((date, index) => {
                            if (!date) return <div key={`empty-${index}`} className="calendar-day-empty" />;
                            const tareasDelDia = getTareasForDate(date);
                            return (
                                <div
                                    key={date.toISOString()}
                                    className={`calendar-day ${isToday(date) ? 'calendar-day-today' : ''} ${isSelected(date) ? 'calendar-day-selected' : ''}`}
                                    onClick={() => handleDayClick(date)}
                                >
                                    <div className="calendar-day-number">{date.getDate()}</div>
                                    <div className="calendar-day-tasks">
                                        {tareasDelDia.slice(0, 3).map(tarea => {
                                            const tipo = getTareaTypeForDate(date, tarea);
                                            const tipoColorClass = tipo === 'inicio' ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200';
                                            const tipoIcon = tipo === 'inicio' ? '▶' : tipo === 'fin' ? '■' : '●';
                                            return (
                                                <div
                                                    key={`${tarea.id}-${tipo}`}
                                                    className={`calendar-task-badge ${tipoColorClass}`}
                                                    title={`${tarea.nombre} - ${tarea.proyecto || 'Sin proyecto'} (${tipo})`}
                                                    onMouseEnter={(e) => handleTaskMouseEnter(tarea, e)}
                                                    onMouseLeave={handleTaskMouseLeave}
                                                >
                                                    <span className="calendar-task-type">{tipoIcon}</span>
                                                    <span className="calendar-task-name">{tarea.nombre}</span>
                                                    {tarea.proyecto && (
                                                        <span className="calendar-task-project">· {tarea.proyecto}</span>
                                                    )}
                                                </div>
                                            );
                                        })}
                                        {tareasDelDia.length > 3 && <div className="calendar-task-more">+{tareasDelDia.length - 3} más</div>}
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </div>

            {/* Hover Card */}
            {hoveredTask && <TaskHoverCard task={hoveredTask.task} position={hoveredTask.position} />}
        </div>
    );
};

export default CalendarView;
