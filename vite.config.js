import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/nexus-admin.css',
                'resources/js/nexus-admin.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        chunkSizeWarningLimit: 1000,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('@fullcalendar')) {
                        return 'vendor-fullcalendar';
                    }
                    if (id.includes('chart.js')) {
                        return 'vendor-chartjs';
                    }
                    if (id.includes('frappe-gantt')) {
                        return 'vendor-gantt';
                    }
                },
            },
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
