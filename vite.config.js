import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
  ],
  server: {
    watch: {
      ignored: ['**/storage/framework/views/**'],
    },
  },
  build: {
    // Inline small assets (<4KB) as base64 to save round-trips on mobile
    assetsInlineLimit: 4096,
    // Generate source maps only in dev — keep prod bundle lean
    sourcemap: false,
    // Chunk splitting for better caching granularity
    rollupOptions: {
      output: {
        // Separate vendor chunk so app code re-deploys don't bust the vendor cache
        manualChunks: {
          vendor: [],
        },
        // 6-char content hash for strong immutable caching
        chunkFileNames:  'assets/[name]-[hash:6].js',
        entryFileNames:  'assets/[name]-[hash:6].js',
        assetFileNames:  'assets/[name]-[hash:6][extname]',
      },
    },
    // Warn when chunk exceeds 512KB
    chunkSizeWarningLimit: 512,
  },
})
