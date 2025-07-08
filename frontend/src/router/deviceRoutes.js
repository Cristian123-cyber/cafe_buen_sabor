export default [
  {
    path: '/device/qr/:table_id',
    name: 'DevicesQR',
    component: () => import('../views/devices/QRView.vue'),
    meta: { requiresAuth: true }, // Solo accesible para no autenticados
  }
];