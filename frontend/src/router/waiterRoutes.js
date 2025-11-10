export default [
  {
    path: '/waiter/mesas',
    name: 'WaiterTables',
    component: () => import('../views/waiter/WaiterDashboard.vue'),
    meta: { requiresAuth: true }, // Solo accesible para no autenticados
    children: [
      {
        path: ':id/orders',
        name: 'WaiterTableOrders',
        component: () => import('../views/waiter/TableOrdersView.vue'),
        meta: { requiresAuth: true }
      }
    ]
  }
  
];  