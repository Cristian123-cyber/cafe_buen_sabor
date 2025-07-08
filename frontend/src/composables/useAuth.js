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
        console.log('data recibida', data);
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

    if (authStore.accessToken === authStore.FAKE_TOKEN) {
      authStore.authStatus = "success";
      console.log("Sesión de desarrollo, check omitido");
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

  // Mock users para desarrollo
  const MOCK_USERS = {
    Mesero: {
      id: 1,
      name: "Usuario Mesero",
      email: "waiter@test.com",
      role_name: "Mesero",
      role_id: 1,
    },
    Cocinero: {
      id: 2,
      name: "Usuario Cocinero",
      email: "kitchen@test.com",
      role_name: "Cocinero",
      role_id: 2,
    },
    Cajero: {
      id: 3,
      name: "Usuario Cajero",
      email: "cashier@test.com",
      role_name: "Cajero",
      role_id: 3,
    },
    Administrador: {
      id: 4,
      name: "Usuario Admin",
      email: "admin@test.com",
      role_name: "Administrador",
      role_id: 5,
    },
    Device: {
      id: 5,
      name: "Dispositivo de Mesa",
      email: "device@gmail.com",
      role_name: "Device",
      role_id: 4,
      table_id: 1,
    },
  };

  const fakeLogin = (role) => {
    const authStore = useAuthStore();
    console.warn(`--- MODO SIMULACIÓN: Logueado como ${role} ---`);
    const fakeUserData = MOCK_USERS[role];

    if (!fakeUserData) {
      console.error(`Rol ${role} no encontrado en MOCK_USERS.`);
      return;
    }

    authStore.setAuthData(fakeUserData, authStore.FAKE_TOKEN);

    // Redirige automáticamente al dashboard del rol
    const targetRoute = authStore.getDashboardRouteByRole(
      authStore.user.role_id
    );
    router.replace(targetRoute);
  };

  return {
    login,
    logout,
    checkAuth,
    handleRefreshToken,
    fakeLogin,
  };
};