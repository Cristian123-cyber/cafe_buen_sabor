
import { iconsMap } from '../iconSafeList.js';

export const navigationConfig = {
  5: [
    { label: 'Dashboard', to: { name: 'Login' }, icon: iconsMap['mdi-view-dashboard-outline'] },
    { label: 'Usuarios', to: { name: 'Login' }, icon: iconsMap['mdi-account-group-outline'] },
    { label: 'Mesas', to: { name: 'Login' }, icon: iconsMap['mdi-table-chair'] },
    { label: 'Productos', to: { name: 'Login' }, icon: iconsMap['mdi-food-outline'] },
    { label: 'Reportes', to: { name: 'Login' }, icon: iconsMap['mdi-chart-line'] },
  ],
  3: [
    { label: 'Dashboard', to: { name: 'Login' }, icon: 'i-mdi-view-dashboard-outline' },
    { label: 'Mesas', to: { name: 'Login'}, icon: 'i-mdi-table-chair' },
    { label: 'Facturas', to: { name: 'Login' }, icon: 'i-mdi-receipt-text-outline' },
  ],
  2: [
    { label: 'Cola de Pedidos', to: { name: 'Login' }, icon: 'i-mdi-stove' },
    { label: 'Activos', to: { name: 'Login' }, icon: 'i-mdi-fire' },
  ],
  1: [
    { label: 'Dashboard', to: { name: 'Login' }, icon: iconsMap['mdi-view-dashboard-outline'] },
    { label: 'Mesas', to: { name: 'Login' }, icon: iconsMap['mdi-account-group-outline'] },
    { label: 'Pedidos', to: { name: 'Login' }, icon: iconsMap['mdi-table-chair'] }
  ],
  Client: [
    /* { label: 'Home', to: { name: 'ClientHome' }, icon: iconsMap['i-ic-baseline-home'] }, */
    { label: 'Menu', to: { name: 'ClientMenu' }, icon: iconsMap['i-raphael-coffee'] },
    { label: 'Orders', to: { name: 'ClientOrders' }, icon: iconsMap['i-material-symbols-orders-rounded'] }
  ],
  // ... lo mismo para los demás roles
};


/**
 * Gets the navigation links for a given user role.
 * @param {String} role The role of the user.
 * @returns {Array} An array of navigation link objects.
 */
export const getNavigationForRole = (role) => {
  return navigationConfig[role] || [];
};