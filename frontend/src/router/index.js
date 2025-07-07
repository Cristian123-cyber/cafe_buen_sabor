import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../stores/authS";
import { useAuth } from "../composables/useAuth";
import { useSessionStore } from "../stores/clientSessionS.js";

// Importa tus archivos de rutas
import publicRoutes from "./publicRoutes.js";
import cashierRoutes from "./chasierRoutes.js";
import waiterRoutes from "./waiterRoutes.js";
import kitchenRoutes from "./kitchenRoutes.js";
import adminRoutes from "./adminRoutes.js";

const routes = [
  ...publicRoutes,
  ...cashierRoutes,
  ...waiterRoutes,
  ...kitchenRoutes,
  ...adminRoutes,

  // Aquí puedes añadir más rutas de otros roles o componentes
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  const sessionStore = useSessionStore();
  const { checkAuth } = useAuth();

  // Esto solo se ejecuta una vez por carga de la página.
  if (!authStore.isAuthCheckComplete) {
    // checkAuth ahora validará el token del localStorage.
    await checkAuth();
    // Marcamos que la validación (exitosa o fallida) ya se hizo.
    authStore.isAuthCheckComplete = true;
  }

  const isAuthenticated = authStore.isAuthenticated;
  const userRole = authStore.userRole;
  const isClientSession = sessionStore.isSessionActive;

  if (
    to.meta.requiresClientSession &&
    !sessionStore.isSessionActive &&
    !authStore.isAuthenticated
  ) {
    return next(authStore.getUnauthorized());
  }

  // 🔐 Bloquear acceso a rutas solo para invitados (login, etc.)
  if (to.meta.requiresGuest) {
    if (isAuthenticated) {
      console.log("ya estas melo");
      return next({
        ...authStore.getDashboardRouteByRole(authStore.user.role_id),
        replace: true,
      });
    }

    if (isClientSession) {
      return next({ name: "ClientMenu", replace: true }); // Redirigís al menú del cliente
    }
  }

  // 3. Proteger rutas que requieren autenticación.
  if (to.meta.requiresAuth) {
    if (!isAuthenticated) {
      // No está autenticado, redirigir al login
      return next({ name: "Login", replace: true });
    }

    // Si está autenticado, verificar si tiene el rol requerido.
    if (to.meta.roles && !to.meta.roles.includes(userRole)) {
      // No tiene el rol permitido, redirigir a una página de "No Autorizado" o a su dashboard.

      return next(authStore.getUnauthorized());
    }
  }

  // 4. Si ninguna de las condiciones anteriores se cumple, permitir el acceso.
  return next();
});

export default router;
