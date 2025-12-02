import * as React from 'react';
import { useState, useEffect } from 'react';
import {
    DndContext,
    DragEndEvent,
    DragOverlay,
    DragStartEvent,
    PointerSensor,
    useSensor,
    useSensors,
    closestCorners,
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
    completada?: Tarea[];
}

interface KanbanBoardProps {
    initialTareas: TareasAgrupadas;
    onUpdateEstado: (tareaId: number, nuevoEstado: string) => void;
}

const KanbanBoard: React.FC<KanbanBoardProps> = ({ initialTareas, onUpdateEstado }) => {
    const [tareas, setTareas] = useState<TareasAgrupadas>(initialTareas);
    const [activeTarea, setActiveTarea] = useState<Tarea | null>(null);
    const [isDarkMode, setIsDarkMode] = useState<boolean>(false);

    // Detectar el tema actual
    useEffect(() => {
        const checkDarkMode = () => {
            // Priorizar la clase 'dark' de Filament sobre la preferencia del sistema
            const hasDarkClass = document.documentElement.classList.contains('dark');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            // Solo usar prefersDark si Filament no tiene una clase específica de tema
            // Filament maneja su propio tema, así que priorizamos hasDarkClass
            const isDark = hasDarkClass;

            console.log('🎨 Kanban Theme Detection:', { hasDarkClass, prefersDark, isDark, source: hasDarkClass ? 'Filament' : 'System' });
            setIsDarkMode(isDark);
        };

        // Verificar al cargar
        checkDarkMode();

        // Escuchar cambios en el tema del sistema
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        const handleChange = () => checkDarkMode();
        mediaQuery.addEventListener('change', handleChange);

        // Observar cambios en la clase 'dark' del documento
        const observer = new MutationObserver(checkDarkMode);
        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class'],
        });

        return () => {
            mediaQuery.removeEventListener('change', handleChange);
            observer.disconnect();
        };
    }, []);

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

    const handleDragEnd = async (event: DragEndEvent) => {
        const { active, over } = event;

        if (!over) {
            setActiveTarea(null);
            return;
        }

        const tareaId = active.id as number;
        let nuevoEstado: string;

        // Bug Fix #1 & #5: Verificar si over.id es una columna (string) o una tarjeta (number)
        if (typeof over.id === 'string') {
            // Drop directo sobre la columna
            nuevoEstado = over.id;
        } else {
            // Drop sobre otra tarjeta - buscar la columna que contiene esa tarjeta
            const overTareaId = over.id as number;
            let foundEstado: string | null = null;

            Object.entries(tareas).forEach(([estado, tareasArray]) => {
                if (tareasArray?.find((t) => t.id === overTareaId)) {
                    foundEstado = estado;
                }
            });

            if (!foundEstado) {
                setActiveTarea(null);
                return;
            }

            nuevoEstado = foundEstado;
        }

        // Encontrar el estado actual de la tarea
        let estadoActual: string | null = null;
        Object.entries(tareas).forEach(([estado, tareasArray]) => {
            if (tareasArray?.find((t) => t.id === tareaId)) {
                estadoActual = estado;
            }
        });

        if (estadoActual && estadoActual !== nuevoEstado) {
            // Guardar el estado anterior para rollback
            const previousTareas = { ...tareas };

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

            // Bug Fix #3: Llamar al callback con manejo de errores y rollback
            try {
                await onUpdateEstado(tareaId, nuevoEstado);
            } catch (error) {
                console.error('Error al actualizar tarea, revirtiendo cambios:', error);
                // Rollback al estado anterior
                setTareas(previousTareas);
            }
        }

        setActiveTarea(null);
    };

    const columns = [
        {
            id: 'pendiente',
            title: 'Tareas Pendientes',
            color: isDarkMode ? 'rgb(107, 114, 128)' : 'rgb(156, 163, 175)', // gray-500 dark : gray-400 light
            tasks: tareas.pendiente || [],
        },
        {
            id: 'en_progreso',
            title: 'Tareas en Progreso',
            color: isDarkMode ? 'rgb(245, 158, 11)' : 'rgb(251, 191, 36)', // amber-500 dark : amber-400 light
            tasks: tareas.en_progreso || [],
        },
        {
            id: 'completada',
            title: 'Tareas Finalizadas',
            color: isDarkMode ? 'rgb(34, 197, 94)' : 'rgb(74, 222, 128)', // green-500 dark : green-400 light
            tasks: tareas.completada || [],
        },
    ];

    return (
        <DndContext
            sensors={sensors}
            collisionDetection={closestCorners}
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
