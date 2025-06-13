import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import fg from 'fast-glob';

const jsFiles = fg.sync('resources/js/**/*.js');

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                ...jsFiles,
                'resources/js/activity-log-pdf.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    optimizeDeps: {
        include: ['html2pdf.js']
    }
});
