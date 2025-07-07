/* import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue()],
}) */

import { defineConfig, loadEnv } from "vite";
import vue from "@vitejs/plugin-vue";
import tailwindcss from "@tailwindcss/vite";
import Components from "unplugin-vue-components/vite";
import Icons from "unplugin-icons/vite";
import IconsResolver from "unplugin-icons/resolver";

export default defineConfig(({ mode }) => {
  // Carga las variables de entorno desde el .env de la carpeta frontend
  const env = loadEnv(mode, process.cwd(), "");

  return {
    plugins: [
      vue(),
      tailwindcss(),
      Components({
        dirs: ["src/components"], // Autoimporta los componentes locales
        extensions: ["vue"],
        deep: true,
        resolvers: [
          IconsResolver({
            prefix: "i", // permite usar <i-mdi-home />, <i-fa6-solid-user />, etc.
          }),
        ],
      }),
      Icons({
        autoInstall: true, // Descarga automáticamente los íconos usados
      }),
    ],

    server: {
      host: "0.0.0.0", // Esto se mantiene fijo para Docker

      // Usamos la variable de entorno para el puerto.
      // Hacemos un parseInt para asegurarnos de que es un número.
      port: parseInt(env.VITE_PORT) || 5173,
      watch: {
        // ---- ESTE ES EL BLOQUE IMPORTANTE ----
        // Reactivamos el polling porque los eventos nativos no funcionan
        usePolling: true,

        // Le decimos a Vite que revise los archivos cada 500 milisegundos (medio segundo)
        // Puedes ajustar este valor. 1000ms (1 segundo) también es una buena opción.
        // ¡Esto es lo que reduce drásticamente el uso de CPU!
        interval: 1000,
      },

      proxy: {
        "/api": {
          // Usamos la variable de entorno para el target del proxy
          target: env.VITE_PROXY_TARGET || "http://localhost:8000",
          changeOrigin: true,
        },
      },
    },
  };
});
