import * as React from 'react';
import { useState } from 'react';
import {
    DndContext,
    DragEndEvent,
    DragOverlay,
    DragStartEvent,
    PointerSensor,
    useSensor,
    useSensors,
} from '@dnd-kit/core';
import KanbanColumn from './KanbanColumn';
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

interface TareasAgrupadas {
    pendiente?: Tarea[];
    en_progreso?: Tarea[];
    completado?: Tarea[];
}

interface KanbanBoardProps {
    initialTareas: TareasAgrupadas;
    onUpdateEstado: (tareaId: number, nuevoEstado: string) => void;
}

const KanbanBoard: React.FC<KanbanBoardProps> = ({ initialTareas, onUpdateEstado }) => {
    const [tareas, setTareas] = useState<TareasAgrupadas>(initialTareas);
    const [activeTarea, setActiveTarea] = useState<Tarea | null>(null);

    const sensors = useSensors(
        useSensor(PointerSensor, {
            activationConstraint: {
                distance: 8,
            },
        })
    );

    const handleDragStart = (event: DragStartEvent) => {
        const { active } = event;
        const tareaId = active.id as number;

        // Encontrar la tarea activa
        let tarea: Tarea | null = null;
        Object.values(tareas).forEach((tareasArray) => {
            const found = tareasArray?.find((t) => t.id === tareaId);
            if (found) {
                tarea = found;
            }
        });

        setActiveTarea(tarea);
    };

    const handleDragEnd = (event: DragEndEvent) => {
        const { active, over } = event;

        if (!over) {
            setActiveTarea(null);
            return;
        }

        const tareaId = active.id as number;
        const nuevoEstado = over.id as string;

        // Encontrar el estado actual de la tarea
        let estadoActual: string | null = null;
        Object.entries(tareas).forEach(([estado, tareasArray]) => {
            if (tareasArray?.find((t) => t.id === tareaId)) {
                estadoActual = estado;
            }
        });

        if (estadoActual && estadoActual !== nuevoEstado) {
            // Actualizar el estado local inmediatamente para una mejor UX
            setTareas((prev) => {
                const newTareas = { ...prev };

                // Encontrar y remover la tarea del estado actual
                const tarea = newTareas[estadoActual as keyof TareasAgrupadas]?.find(
                    (t) => t.id === tareaId
                );

                if (tarea) {
                    newTareas[estadoActual as keyof TareasAgrupadas] = newTareas[
                        estadoActual as keyof TareasAgrupadas
                    ]?.filter((t) => t.id !== tareaId);

                    // Actualizar el estado de la tarea
                    tarea.estado = nuevoEstado;

                    // Agregar la tarea al nuevo estado
                    if (!newTareas[nuevoEstado as keyof TareasAgrupadas]) {
                        newTareas[nuevoEstado as keyof TareasAgrupadas] = [];
                    }
                    newTareas[nuevoEstado as keyof TareasAgrupadas]?.push(tarea);
                }

                return newTareas;
            });

            // Llamar al callback para actualizar en el backend
            onUpdateEstado(tareaId, nuevoEstado);
        }

        setActiveTarea(null);
    };

    const columns = [
        {
            id: 'pendiente',
            title: 'Tareas Pendientes',
            color: 'rgb(156, 163, 175)',
            tasks: tareas.pendiente || [],
        },
        {
            id: 'en_progreso',
            title: 'Tareas en Progreso',
            color: 'rgb(251, 191, 36)',
            tasks: tareas.en_progreso || [],
        },
        {
            id: 'completada',
            title: 'Tareas Finalizadas',
            color: 'rgb(34, 197, 94)',
            tasks: tareas.completada || [],
        },
    ];

    return (
        <DndContext
            sensors={sensors}
            onDragStart={handleDragStart}
            onDragEnd={handleDragEnd}
        >
            <div className="kanban-board">
                {columns.map((column) => (
                    <KanbanColumn
                        key={column.id}
                        id={column.id}
                        title={column.title}
                        color={column.color}
                        tasks={column.tasks}
                    />
                ))}
            </div>

            <DragOverlay>
                {activeTarea ? (
                    <div className="kanban-card" style={{ opacity: 0.8 }}>
                        <KanbanCard tarea={activeTarea} isDragging={false} />
                    </div>
                ) : null}
            </DragOverlay>
        </DndContext>
    );
};

export default KanbanBoard;
