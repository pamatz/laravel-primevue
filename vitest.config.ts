import vue from '@vitejs/plugin-vue';
import path from 'path';
import { defineConfig } from 'vitest/config';

export default defineConfig({
    plugins: [vue()],
    test: {
        globals: true,
        environment: 'happy-dom',
        setupFiles: ['./resources/js/pages/auth/__tests__/setup.ts']
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js')
        }
    }
});
