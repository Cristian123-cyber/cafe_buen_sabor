export default [
  {
    path: '/admin/dashboard',
    name: 'AdminDashboard',
    component: () => import('../views/admin/AdminDashboard.vue'),
    meta: { requiresAuth: true, roles: ["Administrador"], title: 'Dashboard' }, 
  },
  {
    path: '/admin/users',
    name: 'AdminUsers',
    component: () => import('../views/admin/AdminUsers.vue'),
    meta: { requiresAuth: true, roles: ["Administrador"], title: 'Usuarios' }, 
  },
  {
    path: '/admin/mesas',
    name: 'AdminTables',
    component: () => import('../views/admin/AdminTables.vue'),
    meta: { requiresAuth: true, roles: ["Administrador"], title: 'Mesas' }, 
  },
  {
    path: '/admin/productos',
    name: 'AdminProducts',
    component: () => import('../views/admin/AdminProducts.vue'),
    meta: { requiresAuth: true, roles: ["Administrador"], title: 'Productos' }, 
  },
  {
    path: '/admin/reportes',
    name: 'AdminReports',
    component: () => import('../views/admin/AdminReports.vue'),
    meta: { requiresAuth: true, roles: ["Administrador"], title: 'Reportes' }, 
  },
  {
    path: '/admin/ingredients',
    name: 'AdminIngredients',
    component: () => import('../views/admin/AdminIngredients.vue'),
    meta: { requiresAuth: true, roles: ["Administrador"], title: 'Ingredientes' }, 
  },
  {
    path: '/admin/ordenes',
    name: 'AdminOrders',
    component: () => import('../views/admin/AdminOrders.vue'),
    meta: { requiresAuth: true, roles: ["Administrador"], title: 'Ordenes' }, 
  },
  {
    path: '/admin/ventas',
    name: 'AdminSales',
    component: () => import('../views/admin/AdminSales.vue'),
    meta: { requiresAuth: true, roles: ["Administrador"], title: 'Ventas' }, 
  },
  {
    path: '/admin/sesiones',
    name: 'AdminSessions',
    component: () => import('../views/admin/AdminSessions.vue'),
    meta: { requiresAuth: true, roles: ["Administrador"], title: 'Sesiones' }, 
  },
  
];