// src/stores/auth.js
import { defineStore } from "pinia";
import { ref, computed } from "vue";
import router from "../router";
import { authService } from "../services/auth"; // Usamos nuestro nuevo servicio

export const useAuthStore = defineStore("auth", () => {
  // --- STATE ---
  const user = ref(JSON.parse(localStorage.getItem("user")) || null);
  const accessToken = ref(localStorage.getItem("auth_token") || null);
  // Un estado para saber si la app está verificando la sesión inicial
  const authStatus = ref("idle"); // 'idle' | 'loading' | 'success' | 'error'
  const FAKE_TOKEN = "fake-jwt-token-for-development";

  // --- GETTERS ---
  const isAuthenticated = computed(() => !!accessToken.value && !!user.value);
  const userRole = computed(() => user.value?.role_name || 5); // Ajusta a tu estructura de user

  // --- ACTIONS ---

  /**
   * Guarda los datos de autenticación en el estado y localStorage.
   */
  const setAuthData = (userData, token) => {
    user.value = userData;
    accessToken.value = token;
    localStorage.setItem("user", JSON.stringify(userData));
    localStorage.setItem("auth_token", token);
  };

  /**
   * Limpia el estado y el storage.
   */
  const clearAuthData = () => {
    user.value = null;
    accessToken.value = null;
    localStorage.removeItem("user");
    localStorage.removeItem("auth_token");
    // Al limpiar, reseteamos el estado a 'idle' para la proxima carga
    authStatus.value = "idle";
  };

  /**
   * Acción de Login: Llama al servicio, guarda los datos y redirige.
   */
  const login = async (credentials) => {
    try {
      const data = await authService.login(credentials);
      // El backend nos devuelve el usuario y el access_token
      setAuthData(data.user, data.access_token);

      // Redirigir al dashboard correspondiente
      const targetRoute = getDashboardRouteByRole(data.user.role_name);
      router.push(targetRoute);
    } catch (error) {
      console.error(
        "Error en el login:",
        error.response?.data?.message || error.message
      );
      // Lanza el error para que el componente de login pueda mostrar un mensaje
      throw error;
    }
  };

  /**
   * Acción de Logout: Limpia todo y redirige al login.
   */
  const logout = () => {
    // Opcional: notificar al backend
    // authService.logout().catch(e => console.error("Server logout failed", e));
    clearAuthData();
    router.push({ name: "Login" }); // Asegúrate de que tienes una ruta llamada 'Login'
  };

  /**
   * Acción de Refresh Token: Usada por el interceptor de Axios.
   */
  const handleRefreshToken = async () => {
    try {
      const data = await authService.refreshToken();
      accessToken.value = data.access_token;
      localStorage.setItem("auth_token", data.access_token);
      console.log("Token refrescado con éxito.");
      return data.access_token;
    } catch (error) {
      console.error("El refresh token ha fallado, cerrando sesión.");
      // Si el refresh falla, la sesión es inválida.
      logout();
      throw error;
    }
  };

  const getDashboardRouteByRole = (role) => {
    const roleRoutes = {
      Administrador: { name: "AdminDashboard" }, // Asegúrate de tener esta ruta
      Mesero: { name: "WaiterDashboard" }, 
      Cocinero: { name: "KitchenDashboard" }, 
      Cajero: { name: "CashierDashboard" }, // Asegúrate de tener esta ruta
      Device: { name: "DevicesQR", params: { table_id: user.value?.table_id } }, 
    };
    return roleRoutes[role] || { name: "Login" }; // Fallback a Login si no hay ruta específica
  };
  const checkAuth = async () => {
    if (!accessToken.value) {
      authStatus.value = "error";
      return;
    }

     //Si estamos en una sesión falsa, la damos por válida sin llamar a la API
    if (accessToken.value === FAKE_TOKEN) {
        console.warn("checkAuth omitido: Sesión de desarrollo activa.");
        authStatus.value = "success";
        return;
    }

    authStatus.value = "loading";
    try {
      // Usamos el endpoint /me para validar el token y obtener datos frescos del usuario
      const userData = await authService.fetchUser();
      setAuthData(userData, accessToken.value);
      authStatus.value = "success";
    } catch (error) {
      // El token es inválido (el interceptor ya intentó el refresh)
      console.error("La verificación de autenticación falló:", error);
      authStatus.value = "error";
      // El interceptor ya llama a logout en caso de fallo del refresh, pero esto es una doble seguridad.
      if (isAuthenticated.value) {
        logout();
      }
    }
  };
  const MOCK_USERS = {
    Mesero: {
      id: 1,
      name: "Usuario Mesero",
      email: "waiter@test.com",
      role_name: "Mesero",
    },
    Cocinero: {
      id: 2,
      name: "Usuario Cocinero",
      email: "kitchen@test.com",
      role_name: "Cocinero",
    },
    Administrador: {
      id: 3,
      name: "Usuario Admin",
      email: "admin@test.com",
      role_name: "Administrador",
    },
    Device: {
      id: 4,
      name: "Dispositivo de Mesa",
      email: "device@gmail.com",
      role_name: "Device",
      table_id: 1, // ID de la mesa asociada
    }
  };
  /**
   * ACCIÓN DE DESARROLLO: Simula un login para un rol específico.
   * @param {string} role - 'Mesero', 'Administrador', etc.
   */
  const fakeLogin = (role) => {
    console.warn(`--- MODO SIMULACIÓN: Logueado como ${role} ---`);
    const fakeUserData = MOCK_USERS[role];

    if (!fakeUserData) {
      console.error(`Rol ${role} no encontrado en MOCK_USERS.`);
      return;
    }
     // Esto previene que el guardián de navegación llame a checkAuth().
     
     setAuthData(fakeUserData, FAKE_TOKEN);
     authStatus.value = "success"; 

    // Redirigir al dashboard correspondiente
    const targetRoute = getDashboardRouteByRole(role);
    router.push(targetRoute);
  };

  const getUnauthorized = () =>{
    
    return { name: "Unauthorized" }; // Asegúrate de tener una ruta llamada 'Unauthorized'

  }

  return {
    user,
    accessToken,
    isAuthenticated,
    userRole,
    authStatus,
    login,
    logout,
    handleRefreshToken,
    checkAuth,
    fakeLogin,
    getDashboardRouteByRole,
    getUnauthorized
  };
});
