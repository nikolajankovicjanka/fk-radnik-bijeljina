import {defineConfig} from "vite"
import vue from "@vitejs/plugin-vue"
import path from "path"

export default defineConfig({
    plugins: [vue()],

    resolve: {
        alias: {
            "@": path.resolve(__dirname, "src"),
        },
    },
    build: {
        outDir: "../backend/public/build",
        emptyOutDir: true,
        manifest: true,
    },
    server: {
        host: "127.0.0.1",
        port: 5173,
        strictPort: true,
        proxy: {
            "/api": {
                target: "http://127.0.0.1:8080",
                changeOrigin: true,
            },
            "/storage": {
                target: "http://127.0.0.1:8080",
                changeOrigin: true,
            },
        },
        hmr: {
            protocol: "ws",
            host: "127.0.0.1",
            port: 5173,
        },
    },
})
