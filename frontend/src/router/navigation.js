
import { iconsMap } from '../iconSafeList.js';

export const navigationConfig = {
  5: [
    { label: 'Dashboard', to: { name: 'Login' }, icon: iconsMap['mdi-view-dashboard-outline'] },
    { label: 'Usuarios', to: { name: 'Login' }, icon: iconsMap['mdi-account-group-outline'] },
    { label: 'Mesas', to: { name: 'Login' }, icon: iconsMap['mdi-table-chair'] },
    { label: 'Productos', to: { name: 'Login' }, icon: iconsMap['mdi-food-outline'] },
    { label: 'Reportes', to: { name: 'Login' }, icon: iconsMap['mdi-chart-line'] },
  ],
  // ... lo mismo para los demás roles
};
/* export const navigationConfig = {
  // --- ADMINISTRADOR ---
  5: [
    { label: 'Dashboard', to: { name: 'Login' }, icon: 'i-mdi-view-dashboard-outline' },
    { label: 'Usuarios', to: { name: 'Login' }, icon: 'i-mdi-account-group-outline' },
    { label: 'Mesas', to: { name: 'Login' }, icon: 'i-mdi-table-chair' },
    { label: 'Productos', to: { name: 'Login' }, icon: 'i-mdi-food-outline' },
    { label: 'Reportes', to: { name: 'Login' }, icon: 'i-mdi-chart-line' },
  ],
  
  // --- MESERO ---
  3: [
    { label: 'Dashboard', to: { name: 'Login' }, icon: 'i-mdi-view-dashboard-outline' },
    { label: 'Mesas', to: { name: 'Login' }, icon: 'i-mdi-table-chair' },
    { label: 'Pedidos', to: { name: 'Login' }, icon: 'i-mdi-clipboard-list-outline' },
  ],

  // --- COCINERO ---
  Cocinero: [
    { label: 'Cola de Pedidos', to: { name: 'KitchenQueue' }, icon: 'i-mdi-stove' },
    { label: 'Activos', to: { name: 'KitchenActiveOrders' }, icon: 'i-mdi-fire' },
  ],

  // --- CAJERO ---
  Cajero: [
    { label: 'Dashboard', to: { name: 'CashierDashboard' }, icon: 'i-mdi-view-dashboard-outline' },
    { label: 'Mesas', to: { name: 'CashierTables' }, icon: 'i-mdi-table-chair' },
    { label: 'Facturas', to: { name: 'CashierInvoices' }, icon: 'i-mdi-receipt-text-outline' },
  ],

  // --- DEVICE (Opcional, si necesitara un sidebar) ---
  Device: [],
};

/**
 * Gets the navigation links for a given user role.
 * @param {String} role The role of the user.
 * @returns {Array} An array of navigation link objects.
 */
export const getNavigationForRole = (role) => {
  return navigationConfig[role] || [];
};