import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/kanban.css',
                'resources/css/calendar.css',
                'resources/js/app.tsx',
                'resources/js/kanban-init.tsx',
                'resources/js/calendar-init.tsx'
            ],
            ssr: 'resources/js/ssr.tsx',
            refresh: true,
        }),
        tailwindcss(),
        wayfinder({
            formVariants: true,
        }),
    ],
    server: {
        cors: true,
    },
    esbuild: {
        jsx: 'automatic',
        jsxImportSource: 'react',
    },
});
