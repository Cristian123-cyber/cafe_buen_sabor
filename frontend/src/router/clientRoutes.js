export default [
  {
    path: '/client/home',
    name: 'ClientHome',
    component: () => import('../views/client/HomeView.vue'),
    meta: { requiresClientSession: true }, // Solo accesible para no autenticados
  },
  {
    path: '/client/menu',
    name: 'ClientMenu',
    component: () => import('../views/client/MenuView.vue'),
    meta: { requiresClientSession: true }, // Solo accesible para no autenticados
  },
  {
    path: '/client/orders',
    name: 'ClientOrders',
    component: () => import('../views/client/OrdersView.vue'),
    meta: { requiresClientSession: true }, // Solo accesible para no autenticados
  },
  {
    path: '/client/cart',
    name: 'ClientCart',
    component: () => import('../views/client/CartView.vue'),
    meta: { requiresClientSession: true }, // Solo accesible para no autenticados
  },
  {
    path: '/product/:id',
    name: 'ProductDetail',
    component: () => import('../views/client/ProductDetails.vue'),
    meta: { requiresClientSession: true }, // Solo accesible para no autenticados
  },
  // Puedes añadir más rutas de cliente aquí (OrderStatusView, etc.)
];