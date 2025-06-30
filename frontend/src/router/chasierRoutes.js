export default [
  {
    path: '/cashier/dashboard',
    name: 'CashierDashboard',
    component: () => import('../views/shared/genericDashboard.vue'),
    meta: { requiresAuth: true }, // Solo accesible para no autenticados
  }
  // Puedes añadir más rutas de cliente aquí (OrderStatusView, etc.)
];