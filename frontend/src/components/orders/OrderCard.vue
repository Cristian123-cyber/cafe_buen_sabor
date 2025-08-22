<template>
  <div class="order-card">
    <div class="order-image-container">
      <div class="order-image" :style="backgroundImageStyle"></div>
    </div>

    <div class="order-content">
      <div class="order-info">
        <div class="order-header">
        

          <BaseBadge :color="statusInfo.badgeClass"> {{ statusInfo.label }}</BaseBadge>
          <span class="order-total">{{ formattedTotal }}</span>
        </div>

        <h3 class="order-id">Pedido #{{ order.id_order }}</h3>
        <p class="order-date">Realizado el {{ formattedDate }}</p>
      </div>

      <div class="additional-actions">
        <BaseButton @click="$emit('view-details', order)" size="sm" variant="accent">
          <span>Ver Pedido</span>

          <template #icon-right>
            <i-mdi-arrow-right class="w-4 h-4" />

          </template>
        </BaseButton>


        <!-- (IMPORTANTE) Aquí se renderizarán los botones adicionales -->
        <div v-if="$slots.actions" >

          <slot name="actions"></slot>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { ORDER_STATUS_LABELS } from '../../utils/constants';

// --- PROPS ---
const props = defineProps({
  order: {
    type: Object,
    required: true,
  },
});

// --- EMITS ---
defineEmits(['view-details']);

// --- COMPUTED ---

/**
 * Formatea la fecha del pedido a un formato legible.
 */
const formattedDate = computed(() => {
  if (!props.order.created_date) return 'Fecha no disponible';
  return new Date(props.order.created_date).toLocaleDateString('es-ES', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  });
});

/**
 * Formatea el monto total a moneda local (COP).
 */
const formattedTotal = computed(() => {
  const total = parseFloat(props.order.total_amount);
  if (isNaN(total)) return '$ 0';
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
  }).format(total);
});

/**
 * Determina el texto y la clase CSS para el distintivo de estado del pedido.
 */
const statusInfo = computed(() => {
  const status = props.order.order_status_name || 'UNKNOWN';
  const info = {
    label: ORDER_STATUS_LABELS[status] || 'Desconocido',
    badgeClass: '',
  };

  switch (status) {
    case 'PENDING':
      info.badgeClass = 'warning';
      break;
    case 'CONFIRMED':
      info.badgeClass = 'success';
      break;
    case 'READY':
      info.badgeClass = 'info';
      break;
    case 'COMPLETED':
      info.badgeClass = 'neutral';
      break;
    case 'CANCELLED':
      info.badgeClass = 'error';
      break;
    default:
      info.badgeClass = 'neutral';
  }
  return info;
});

/**
 * Genera un fondo para la tarjeta.
 * Usamos una imagen de placeholder más atractiva.
 */
const backgroundImageStyle = computed(() => ({
  'background-image': `url('${props.order.products[0]?.product_image_url}')`,
}));

</script>

<style scoped>
@reference '../../style.css';

/* --- Contenedor Principal --- */
.order-card {
  @apply flex bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm transition-all duration-300 ease-in-out hover:shadow-lg;
}

/* --- Imagen --- */
.order-image-container {
  @apply w-28 flex-shrink-0 sm:w-32;
}

.order-image {
  @apply w-full h-full bg-center bg-cover;
}

/* --- Contenido --- */
.order-content {
  @apply flex flex-1 flex-col justify-between p-4 sm:p-5;
}

.order-info {
  @apply flex flex-col;
}

.order-header {
  @apply flex justify-between items-center mb-2;
}

/* --- Elementos de Información --- */
.order-id {
  @apply text-lg font-bold text-gray-800 leading-tight mb-1;
  order: 1;
  /* Para reordenar visualmente */
}

.order-date {
  @apply text-sm text-gray-500 mb-4;
  order: 2;
  /* Para reordenar visualmente */
}

.order-total {
  @apply font-semibold text-gray-900 text-base;
}

.order-action {
  @apply mt-auto pt-2;
  /* Empuja el botón hacia abajo */
}

/* --- Botón de Acción --- */
.btn-view-details {
  @apply flex items-center justify-center gap-2 w-full bg-accent text-white text-sm px-4 py-2.5 rounded-lg font-semibold transition-all duration-200 ease-in-out hover:bg-accent-dark focus:outline-none hover:shadow-lg;
}

.btn-view-details i-mdi-arrow-right {
  @apply transition-transform duration-200 group-hover:translate-x-1;
}

/* --- Estado del Pedido --- */
.status-badge {
  @apply inline-flex items-center px-3 py-1 rounded-full text-xs font-bold leading-none;
}

.status-pending {
  @apply bg-amber-100 text-amber-800 ring-1 ring-inset ring-amber-200;
}

.status-confirmed {
  @apply bg-blue-100 text-blue-800 ring-1 ring-inset ring-blue-200;
}

.status-ready {
  @apply bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-200;
}

.status-delivered {
  @apply bg-gray-100 text-gray-700 ring-1 ring-inset ring-gray-200;
}

.status-cancelled {
  @apply bg-red-100 text-red-800 ring-1 ring-inset ring-red-200;
}

.status-unknown {
  @apply bg-gray-100 text-gray-600 ring-1 ring-inset ring-gray-200;
}

.order-action-container {
  @apply mt-auto pt-2 flex flex-col gap-2;
}
.additional-actions {
  @apply flex flex-col sm:flex-row items-stretch sm:items-center sm:justify-end gap-2 w-full;
}



</style>