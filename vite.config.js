import { defineConfig, splitVendorChunkPlugin } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    // base: "https://1977-27-72-29-216.ngrok-free.app",
    plugins: [
        laravel({
            input: [
                'resources/css/app.scss',
                'resources/js/index.js',
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
        }),
        splitVendorChunkPlugin()
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
    server: {
        host: false
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vue: ['vue']
                }
            }
        }
    }
});