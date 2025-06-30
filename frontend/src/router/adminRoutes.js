export default [
  {
    path: '/admin/dashboard',
    name: 'AdminDashboard',
    component: () => import('../views/shared/genericDashboard.vue'),
    meta: { requiresAuth: true, roles: ["Administrador"] }, // Solo accesible para no autenticados
  }
  // Puedes añadir más rutas de cliente aquí (OrderStatusView, etc.)
];