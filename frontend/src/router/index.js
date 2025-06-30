import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../stores/authS";

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
  {
    path: "/auth/login333",
    name: "LoginPR",
    component: () => import("../views/auth/LoginView.vue"),
  }
  
  // Aquí puedes añadir más rutas de otros roles o componentes
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  // 1. Asegurar que el estado de auth está inicializado antes de continuar.
  if (authStore.authStatus === "idle") {
    await authStore.checkAuth();
  }

  const isAuthenticated = authStore.isAuthenticated;
  const userRole = authStore.userRole;

  // 2. Redirigir si el usuario está logueado pero intenta acceder a una ruta de "invitado" (como el login).
  if (to.meta.requiresGuest && isAuthenticated) {
    // Lo enviamos a su dashboard correspondiente.
    return next(authStore.getDashboardRouteByRole(userRole));
  }

  // 3. Proteger rutas que requieren autenticación.
  if (to.meta.requiresAuth) {
    if (!isAuthenticated) {
      // No está autenticado, redirigir al login guardando la ruta a la que quería ir.
      return next({ name: "Login", query: { redirect: to.fullPath } });
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
