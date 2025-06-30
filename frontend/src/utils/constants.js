// src/utils/constants.js

// Roles del sistema
export const USER_ROLES = {
  TABLE_DISPLAY: 4, // Pantalla de mesa
  WAITER: 1, // Mesero
  KITCHEN: 2, // Cocina
  CASHIER: 3, // Cajero
  ADMIN: 5, // Administrador
}

// Estados de pedidos
export const ORDER_STATUSES = {
  PENDING: 'PENDING',        // Pedido realizado, esperando confirmación
  CONFIRMED: 'CONFIRMED',    // Confirmado por mesero, enviado a cocina
  READY: 'READY',           // Preparado por cocinero, listo para recoger
  DELIVERED: 'DELIVERED',    // Entregado al cliente por mesero
  CANCELLED: 'CANCELLED'     // Cancelado
}

// Labels para mostrar en UI
export const ORDER_STATUS_LABELS = {
  PENDING: 'Pendiente',
  CONFIRMED: 'En Cocina',
  READY: 'Listo para Recoger',
  DELIVERED: 'Entregado',
  CANCELLED: 'Cancelado'
}

// Colores por estado
export const ORDER_STATUS_COLORS = {
  PENDING: 'bg-yellow-100 text-yellow-800 border-yellow-200',
  CONFIRMED: 'bg-blue-100 text-blue-800 border-blue-200',
  READY: 'bg-green-100 text-green-800 border-green-200',
  DELIVERED: 'bg-gray-100 text-gray-800 border-gray-200',
  CANCELLED: 'bg-red-100 text-red-800 border-red-200'
}

// Estados de mesa
export const TABLE_STATUSES = {
  FREE: 'FREE',          // Mesa libre
  OCCUPIED: 'OCCUPIED',    // Mesa ocupada
  INACTIVE: 'INACTIVE' // Mesa inactiva (no disponible)
}

// Tipos de notificaciones
export const NOTIFICATION_TYPES = {
  ORDER_READY: 'ORDER_READY',
  ORDER_CONFIRMED: 'ORDER_CONFIRMED',
  ORDER_CANCELLED: 'ORDER_CANCELLED'
}

/* // Métodos de pago
export const PAYMENT_METHODS = {
  CASH: 'CASH',
  CARD: 'CARD',
  TRANSFER: 'TRANSFER'
} */

// Configuración de polling
export const POLLING_INTERVALS = {
  NOTIFICATIONS: 15000,  // 15 segundos
  QR_REFRESH: 30000,     // 30 segundos
  ORDER_STATUS: 10000    // 10 segundos
}

// Configuración de QR
export const QR_CONFIG = {
  REFRESH_INTERVAL: 600000, // 10 minutos en milisegundos
  SIZE: 200,
  MARGIN: 2,
  ERROR_CORRECTION: 'M'
}

/* // Permisos por rol
export const ROLE_PERMISSIONS = {
  [USER_ROLES.TABLE_DISPLAY]: [
    'display_qr',
    'view_table_info'
  ],
  [USER_ROLES.WAITER]: [
    'view_tables',
    'confirm_orders',
    'mark_delivered',
    'view_notifications',
    'assign_to_orders'
  ],
  [USER_ROLES.KITCHEN]: [
    'view_confirmed_orders',
    'mark_ready',
    'view_queue'
  ],
  [USER_ROLES.CASHIER]: [
    'view_delivered_orders',
    'process_payment',
    'unify_orders',
    'generate_invoice',
    'assign_waiter'
  ],
  [USER_ROLES.ADMIN]: [
    'manage_users',
    'manage_tables',
    'manage_products',
    'view_analytics',
    'refresh_qr',
    'manage_permissions',
    'view_reports'
  ]
}
 */
// Navegación por rol
export const ROLE_NAVIGATION = {
  [USER_ROLES.WAITER]: [
    { name: 'Dashboard', path: '/meseros/dashboard', icon: 'i-mdi-view-dashboard' },
    { name: 'Mesas', path: '/meseros/tables', icon: 'i-mdi-table-furniture' },
    { name: 'Pedidos', path: '/meseros/orders', icon: 'i-mdi-clipboard-list' }
  ],
  [USER_ROLES.KITCHEN]: [
    { name: 'Cola', path: '/cocina/queue', icon: 'i-mdi-chef-hat' }, // Cola de pedidos
    { name: 'Activos', path: '/cocina/active', icon: 'i-mdi-fire' } // Pedidos activos
  ],
  [USER_ROLES.CASHIER]: [
    { name: 'Dashboard', path: '/caja/dashboard', icon: 'i-mdi-cash-register' },
    { name: 'Mesas', path: '/caja/tables', icon: 'i-mdi-table-furniture' },
    { name: 'Checkout', path: '/caja/checkout', icon: 'i-mdi-credit-card' },
    { name: 'Facturas', path: '/caja/invoices', icon: 'i-mdi-receipt' }
  ],
  [USER_ROLES.ADMIN]: [
    { name: 'Dashboard', path: '/admin/dashboard', icon: 'i-mdi-view-dashboard' },
    { name: 'Usuarios', path: '/admin/users', icon: 'i-mdi-account-group' },
    { name: 'Mesas', path: '/admin/tables', icon: 'i-mdi-table-furniture' },
    { name: 'Productos', path: '/admin/products', icon: 'i-mdi-coffee' },
    { name: 'Permisos', path: '/admin/permissions', icon: 'i-mdi-shield-account' },
    { name: 'Reportes', path: '/admin/reports', icon: 'i-mdi-chart-line' }
  ]
}

// Configuración de API
export const API_CONFIG = {
  BASE_URL: import.meta.env.VITE_API_URL || '/api',
  TIMEOUT: 10000,
  RETRY_ATTEMPTS: 3
}

// Mensajes de error comunes
export const ERROR_MESSAGES = {
  NETWORK_ERROR: 'Error de conexión. Verifica tu internet.',
  UNAUTHORIZED: 'No tienes permisos para realizar esta acción.',
  FORBIDDEN: 'Acceso denegado.',
  NOT_FOUND: 'Recurso no encontrado.',
  SERVER_ERROR: 'Error del servidor. Intenta más tarde.',
  VALIDATION_ERROR: 'Datos inválidos. Revisa la información.',
  QR_EXPIRED: 'El código QR ha expirado. Escanea el nuevo código.',
  TABLE_OCCUPIED: 'La mesa ya está ocupada.',
  ORDER_NOT_FOUND: 'Pedido no encontrado.'
}