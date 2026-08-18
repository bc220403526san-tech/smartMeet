import { defineConfig } from 'vite';

import laravel from 'laravel-vite-plugin';

import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    server: {
      host: '0.0.0.0',
      port: 5173,
        cors: true,
        hmr: {
            host: '172.18.144.1',
        }
    },

    plugins: [

        laravel({

            input: [

                'resources/css/meeting-room.css',

                'resources/css/app.css',

                'resources/js/app.js',

            ],

            refresh: true,

        }),

        tailwindcss(),

    ],



});

