import axios from "axios";
import api from "./api.js";
import { API_CONFIG } from "../utils/constants";
import { useLoadingStore } from "../stores/loadingS.js";


// Instancia separada para operaciones de autenticación (sin interceptors)
const authApi = axios.create({
  baseURL: API_CONFIG.BASE_URL,
  timeout: API_CONFIG.TIMEOUT,
  withCredentials: true,
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});

export const authService = {
  // Login
  login: async (credentials) => {

    try {
      const response = await authApi.post("/auth/login", credentials);
      return response.data;
    } catch (error) {
      return {
      success: false,
      message:
        error?.response?.data?.message ||
        error?.response?.message ||
        error.message ||
        "Error desconocido",
    };
     
    }
  },

  // Logout
  logout: async () => {
    try {
      const response = await api.post("/auth/logout");
      return response.data;
    } catch (error) {
      throw error;
    }
  },

  // 🔥 Refresh token - usa la instancia separada para evitar bucles
  refreshToken: async () => {
    try {
      
      const response = await authApi.post("/auth/refresh");
      return response.data;
    } catch (error) {
      console.log('Error en refresh service:', error);
      throw error;
    }
  },
  // Fetch user data
  fetchUser: async () => {
    try {
      const response = await api.get("/auth/me");
      return response.data;
    } catch (error) {
      throw error;
    }
  },
};
