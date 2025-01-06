import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    // base: "https://hwee-jan.pm22.corsivalab.xyz",
    base: "/build/",
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        })
    ],
    server: {
        watch: {
            ignored: ['**/node_modules/**', '**/vendor/**']
        },
        hmr: {
            overlay: false
        }
    },
});
