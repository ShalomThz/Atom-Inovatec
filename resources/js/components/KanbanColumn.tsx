import * as React from 'react';
import { useDroppable } from '@dnd-kit/core';
import { SortableContext, verticalListSortingStrategy } from '@dnd-kit/sortable';
import KanbanCard from './KanbanCard';

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

interface KanbanColumnProps {
    id: string;
    title: string;
    color: string;
    tasks: Tarea[];
}

const KanbanColumn: React.FC<KanbanColumnProps> = ({ id, title, color, tasks }) => {
    const { setNodeRef, isOver } = useDroppable({
        id,
    });

    const taskIds = tasks.map((task) => task.id);

    return (
        <div
            ref={setNodeRef}
            className={`kanban-column ${isOver ? 'kanban-column-drag-over' : ''}`}
        >
            <div className="kanban-column-header">
                <h3 className="kanban-column-title">{title}</h3>
                <span
                    className="kanban-column-badge"
                    style={{ backgroundColor: color }}
                >
                    {tasks.length}
                </span>
            </div>

            <SortableContext items={taskIds} strategy={verticalListSortingStrategy}>
                <div className="kanban-cards-container">
                    {tasks.length === 0 ? (
                        <div className="flex items-center justify-center py-8 text-gray-400 text-sm">
                            No hay tareas
                        </div>
                    ) : (
                        tasks.map((tarea) => <KanbanCard key={tarea.id} tarea={tarea} />)
                    )}
                </div>
            </SortableContext>
        </div>
    );
};

export default KanbanColumn;
