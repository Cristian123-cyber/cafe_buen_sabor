export default [
  {
    path: '/kitchen/dashboard',
    name: 'KitchenDashboard',
    component: () => import('../views/kitchen/KitchenDashboard.vue'),
    meta: { requiresAuth: true }, // Solo accesible para no autenticados
  }
  // Puedes añadir más rutas de cliente aquí (OrderStatusView, etc.)
];