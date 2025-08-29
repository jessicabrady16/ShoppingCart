import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
    server: {
        host: "0.0.0.0",
        port: 5173,
        strictPort: true, // don't hop to 5174/5178 etc.
        hmr: { host: "localhost", port: 5173 },
    },
    plugins: [
        laravel({
            input: ["resources/js/cart.js", "resources/js/shop.js"],
            refresh: true,
        }),
        vue(),
    ],
});
