import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'

// https://vite.dev/config/
export default defineConfig({
  plugins: [react(), tailwindcss()],
  server: {
    proxy: {
      '/api': {
        // XAMPP Apache を使用（Apacheが起動している必要あり）
        target: 'http://localhost/littlecoder/backend/public',
        changeOrigin: true,
      },
    },
  },
})
