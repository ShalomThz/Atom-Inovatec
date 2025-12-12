import * as React from 'react';
import { createElement } from 'react';
import { createRoot } from 'react-dom/client';
import CalendarView from './components/CalendarView';

document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('calendar-view-root');

    if (!container) {
        console.error('No se encontró el contenedor del Calendar View');
        return;
    }

    let tareasData = [];

    try {
        tareasData = JSON.parse(container.dataset.tareas || '[]');
    } catch (error) {
        console.error('Error al parsear datos de tareas:', error);
        console.error('Datos recibidos:', container.dataset.tareas);
        // Mostrar mensaje de error al usuario
        container.innerHTML = `
            <div style="padding: 2rem; text-align: center; color: #dc2626;">
                <h3 style="font-size: 1.25rem; font-weight: bold; margin-bottom: 0.5rem;">Error al cargar el calendario</h3>
                <p style="color: #6b7280;">No se pudieron cargar las tareas. Por favor, recarga la página.</p>
            </div>
        `;
        return;
    }

    const root = createRoot(container);
    root.render(
        createElement(CalendarView, {
            tareas: tareasData
        })
    );
});
