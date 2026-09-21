import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

// Dev server proxies /api to the CMS server (browser never calls localhost directly).
export default defineConfig({
  plugins: [react()],
  server: {
    host: '0.0.0.0',
    port: 5173,
    allowedHosts: true,
    proxy: { '/api': { target: process.env.API_URL || 'http://127.0.0.1:4170', changeOrigin: true } },
  },
  build: { outDir: 'dist', chunkSizeWarningLimit: 900 },
});
