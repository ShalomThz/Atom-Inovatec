import * as React from 'react';
// Estilos específicos del calendario
import '../../css/calendar.css';
import { useState, useMemo, useEffect } from 'react';
import { Button } from './ui/button';
import { Folder, User, TrendingUp, Calendar, Clock, CheckCircle, XCircle, Hourglass, X } from 'lucide-react';

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

const DetailPanelContent = ({ task }: { task: Tarea }) => {
    const estadoInfo = getEstadoInfo(task.estado);

    return (
        <div className="p-6 h-full overflow-y-auto">
            {task.descripcion && <p className="text-sm text-gray-600 dark:text-gray-400 mb-4">{task.descripcion}</p>}

            <div className="space-y-3 text-sm">
                <div className="flex items-center gap-3">
                    <Folder className="w-5 h-5 text-gray-400 flex-shrink-0" />
                    <span className="text-gray-700 dark:text-gray-300">{task.proyecto || 'N/A'}</span>
                </div>
                <div className="flex items-center gap-3">
                    <User className="w-5 h-5 text-gray-400 flex-shrink-0" />
                    <span className="text-gray-700 dark:text-gray-300">{task.asignado || 'Sin asignar'}</span>
                </div>
                <div className="flex items-center gap-3">
                    <TrendingUp className="w-5 h-5 text-gray-400 flex-shrink-0" />
                    <span className="text-gray-700 dark:text-gray-300">{task.progreso}% completado</span>
                </div>
                <div className="flex items-center gap-3">
                    <div className={`w-5 h-5 flex-shrink-0 flex items-center justify-center`}>
                        <span className={`px-2 py-1 text-xs font-semibold rounded-full ${getPriorityColor(task.prioridad, 'bg')} ${getPriorityColor(task.prioridad, 'text')}`}>
                            {String(task.prioridad).charAt(0).toUpperCase()}
                        </span>
                    </div>
                    <span className="text-gray-700 dark:text-gray-300">Prioridad {task.prioridad}</span>
                </div>
                <div className={`flex items-center gap-3 font-medium ${estadoInfo.className}`}>
                    <div className="w-5 h-5 flex-shrink-0">{estadoInfo.icon}</div>
                    <span>{estadoInfo.text}</span>
                </div>
            </div>

            {(task.fecha_inicio || task.fecha_fin) && (
                <div className="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 text-sm">
                    {task.fecha_inicio && <p className="flex items-center gap-2 text-gray-600 dark:text-gray-400"><Calendar className="w-4 h-4"/> <strong>Inicio:</strong> {new Date(task.fecha_inicio).toLocaleDateString('es-ES')}</p>}
                    {task.fecha_fin && <p className="flex items-center gap-2 text-gray-600 dark:text-gray-400 mt-1"><Calendar className="w-4 h-4"/> <strong>Fin:</strong> {new Date(task.fecha_fin).toLocaleDateString('es-ES')}</p>}
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
        completado: { icon: <CheckCircle className="w-4 h-4" />, text: 'Completado', className: 'text-green-600 dark:text-green-400' },
        en_progreso: { icon: <Hourglass className="w-4 h-4" />, text: 'En Progreso', className: 'text-amber-600 dark:text-amber-400' },
        cancelado: { icon: <XCircle className="w-4 h-4" />, text: 'Cancelado', className: 'text-red-600 dark:text-red-400' },
        pendiente: { icon: <Clock className="w-4 h-4" />, text: 'Pendiente', className: 'text-gray-600 dark:text-gray-400' },
    };
    return info[e] || info.pendiente;
};

const CalendarView: React.FC<CalendarViewProps> = ({ tareas }) => {
    const [currentDate, setCurrentDate] = useState(new Date());
    const [selectedDate, setSelectedDate] = useState<Date | null>(null);
    const [selectedTask, setSelectedTask] = useState<Tarea | null>(null);

    const isPanelOpen = !!selectedTask;

    const handleDayClick = (date: Date) => {
        setSelectedDate(date);
        setSelectedTask(null); // Close panel if a day is clicked
    };

    const handleTaskClick = (tarea: Tarea, e: React.MouseEvent) => {
        e.stopPropagation();
        setSelectedTask(tarea);
        setSelectedDate(null);
    };
    
    const closePanel = () => {
        setSelectedTask(null);
    }

    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();

    const firstDayOfMonth = new Date(year, month, 1);
    const lastDayOfMonth = new Date(year, month + 1, 0);
    const daysInMonth = lastDayOfMonth.getDate();
    const startingDayOfWeek = firstDayOfMonth.getDay();

    const goToPreviousMonth = () => {
        setCurrentDate(new Date(year, month - 1, 1));
        setSelectedDate(null);
        setSelectedTask(null);
    };

    const goToNextMonth = () => {
        setCurrentDate(new Date(year, month + 1, 1));
        setSelectedDate(null);
        setSelectedTask(null);
    };

    const goToToday = () => {
        setCurrentDate(new Date());
        setSelectedDate(new Date());
        setSelectedTask(null);
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
                                    className={`calendar-day ${isToday(date) ? 'calendar-day-today' : ''} ${isSelected(date) && !selectedTask ? 'calendar-day-selected' : ''}`}
                                    onClick={() => handleDayClick(date)}
                                >
                                    <div className="calendar-day-number">{date.getDate()}</div>
                                    <div className="calendar-day-tasks">
                                        {tareasDelDia.slice(0, 3).map(tarea => {
                                            const tipo = getTareaTypeForDate(date, tarea);
                                            const tipoColorClass = tipo === 'inicio' ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200';
                                            return (
                                                <div
                                                    key={`${tarea.id}-${tipo}`}
                                                    className={`calendar-task-badge cursor-pointer ${tipoColorClass} ${selectedTask?.id === tarea.id ? 'ring-2 ring-blue-500' : ''}`}
                                                    title={`${tarea.nombre} - ${tipo}`}
                                                    onClick={(e) => handleTaskClick(tarea, e)}
                                                >
                                                    <span className="calendar-task-name">Tarea {tipo}</span>
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

            {/* Slide-over Panel */}
            <>
                <div className={`fixed inset-0 bg-gray-900/50 z-40 backdrop-blur-sm transition-opacity ${isPanelOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'}`}
                    onClick={closePanel}
                ></div>
                <div className={`fixed top-0 right-0 h-full bg-white dark:bg-gray-800 w-full max-w-md z-50 shadow-lg transform transition-transform duration-300 ease-in-out ${isPanelOpen ? 'translate-x-0' : 'translate-x-full'}`}>
                    {selectedTask && (
                        <>
                            <div className="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-start gap-4">
                                <h2 className="text-xl font-bold text-gray-900 dark:text-white pt-1">{selectedTask.nombre}</h2>
                                <Button variant="ghost" size="icon" onClick={closePanel} className="flex-shrink-0">
                                    <X className="w-6 h-6" />
                                </Button>
                            </div>
                            <DetailPanelContent task={selectedTask} />
                        </>
                    )}
                </div>
            </>
        </div>
    );
};

export default CalendarView;
