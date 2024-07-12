import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
  plugins: [react()],
  build: {
    outDir: 'public/build',
    manifest: true,
    rollupOptions: {
      input: 'resources/js/main.jsx',
    },
  },
  resolve: {
    alias: {
      '@': '/resources/js',
    },
  },
});
