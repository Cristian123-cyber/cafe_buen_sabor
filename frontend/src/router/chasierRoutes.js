export default [
  {
    path: '/cashier/mesas',
    name: 'CashierTables',
    component: () => import('../views/cashier/CashierDashboard.vue'),
    meta: { requiresAuth: true }, // Solo accesible para no autenticados
  },
  {
    path: '/cashier/ventas',
    name: 'CashierSales',
    component: () => import('../views/cashier/CashierDashboard.vue'),
    meta: { requiresAuth: true }, // Solo accesible para no autenticados
  },
  // Puedes añadir más rutas de cliente aquí (OrderStatusView, etc.)
];