// src/composables/useAuth.js
import { useAuthStore } from "../stores/authS";
import { authService } from "../services/auth";
import { useLoadingStore } from "../stores/loadingS";
import router from "../router/index.js";

export const useAuth = () => {
  const login = async (credentials) => {
    const authStore = useAuthStore();
    try {
      authStore.authStatus = "loading";

      const data = await authService.login(credentials);

      if (data.success) {
        authStore.setAuthData(data.data.user, data.data.access_token);
        router.replace(
          authStore.getDashboardRouteByRole(data.data.user.role_id)
        );
      } else {
        throw new Error(data.message || "Error desconocido del servidor");
      }
    } catch (error) {
      authStore.authStatus = "error";
      if (error instanceof Error) {
        throw error;
      } else {
        throw new Error(error.message || "Ocurrió un error desconocido");
      }
    }
  };

  const logout = async () => {
    console.log("Haciendo logout");
    const authStore = useAuthStore();

    try {
      await authService.logout();
    } catch (e) {
      console.log("Logout falló en servidor (ignorado):", e.message);
    }
    
    authStore.clearAuthData();
    router.replace({ name: "Login" });
  };

  const handleRefreshToken = async () => {
    const authStore = useAuthStore();

    try {
      const data = await authService.refreshToken();
      
      if (data.success && data.data?.access_token) {

        authStore.setAuthData(data.data.user, data.data.access_token);
      
        return data.data.access_token;
      } else {
        throw new Error(data.message || "Error al refrescar token");
      }
    } catch (error) {
      console.error("Error en handleRefreshToken composable:", error);
      // Limpiar datos de autenticación si el refresh falla
      authStore.clearAuthData();
      throw error;
    }
  };

  const checkAuth = async () => {
    const authStore = useAuthStore();
    const loadingStore = useLoadingStore();

    if (!authStore.accessToken) {
      authStore.authStatus = "error";
      return;
    }


    authStore.authStatus = "loading";
    try {
      const userData = await authService.fetchUser();
      
      if (userData.success && userData.data) {
        authStore.setAuthData(userData.data, authStore.accessToken);

      } else {
        throw new Error(userData.message || "Error al obtener datos del usuario");
      }
    } catch (error) {
      console.error("Error en checkAuth:", error);
      authStore.authStatus = "error";
      // No limpiar aquí - dejar que el interceptor maneje el refresh
    }
  };

  

  

  return {
    login,
    logout,
    checkAuth,
    handleRefreshToken,
  };
};