export default [
  {
    path: '/waiter/mesas',
    name: 'WaiterTables',
    component: () => import('../views/waiter/WaiterDashboard.vue'),
    meta: { requiresAuth: true }, // Solo accesible para no autenticados
  },
  {
    path: '/waiter/pedidos',
    name: 'WaiterOrders',
    component: () => import('../views/waiter/WaiterDashboard.vue'),
    meta: { requiresAuth: true }, // Solo accesible para no autenticados
  }
  // Puedes añadir más rutas de cliente aquí (OrderStatusView, etc.)
];