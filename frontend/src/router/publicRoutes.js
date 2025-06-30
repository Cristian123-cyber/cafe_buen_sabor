export default [
  {
    path: "/",
    redirect: "/auth/login", // Redirigir la raíz al login por defecto
  },
  {
    path: "/auth/login",
    name: "Login",
    component: () => import("../views/auth/TryView.vue"),
    meta: { requiresGuest: true }, // Solo accesible para no autenticados
  },
  {
    path: "/devices/qr/:table_id",
    name: "DevicesQR",
    component: () => import("../views/devices/displayQRView.vue"),
    meta: { requiresAuth: true, roles: ["Device"] }, // Rol especial para los displays

  }
  
];
