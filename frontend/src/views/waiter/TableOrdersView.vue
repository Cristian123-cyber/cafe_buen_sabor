<!-- src/views/waiter/TableOrdersView.vue -->
<script setup>
import { onMounted, onUnmounted, ref, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { storeToRefs } from 'pinia';
import { useOrderStore } from '../../stores/orderS';
import { useOrders } from '../../composables/useOrders';
import { useAlert } from '../../composables/useAlert'; // Para confirmaciones

const orderStore = useOrderStore();
const { ordersForCurrentTable, errors, isLoading } = storeToRefs(orderStore);
const { fetchOrdersByTableId, handleUpdateStatus, handleBulkUpdateStatus } = useOrders();
const alert = useAlert();
const router = useRouter();
const route = useRoute();

const tableId = ref(route.params.id);
const isBulkConfirmLoading = ref(false);
const isBulkCancelLoading = ref(false);



// --- MODAL ---
const isModalOpen = ref(false);
const selectedOrder = ref(null);

// --- COMPUTED ---
/**
 * Filtra y devuelve solo los pedidos que están pendientes de confirmación.
 * @returns {Array}
 */
const pendingOrders = computed(() => {
  return ordersForCurrentTable.value.filter(
    (order) => order.order_status_name === 'PENDING'
  );
});

// --- MÉTODOS ---

const openDetailsModal = (order) => {
  selectedOrder.value = order;
  isModalOpen.value = true;
};
const closeDetailsModal = () => {
  isModalOpen.value = false;
  setTimeout(() => { selectedOrder.value = null; }, 300);
};

const goBack = () => {
  router.push({ name: 'WaiterTables' });
};

// --- ACCIONES INDIVIDUALES ---
const confirmOrder = async (orderId) => {
  const isConfirmed = await alert.show({
    variant: 'warning',
    title: '¿Desea confirmar este pedido?',
    message: 'Este pedido será marcado como confirmado. ¿Deseas continuar?',
    confirmButtonText: 'Sí, confirmar',
    cancelButtonText: 'Cancelar',
  });
  if (isConfirmed) {
    handleUpdateStatus(orderId, 'confirm', tableId.value);
  }

};

const cancelOrder = async (orderId) => {
   const isConfirmed = await alert.show({
    variant: 'warning',
    title: '¿Desea cancelar este pedido?',
    message: 'Este pedido será cancelado y no podrá ser recuperado. ¿Deseas continuar?',
    confirmButtonText: 'Sí, cancelar',
    cancelButtonText: 'Cancelar',
  });
  

  if (isConfirmed) {
    handleUpdateStatus(orderId, 'cancel', tableId.value);
  }
};

// --- ACCIONES MASIVAS ---
const confirmAllPending = async () => {
  const isConfirmed = await alert.show({
    variant: 'warning',
    title: '¿Cancelar todos los pedidos pendientes?',
    message: 'Esta acción cancelará todos los pedidos pendientes. ¿Deseas continuar?',
    confirmButtonText: 'Sí, cancelar',
    cancelButtonText: 'Cancelar',
  });
  if (isConfirmed){

    isBulkConfirmLoading.value = true;
    const ids = pendingOrders.value.map(o => o.id_order);
    await handleBulkUpdateStatus(ids, 'confirm-all', tableId.value);
    isBulkConfirmLoading.value = false;
  }
};

const cancelAllPending = async () => {
  const isConfirmed = await alert.show({
    variant: 'warning',
    title: '¿Cancelar todos los pedidos pendientes?',
    message: 'Esta acción cancelará todos los pedidos pendientes. ¿Deseas continuar?',
    confirmButtonText: 'Sí, cancelar',
    cancelButtonText: 'Cancelar',
  });

  if (isConfirmed) {
    isBulkCancelLoading.value = true;
    const ids = pendingOrders.value.map(o => o.id_order);
    await handleBulkUpdateStatus(ids, 'cancel-all', tableId.value);
    isBulkCancelLoading.value = false;
  }
};

// --- CICLO DE VIDA ---
onMounted(async () => {
  if (tableId.value) {
    await fetchOrdersByTableId(tableId.value);
  } else {
    errors.value.fetchOrders = "ID de mesa no proporcionado.";
  }
});

onUnmounted(() => {
  orderStore.setOrdersForCurrentTable([]);
});
</script>

<template>
  <div class="page-container">
    <!-- Encabezado y Navegación -->
    <header class="improved-header">
      <div class="header-content">
        <BaseButton variant="ghost" size="icon" @click="goBack" aria-label="Volver a Mesas" class="back-btn">
          <i-mdi-arrow-left class="w-6 h-6" />
        </BaseButton>
        <div class="header-info">
          <h1 class="header-title">Mesa #{{ tableId }}</h1>
          <p class="header-subtitle">Gestión de pedidos</p>
        </div>
        <div class="header-status" v-if="ordersForCurrentTable && ordersForCurrentTable.length > 0">
          <span class="orders-count">{{ ordersForCurrentTable.length }}</span>
          <span class="orders-label">pedidos totales</span>
        </div>
      </div>
    </header>

    <!-- Barra de Acciones Masivas -->
    <div v-if="pendingOrders.length > 0" class="improved-bulk-actions">
      <div class="bulk-content">
        <div class="bulk-info">
          <div class="pending-indicator"></div>
          <div class="bulk-text">
            <p class="bulk-title">Acciones pendientes</p>
            <p class="bulk-subtitle">Tienes <span class="pending-count">{{ pendingOrders.length }}</span> {{
              pendingOrders.length > 1 ? 'pedidos' : 'pedido' }}
              {{ pendingOrders.length > 1 ? 'pendientes' : 'pendiente' }} de confirmación</p>
          </div>
        </div>
        <div class="bulk-buttons">
          <BaseButton @click="confirmAllPending" :loading="isBulkConfirmLoading" variant="success" size="sm">
            <template #icon-left>
              <i-mdi-check-all class="w-4 h-4" />
            </template>
            <span>Confirmar todo</span>
          </BaseButton>
          <BaseButton @click="cancelAllPending" :loading="isBulkCancelLoading" variant="danger" size="sm">
            <template #icon-left>
              <i-material-symbols-cancel-rounded class="w-4 h-4" />
            </template>
            <span>Cancelar todo</span>
          </BaseButton>
        </div>
      </div>
    </div>

    <!-- Contenido Principal -->
    <div class="content-area">
      <div v-if="isLoading" class="orders-skeleton flex flex-col gap-3">
        <div v-for="n in 4" :key="n" class="animate-pulse">
          <!-- Card simulada -->
          <div class="bg-gray-100 rounded-xl border border-gray-200 p-4 flex gap-4 items-start relative">

            <!-- Imagen simulada -->
            <div class="w-24 h-24 rounded-lg bg-gray-300 flex-shrink-0"></div>

            <!-- Contenido (texto + botón falso) -->
            <div class="flex-1 flex flex-col justify-between h-full">
              <!-- Bloque de info completa (status, id, fecha, etc.) -->
              <div class="w-full h-20 bg-gray-200 rounded-md mb-3"></div>

              <!-- Botón simulado ocupando el resto -->
              <div class="w-full h-10 bg-gray-300 rounded-md"></div>
            </div>
          </div>
        </div>
      </div>




      <!-- Estado de Error -->
      <div v-else-if="errors.fetchOrders" class="state-container">
        <i-mdi-alert-circle-outline class="w-8 h-8 text-red-500" />
        <p class="mt-3 text-red-700">¡Ups! Hubo un problema</p>
        <p class="text-sm text-gray-500">{{ error }}</p>
      </div>

      <!-- Estado Vacío -->
      <div v-else-if="!ordersForCurrentTable || ordersForCurrentTable.length === 0" class="state-container">
        <i-mdi-food-off-outline class="w-8 h-8 text-gray-400" />
        <p class="mt-3 text-gray-600">Aún no se han realizado pedidos para esta mesa.</p>
      </div>

      <!-- Lista de Pedidos -->
      <div v-else class="orders-list">
        <OrderCard v-for="order in ordersForCurrentTable" :key="order.id_order" :order="order"
          @view-details="openDetailsModal">
          <!-- Slot de acciones para CADA tarjeta -->
          <template #actions>
            <div v-if="order.order_status_name !== 'READY' && order.order_status_name !== 'COMPLETED'" class="individual-actions">
              <BaseButton variant="success" size="sm" @click.stop="confirmOrder(order.id_order)">

                <template #icon-left>
                  <i-line-md-circle-filled-to-confirm-circle-filled-transition class="w-4 h-4" />
                </template>
                <span>Confirmar</span>
              </BaseButton>
              <BaseButton variant="danger" size="sm" @click.stop="cancelOrder(order.id_order)">
                <template #icon-left>
                  <i-material-symbols-cancel-rounded class="w-4 h-4" />
                </template>
                <span>Cancelar</span>
              </BaseButton>
            </div>
          </template>
        </OrderCard>
      </div>
    </div>

    <!-- Modal para Detalles -->
    <BaseModal v-model="isModalOpen" :title="`Detalle del Pedido #${selectedOrder?.id_order}`" max-width="lg">
      <OrderDetails v-if="selectedOrder" :order="selectedOrder" />
      <template #footer>
        <BaseButton variant="secondary" @click="closeDetailsModal">Cerrar</BaseButton>
      </template>
    </BaseModal>
  </div>
</template>

<style scoped>
@reference "../../style.css";

.page-container {
  @apply flex flex-col gap-6;
}

.improved-header {
  @apply bg-white border-b border-gray-100 px-8 py-6 rounded-xl shadow-sm;
}

.header-content {
  @apply flex items-center justify-between;
}

.header-info {
  @apply flex-1 px-4;
}

.header-title {
  @apply text-xl font-semibold text-gray-900;
}

.header-subtitle {
  @apply text-sm text-gray-500;
}

.header-status {
  @apply flex flex-col items-center justify-center;
}

.orders-count {
  @apply text-lg font-bold text-blue-600;
}

.orders-label {
  @apply text-xs text-gray-500;
}

.back-btn {
  @apply text-gray-500 hover:text-gray-700 transition-colors;
}

.content-area {
  @apply mt-2;
}

.state-container {
  @apply flex flex-col items-center justify-center text-center py-16 bg-gray-50/50 rounded-lg;
}

.orders-list {
  @apply flex flex-col gap-4;
}

.orders-skeleton {
  @apply flex flex-col gap-4;
}

/* Estilos mejorados para la barra de acciones masivas */
.improved-bulk-actions {
  @apply bg-white rounded-xl shadow-sm p-4 py-3;
}

.bulk-content {
  @apply flex flex-col sm:flex-row items-center justify-between px-5 py-4;
}

.bulk-info {
  @apply flex items-center gap-3 mb-3 sm:mb-0;
}

.pending-indicator {
  @apply w-3 h-3 rounded-full bg-blue-500 animate-pulse mr-3;
}

.bulk-text {
  @apply flex flex-col;
}

.bulk-title {
  @apply text-lg font-medium text-gray-700;
}

.bulk-subtitle {
  @apply text-sm text-gray-500 mt-1;
}

.pending-count {
  @apply font-semibold text-gray-700;
}

.bulk-buttons {
  @apply flex gap-2;
}

.bulk-btn {
  @apply rounded-lg transition-all duration-200;
}

.bulk-btn:hover {
  @apply transform transition-transform duration-200 -translate-y-px;
}

.confirm-btn {
  @apply bg-emerald-50 text-emerald-700 border border-emerald-100 hover:bg-emerald-100 hover:shadow-sm;
}

.cancel-btn {
  @apply bg-rose-50 text-rose-700 border border-rose-100 hover:bg-rose-100 hover:shadow-sm;
}


.individual-actions {
  @apply grid grid-cols-2 gap-2 w-full;
}
</style>