export default [
  {
    path: '/cashier/mesas',
    name: 'CashierTables',
    component: () => import('../views/cashier/CashierDashboard.vue'),
    meta: { requiresAuth: true }, // Solo accesible para no autenticados
    children: [
      {
        path: ':id/orders',
        name: 'CashierTableOrders',
        component: () => import('../views/cashier/TableOrdersCashier.vue'),
        meta: { requiresAuth: true }
      }
    ]
  },
  {
    path: '/cashier/ventas',
    name: 'CashierSales',
    component: () => import('../views/admin/AdminSales.vue'),
    meta: { requiresAuth: true }, // Solo accesible para no autenticados
  },
  // Puedes añadir más rutas de cliente aquí (OrderStatusView, etc.)
];