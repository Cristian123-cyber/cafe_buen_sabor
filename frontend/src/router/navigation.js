
import { iconsMap } from '../iconSafeList.js';

export const navigationConfig = {
  5: [
    { label: 'Dashboard', to: { name: 'AdminDashboard' }, icon: iconsMap['mdi-view-dashboard-outline'] },
    { label: 'Usuarios', to: { name: 'AdminUsers' }, icon: iconsMap['mdi-account-group-outline'] },
    { label: 'Mesas', to: { name: 'AdminTables' }, icon: iconsMap['mdi-table-chair'] },
    { label: 'Productos', to: { name: 'AdminProducts' }, icon: iconsMap['mdi-food-outline'] },
    { label: 'Ingredientes', to: { name: 'AdminIngredients' }, icon: iconsMap['mdi-food-apple'] },
    { label: 'Ordenes', to: { name: 'AdminOrders' }, icon: iconsMap['i-material-symbols-orders-rounded'] },
    { label: 'Ventas', to: { name: 'AdminSales' }, icon: iconsMap['mdi-food-apple'] },
    { label: 'Sesiones', to: { name: 'AdminSessions' }, icon: iconsMap['mdi-food-apple'] },
    { label: 'Reportes', to: { name: 'AdminReports' }, icon: iconsMap['mdi-chart-line'] },
  ],
  3: [
    { label: 'Mesas', to: { name: 'CashierTables'}, icon: 'i-mdi-table-chair' },
    { label: 'Ventas', to: { name: 'CashierSales' }, icon: 'i-mdi-receipt-text-outline' },
  ],
  2: [
    { label: 'Cola de Pedidos', to: { name: 'KitchenDashboard' }, icon: iconsMap['mdi-stove'] },
  ],
  1: [
    { label: 'Mesas', to: { name: 'WaiterTables' }, icon: iconsMap['mdi-table-chair'] },
    { label: 'Ordenes', to: { name: 'WaiterOrders' }, icon: iconsMap['i-material-symbols-orders-rounded'] }
  ],
  Client: [
    { label: 'Home', to: { name: 'ClientHome' }, icon: iconsMap['i-ic-baseline-home'] },
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