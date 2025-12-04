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

    const tareasData = JSON.parse(container.dataset.tareas || '[]');

    const root = createRoot(container);
    root.render(
        createElement(CalendarView, {
            tareas: tareasData
        })
    );
});
