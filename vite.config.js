import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue'
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
        tailwindcss(),
    ],

    server: {
        proxy: {
            // Proxy untuk backend API Laravel
            '/api': {
                target: 'http://localhost:8000',
                changeOrigin: true,
            },
            // Proxy untuk route export / download Excel
            '/export': {
                target: 'http://localhost:8000',
                changeOrigin: true,
            },
        },
    },
});
