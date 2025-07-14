import axios from "axios";
import api from "./api.js";
import { API_CONFIG } from "../utils/constants";
import { useSessionStore } from "../stores/clientSessionS.js";
import { useAuthStore } from "../stores/authS.js";

import { useLoadingStore } from "../stores/loadingS.js";
import router from "../router/index.js";

// Instancia separada para operaciones de cliente
const clientApi = axios.create({
  baseURL: API_CONFIG.BASE_URL,
  timeout: API_CONFIG.TIMEOUT,
  withCredentials: true,
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});

// --- 2. INTERCEPTOR DE PETICIONES ---
clientApi.interceptors.request.use(
  (config) => {
    // Obtenemos el store FUERA de un componente o setup

    const sessionStore = useSessionStore();
    const loadingStore = useLoadingStore();
    // Incrementamos el contador de peticiones activas
    loadingStore.requestStarted();

    // Usamos el estado de Pinia como fuente de verdad
    if (sessionStore.sessionToken) {
      config.headers.Authorization = `Bearer ${sessionStore.sessionToken}`;
    }
    return config;
  },
  (error) => {
    const appStore = useLoadingStore();
    appStore.requestFinished(); // <--- AÑADIDO
    return Promise.reject(error);
  }
);

clientApi.interceptors.response.use(
  (response) => {
    const appStore = useLoadingStore();
    appStore.requestFinished();

    return response;
  },
  async (error) => {
    const sessionStore = useSessionStore();
    const authStore = useAuthStore();

    const appStore = useLoadingStore();
    appStore.requestFinished();

    console.log("UN AUTH TORIZED");
    sessionStore.clearSession();


    if (!authStore.isAuthenticated){
      
      router.replace({ name: "Unauthorized" });
    }


    return Promise.reject(error);
  }
);

export const clientSessionService = {
  /**
   * Valida el QR contra la mesa en el backend y retorna el JWT + info
   */
  validateQr: async (qrToken, tableId) => {
    try {
      console.log("validando qr llamando api");

      const response = await api.post("/table-sessions/validate-qr", {
        qr_token: qrToken,
        id_table: tableId,
      });
      console.log(response);
      return response.data;
    } catch (error) {
      console.log(error);

      throw error;
    }
  },

  fetchClient: async (sessionId) => {


    try {

      console.log('validando session');
      const response = await clientApi.post("/table-sessions/validate-session", { session_id: sessionId} );

      console.log(response);
      return response.data;
      
    } catch (error) {



      console.log(error);
      throw error;
      
      
    }




    


  },
};
