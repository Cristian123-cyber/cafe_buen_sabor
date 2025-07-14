export default [
  {
    path: "/",
    redirect: { name: "Login" },
  },
  {
    path: "/auth/login",
    name: "Login",
    component: () => import("../views/auth/LoginView.vue"),
    meta: { requiresGuest: true }, // Solo accesible para no autenticados
  },
  {
    path: "/session/validate",
    name: "SessionValidate",
    component: () => import("../views/public/SessionValidateView.vue"),
    meta: { requiresAuth: false }, // pública
  },
  {
  path: '/error/unauthorized',
  name: 'Unauthorized',
  component: () => import('../views/error/Unauthorized.vue'),
  meta: {
    // No requiere autenticación para mostrarse, ya que un no-autenticado podría llegar aquí
  }
}
];
