<template>
  <div v-if="order" class="order-detail-container">
    
    <div class="section-header">
      <div>
        <h3 class="order-id">Pedido #{{ order.id_order }}</h3>
        <p class="order-date">{{ formattedDate }}</p>
      </div>
      <span :class="statusInfo.badgeClass">{{ statusInfo.label }}</span>
    </div>

    <div class="section">
      <h4 class="section-title">Resumen de Productos</h4>
      <ul class="product-list">
        <li v-for="product in order.products" :key="product.products_id_product" class="product-item">
          <img :src="product.product_image_url || 'https://via.placeholder.com/150'" alt="Imagen del producto" class="product-image"/>
          <div class="product-info">
            <span class="product-name">{{ product.product_name }}</span>
            <span class="product-quantity">Cantidad: {{ product.quantity }}</span>
          </div>
          <span class="product-subtotal">{{ formatCurrency(product.product_price * product.quantity) }}</span>
        </li>
      </ul>
    </div>

    <div class="section">
      <h4 class="section-title">Resumen de Pago</h4>
      <div class="financial-summary">
        <div class="summary-item">
          <span>Subtotal</span>
          <span>{{ formatCurrency(calculatedSubtotal) }}</span>
        </div>
        <div class="summary-item text-lg font-bold text-gray-800 total-item">
          <span>Total</span>
          <span>{{ formattedTotal }}</span>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { computed } from 'vue';
import { ORDER_STATUS_LABELS } from '../../utils/constants'; // Reutilizamos las constantes

// --- PROPS ---
const props = defineProps({
  order: {
    type: Object,
    required: true,
    default: () => null // Default para evitar errores si se renderiza antes de tiempo
  },
});

// --- HELPERS ---
/**
 * Formateador de moneda reutilizable.
 */
const formatCurrency = (value) => {
  const amount = parseFloat(value);
  if (isNaN(amount)) return '$ 0';
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
  }).format(amount);
};

// --- COMPUTED ---

// Subtotal calculado a partir de los productos.
const calculatedSubtotal = computed(() => {
  if (!props.order?.products) return 0;
  return props.order.products.reduce((acc, product) => acc + (product.product_price * product.quantity), 0);
});

// Total final (usa el total del pedido como fuente de verdad)
const formattedTotal = computed(() => formatCurrency(props.order.total_amount));

// Fecha formateada
const formattedDate = computed(() => {
  if (!props.order?.created_date) return '';
  return new Date(props.order.created_date).toLocaleDateString('es-ES', {
    day: 'numeric', month: 'long', year: 'numeric', hour: 'numeric', minute: 'numeric'
  });
});

// Lógica para el distintivo de estado (reutilizada de OrderCard)
const statusInfo = computed(() => {
  const status = props.order?.order_status_name || 'UNKNOWN';
  const info = {
    label: ORDER_STATUS_LABELS[status] || 'Desconocido',
    badgeClass: 'status-badge ',
  };
  switch (status) {
    case 'PENDING': info.badgeClass += 'status-pending'; break;
    case 'CONFIRMED': info.badgeClass += 'status-confirmed'; break;
    case 'READY': info.badgeClass += 'status-ready'; break;
    case 'DELIVERED': info.badgeClass += 'status-delivered'; break;
    case 'CANCELLED': info.badgeClass += 'status-cancelled'; break;
    default: info.badgeClass += 'status-unknown';
  }
  return info;
});
</script>

<style scoped>
@reference "../../style.css";

.order-detail-container {
  @apply flex flex-col gap-6 p-1; /* Añadimos p-1 para evitar que los focus ring se corten */
}

.section {
  @apply border-t border-gray-200 pt-4;
}
.section-header {
    @apply flex justify-between items-start pb-4;
}
.section-title {
  @apply text-base font-semibold text-gray-700 mb-4;
}
.order-id {
  @apply text-xl font-bold text-gray-900;
}
.order-date {
  @apply text-sm text-gray-500;
}

/* Lista de productos */
.product-list {
  @apply flex flex-col gap-4;
}
.product-item {
  @apply flex items-center gap-4;
}
.product-image {
  @apply w-14 h-14 rounded-lg object-cover bg-gray-100 flex-shrink-0;
}
.product-info {
  @apply flex-1;
}
.product-name {
  @apply font-semibold text-gray-800;
}
.product-quantity {
  @apply text-sm text-gray-500 block;
}
.product-subtotal {
  @apply font-semibold text-gray-700;
}

/* Resumen financiero */
.financial-summary {
    @apply flex flex-col gap-2 text-gray-600;
}
.summary-item {
    @apply flex justify-between items-center text-sm;
}
.total-item {
    @apply border-t border-gray-200 pt-3 mt-2;
}


/* --- Estilos de Estado (Reutilizados) --- */
.status-badge {
  @apply inline-flex items-center px-3 py-1 rounded-full text-xs font-bold leading-none whitespace-nowrap;
}
.status-pending { @apply bg-amber-100 text-amber-800 ring-1 ring-inset ring-amber-200; }
.status-confirmed { @apply bg-blue-100 text-blue-800 ring-1 ring-inset ring-blue-200; }
.status-ready { @apply bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-200; }
.status-delivered { @apply bg-gray-100 text-gray-700 ring-1 ring-inset ring-gray-200; }
.status-cancelled { @apply bg-red-100 text-red-800 ring-1 ring-inset ring-red-200; }
.status-unknown { @apply bg-gray-100 text-gray-600 ring-1 ring-inset ring-gray-200; }
</style>