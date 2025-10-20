import * as React from 'react';
import { createElement } from 'react';
import { createRoot } from 'react-dom/client';
import KanbanBoard from './components/KanbanBoard';

// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('kanban-board-root');

    if (!container) {
        console.error('No se encontró el contenedor del Kanban Board');
        return;
    }

    // Obtener los datos de las tareas
    const tareasData = JSON.parse(container.dataset.tareas || '{}');

    // Función para actualizar el estado de una tarea
    const handleUpdateEstado = async (tareaId, nuevoEstado) => {
        try {
            // Usar fetch para evitar que Livewire recargue la página
            const response = await fetch('/admin/kanban-board/update-tarea', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    tarea_id: tareaId,
                    nuevo_estado: nuevoEstado
                })
            });

            if (!response.ok) {
                throw new Error('Error al actualizar la tarea');
            }

            const result = await response.json();
            console.log('Tarea actualizada exitosamente:', result);
            return result;

        } catch (error) {
            console.error('Error al actualizar tarea:', error);
            throw error;
        }
    };

    // Crear el root de React y renderizar el componente
    const root = createRoot(container);
    root.render(
        createElement(KanbanBoard, {
            initialTareas: tareasData,
            onUpdateEstado: handleUpdateEstado
        })
    );
});
