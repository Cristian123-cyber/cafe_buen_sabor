// src/services/api.js
import axios from "axios";
import { API_CONFIG, ERROR_MESSAGES } from "../utils/constants";

// ... importaciones ...
import { useAuthStore } from "../stores/authS.js";
import { useLoadingStore } from "../stores/loadingS";
import { useAuth } from "../composables/useAuth";

import { useAlert } from "../composables/useAlert";

import router from "../router/index.js";

const api = axios.create({
  baseURL: API_CONFIG.BASE_URL,
  timeout: API_CONFIG.TIMEOUT,
  withCredentials: true, // Esto es para que Axios envíe cookies HttpOnly
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});

// --- 2. INTERCEPTOR DE PETICIONES ---
api.interceptors.request.use(
  (config) => {
    // Obtenemos el store FUERA de un componente o setup

    const authStore = useAuthStore();
    const loadingStore = useLoadingStore();
    // Incrementamos el contador de peticiones activas
    loadingStore.requestStarted();

    // Usamos el estado de Pinia como fuente de verdad
    if (authStore.accessToken) {
      config.headers.Authorization = `Bearer ${authStore.accessToken}`;
    }
    return config;
  },
  (error) => {
    const appStore = useLoadingStore();
    appStore.requestFinished(); // <--- AÑADIDO
    return Promise.reject(error);
  }
);

// Variable para evitar múltiples llamadas de refresh simultáneas
let refreshTokenPromise = null;

api.interceptors.response.use(
  (response) => {
    // ----> ¡ACCIÓN DE CARGA! Decrementamos el contador al recibir una respuesta exitosa <----

    const appStore = useLoadingStore();
    appStore.requestFinished();

    return response;
  },
  async (error) => {
    const appStore = useLoadingStore();
    const alert = useAlert();

    appStore.requestFinished(); // Decrementamos el contador de peticiones activas al recibir un error

    const originalRequest = error.config;
    // Error de red (sin respuesta del servidor)
    if (!error.response) {
      alert.show({
        variant: "error",
        title: "Error de red",
        message: "Ha ocurrido un error, verifica tu conexion",
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar",
      });

      return Promise.reject(error);
    }
    // Si el error no es 401 o la petición ya es un reintento, lo dejamos pasar.
    if (error.response?.status !== 401 || originalRequest._retry) {
      if (error.response.status === 500) {
        alert.show({
          variant: "error",
          title: "Error interno del servidor",
          message: "Ha ocurrido un error.",
          confirmButtonText: "Aceptar",
          cancelButtonText: "Cancelar",
        });
      }
      return Promise.reject(error);
    }

    // Si ya hay una petición de refresh en curso, esperamos a que termine
    if (refreshTokenPromise) {
      try {
        const newToken = await refreshTokenPromise;
        originalRequest.headers["Authorization"] = `Bearer ${newToken}`;
        return api(originalRequest);
      } catch (e) {
        return Promise.reject(e);
      }
    }

    originalRequest._retry = true;
    const { handleRefreshToken } = useAuth();

    // Creamos una nueva promesa para el refresh y la guardamos

    try {
      refreshTokenPromise = handleRefreshToken();
      const newToken = await refreshTokenPromise;
      // Reseteamos la promesa una vez resuelta
      refreshTokenPromise = null;

      // Reintentamos la petición original con el nuevo token
      originalRequest.headers["Authorization"] = `Bearer ${newToken}`;
      return api(originalRequest);
    } catch (refreshError) {
      console.error("Error en refresh API INTERCEPTOR", refreshError);
      refreshTokenPromise = null;
      // aquí podríamos añadir una notificación si quisiéramos.
      await alert.show({
        variant: "error",
        title: "Tu sesion ha expirado.",
        message: "Por favor, intentea iniciar sesion nuevamente",
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar",
      });

      // 🔥 Acción extra recomendada:
      const authStore = useAuthStore();
      authStore.clearAuthData();
      router.replace({ name: "Login" });

      return Promise.reject(refreshError);
    }
  }
);

export default api;
