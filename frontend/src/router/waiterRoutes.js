export default [
  {
    path: '/waiter/dashboard',
    name: 'WaiterDashboard',
    component: () => import('../views/waiter/WaiterDashboard.vue'),
    meta: { requiresAuth: true }, // Solo accesible para no autenticados
  }
  // Puedes añadir más rutas de cliente aquí (OrderStatusView, etc.)
];