// src/stores/auth.js
import { defineStore } from "pinia";
import { ref, computed } from "vue";

export const useAuthStore = defineStore("auth", () => {
  const user = ref(null);
  try {
    const rawUser = localStorage.getItem("user");
    if (rawUser && rawUser !== "undefined") user.value = JSON.parse(rawUser);
  } catch (e) {
    console.warn("Error parseando user:", e);
  }
  const storedToken = localStorage.getItem("auth_token");
  const accessToken = ref(storedToken ? storedToken : null);

  const authStatus = ref("idle");
  const isAuthCheckComplete = ref(false); // <--- LA BANDERA CLAVE
  const FAKE_TOKEN = "fake-jwt-token-for-development";

  const isAuthenticated = computed(
    () => authStatus.value === "success" && !!accessToken.value && !!user.value
  );
  const userRole = computed(() => user.value?.role_name || "");
  const userName = computed(() => user.value?.name || "");

  const setAuthData = (userData, token) => {
    user.value = userData;
    accessToken.value = token;
    localStorage.setItem("user", JSON.stringify(userData));
    localStorage.setItem("auth_token", token);

    authStatus.value = "success"; // Marcar como autenticado exitosamente
  };

  const clearAuthData = () => {
    user.value = null;
    accessToken.value = null;
    localStorage.removeItem("user");
    localStorage.removeItem("auth_token");
    authStatus.value = "idle";
  };

  const getDashboardRouteByRole = (role) => {
    const roleRoutes = {
      5: { name: "AdminDashboard" },
      1: { name: "WaiterTables" },
      2: { name: "KitchenDashboard" },
      3: { name: "CashierTables" },
      4: { name: "DevicesQR", params: { table_id: user.value?.table_id} },
    };
    return roleRoutes[role] || { name: "Login" };
  };

  const getUnauthorized = () => {
    return { name: "Unauthorized" };
  };

  return {
    user,
    accessToken,
    isAuthenticated,
    userRole,
    userName,
    authStatus,
    setAuthData,
    clearAuthData,
    getDashboardRouteByRole,
    getUnauthorized,
    FAKE_TOKEN,
    isAuthCheckComplete, // <-- aquí está la magia
  };
});
