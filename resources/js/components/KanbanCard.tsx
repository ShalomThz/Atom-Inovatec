import * as React from 'react';
import { useSortable } from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';

interface Tarea {
    id: number;
    nombre: string;
    descripcion: string;
    proyecto: string | null;
    asignado: string | null;
    prioridad: string;
    progreso: number;
    fecha_fin: string | null;
    estado: string;
}

interface KanbanCardProps {
    tarea: Tarea;
    isDragging?: boolean;
}

const KanbanCard: React.FC<KanbanCardProps> = ({ tarea, isDragging = false }) => {
    const { attributes, listeners, setNodeRef, transform, transition } = useSortable({
        id: tarea.id,
    });

    const style = {
        transform: CSS.Transform.toString(transform),
        transition,
    };

    const getPrioridadColor = (prioridad: string) => {
        switch (prioridad) {
            case 'alta':
                return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
            case 'media':
                return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
            case 'baja':
                return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
            default:
                return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
        }
    };

    const getPrioridadLabel = (prioridad: string) => {
        switch (prioridad) {
            case 'alta':
                return 'Alta';
            case 'media':
                return 'Media';
            case 'baja':
                return 'Baja';
            default:
                return prioridad;
        }
    };

    const handleCardClick = (e: React.MouseEvent) => {
        e.stopPropagation();
        window.location.href = `/admin/tareas?tableAction=view&tableActionRecord=${tarea.id}`;
    };

    return (
        <div
            ref={setNodeRef}
            style={style}
            {...attributes}
            {...listeners}
            onClick={handleCardClick}
            className={`kanban-card ${isDragging ? 'kanban-card-dragging' : ''}`}
        >
            <div className="flex items-start justify-between mb-3">
                <h4 className="kanban-card-title flex-1">{tarea.nombre}</h4>
                <span className={`kanban-card-badge ${getPrioridadColor(tarea.prioridad)} ml-2 flex-shrink-0`}>
                    {getPrioridadLabel(tarea.prioridad)}
                </span>
            </div>

            {tarea.descripcion && (
                <p className="kanban-card-description line-clamp-2">{tarea.descripcion}</p>
            )}

            {tarea.proyecto && (
                <div className="flex items-center gap-1.5 mb-3 px-2.5 py-1.5 bg-gray-50 dark:bg-gray-800 rounded-md">
                    <svg
                        className="w-3.5 h-3.5 text-gray-500 dark:text-gray-400 flex-shrink-0"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth={2}
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"
                        />
                    </svg>
                    <span className="text-xs font-medium text-gray-700 dark:text-gray-300 truncate">
                        {tarea.proyecto}
                    </span>
                </div>
            )}

            <div className="mb-3 bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                <div className="flex justify-between items-center mb-2">
                    <span className="text-xs font-medium text-gray-600 dark:text-gray-400">
                        Progreso
                    </span>
                    <span className="text-xs font-bold text-gray-900 dark:text-gray-100">
                        {tarea.progreso}%
                    </span>
                </div>
                <div className="kanban-progress-bar h-2">
                    <div
                        className="kanban-progress-fill h-2 transition-all duration-300"
                        style={{
                            width: `${tarea.progreso}%`,
                            backgroundColor: tarea.progreso === 100 ? 'rgb(34, 197, 94)' :
                                           tarea.progreso >= 50 ? 'rgb(59, 130, 246)' :
                                           'rgb(251, 191, 36)'
                        }}
                    />
                </div>
            </div>

            <div className="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-700">
                {tarea.asignado ? (
                    <div className="flex items-center gap-2">
                        <div className="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm">
                            <span className="text-xs font-bold text-white">
                                {tarea.asignado.charAt(0).toUpperCase()}
                            </span>
                        </div>
                        <span className="text-xs font-medium text-gray-700 dark:text-gray-300 truncate max-w-[120px]">
                            {tarea.asignado}
                        </span>
                    </div>
                ) : (
                    <div className="flex items-center gap-2">
                        <div className="w-7 h-7 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                            <svg className="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <span className="text-xs text-gray-400 dark:text-gray-500">Sin asignar</span>
                    </div>
                )}

                {tarea.fecha_fin && (
                    <div className="flex items-center gap-1.5 px-2 py-1 bg-blue-50 dark:bg-blue-900/20 rounded-md">
                        <svg className="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span className="text-xs font-medium text-blue-700 dark:text-blue-300">
                            {tarea.fecha_fin}
                        </span>
                    </div>
                )}
            </div>
        </div>
    );
};

export default KanbanCard;
