import { createRouter, createWebHistory } from "vue-router";

// Importa tus archivos de rutas
import publicRoutes from "./publicRoutes.js";
import cashierRoutes from "./chasierRoutes.js";
import waiterRoutes from "./waiterRoutes.js";
import kitchenRoutes from "./kitchenRoutes.js";
import adminRoutes from "./adminRoutes.js";
import devicesRoutes from "./deviceRoutes.js";
import clientRoutes from "./clientRoutes.js";

const routes = [
  ...publicRoutes,
  ...cashierRoutes,
  ...waiterRoutes,
  ...kitchenRoutes,
  ...adminRoutes,
  ...devicesRoutes,
  ...clientRoutes
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {

  const { useAuthStore } = await import('../stores/authS');
  const { useAuth } = await import('../composables/useAuth');
  const { useSessionStore } = await import('../stores/clientSessionS');
  const { useClientSession } = await import('../composables/useClientSession.js');

  
  const authStore = useAuthStore();

  const sessionStore = useSessionStore();
  const { checkAuth } = useAuth();
  const { checkAuthClient } = useClientSession();

  // Esto solo se ejecuta una vez por carga de la página.
  if (!authStore.isAuthCheckComplete) {
    // checkAuth ahora validará el token del localStorage.
    await checkAuth();
    // Marcamos que la validación (exitosa o fallida) ya se hizo.
    authStore.isAuthCheckComplete = true;
  }
  const isAuthenticated = authStore.isAuthenticated;

  

  if (sessionStore.sessionStatus === 'idle' && sessionStore.sessionToken) {

    await checkAuthClient();
  }

  const userRole = authStore.userRole;
  const isClientSession = sessionStore.isSessionActive;

  if (
    to.meta.requiresClientSession &&
    !isClientSession &&
    !isAuthenticated
  ) {
    return next(authStore.getUnauthorized());
  }

  // 🔐 Bloquear acceso a rutas solo para invitados (login, etc.)
  if (to.meta.requiresGuest) {

    
    if (isAuthenticated) {
      return next({
        ...authStore.getDashboardRouteByRole(authStore.user.role_id),
        replace: true,
      });
    }

    if (isClientSession) {
      return next({ name: "ClientHome", replace: true }); // Redirigís al menú del cliente
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
