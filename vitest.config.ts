import { resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { defineConfig } from 'vitest/config';

const rootDir = fileURLToPath(new URL('.', import.meta.url));

export default defineConfig({
    resolve: {
        alias: {
            '@': resolve(rootDir, 'resources/js'),
            'ziggy-js': resolve(rootDir, 'vendor/tightenco/ziggy'),
        },
    },
    test: {
        environment: 'node',
        include: ['resources/js/**/*.test.{ts,tsx}'],
    },
});
