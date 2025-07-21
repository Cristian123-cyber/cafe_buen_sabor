// src/iconsMap.js
import IconDashboard from "virtual:icons/mdi/view-dashboard-outline";
import IconUsers from "virtual:icons/mdi/account-group-outline";
import IconTables from "virtual:icons/mdi/table-chair";
import IconProducts from "virtual:icons/mdi/food-outline";
import IconReports from "virtual:icons/mdi/chart-line";
import IconOrders from "virtual:icons/mdi/clipboard-list-outline";
import IconStove from "virtual:icons/mdi/stove";
import IconFire from "virtual:icons/mdi/fire";
import IconInvoices from "virtual:icons/mdi/receipt-text-outline";
import IconLogout from "virtual:icons/mdi/logout";
import IconClose from "virtual:icons/mdi/close";
import IconCoffee from "virtual:icons/mdi/coffee";
import IconIngredient from "virtual:icons/mdi/food-apple";

import IconOrderReady from "virtual:icons/mdi/silverware-fork-knife";
import IconOrderConfirmed from "virtual:icons/mdi/check-circle-outline";
import IconOrderCancelled from "virtual:icons/mdi/cancel";
import IconDefault from "virtual:icons/mdi/bell-outline";

import IconMenu from "virtual:icons/ph/squares-four";
import IconOrderStatus from "virtual:icons/ph/list-bullets-bold";
import IconHomeClient from "virtual:icons/ic/baseline-home";
import IconMenuClient from "virtual:icons/raphael/coffee";
import IconOrdersClient from "virtual:icons/material-symbols/orders-rounded";

export const iconsMap = {
  "mdi-view-dashboard-outline": IconDashboard,
  "mdi-account-group-outline": IconUsers,
  "mdi-table-chair": IconTables,
  "mdi-food-outline": IconProducts,
  "mdi-chart-line": IconReports,
  "mdi-clipboard-list-outline": IconOrders,
  "mdi-stove": IconStove,
  "mdi-fire": IconFire,
  "mdi-receipt-text-outline": IconInvoices,
  "mdi-logout": IconLogout,
  "mdi-close": IconClose,
  "mdi-coffee": IconCoffee,
  "ph-squares-four": IconMenu,
  "ph-list-bullets-bold": IconOrderStatus,
  "i-ic-baseline-home": IconHomeClient,
  "i-raphael-coffee": IconMenuClient,
  "i-material-symbols-orders-rounded": IconOrdersClient,
  "mdi-food-apple": IconIngredient,
};

export const notificationIcons = {
  OrderReady: IconOrderReady,
  OrderConfirmed: IconOrderConfirmed,
  OrderCancelled: IconOrderCancelled,
  Default: IconDefault,
};
