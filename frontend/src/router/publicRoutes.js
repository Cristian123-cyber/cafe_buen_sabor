

export default [
  {
  path: "/",
  redirect: { name: 'Login' }
},
  {
    path: "/auth/login",
    name: "Login",
    component: () => import("../views/auth/LoginView.vue"),
    meta: { requiresGuest: true }, // Solo accesible para no autenticados
  },
  {
    path: "/tables/validate-qr",
    name: "SessionValidate",
    component: () => import("../views/public/SessionValidateView.vue"),
    meta: { requiresAuth: false }, // pública
  },
];
