<script setup>
import { ref, onMounted, computed, watch, onUnmounted } from 'vue';
import { useOrderStore } from '../../stores/orderS';
import { useOrders } from '../../composables/useOrders';
import { storeToRefs } from 'pinia';
import { useAlert } from '../../composables/useAlert'; // Para confirmaciones


const orderStore = useOrderStore();
const { kitchenQueue, errors, isLoading } = storeToRefs(orderStore);
const { fetchOrdersForKitchen, handleUpdateStatus } = useOrders();

const alert = useAlert();

const searchTerm = ref('');


// Modal
const isModalOpen = ref(false);
const selectedOrder = ref(null);

// Computed: filtrar orders por searchTerm (campo id_order)
const filteredOrders = computed(() => {
  const term = (searchTerm.value || '').toString().trim();
  if (!term) return kitchenQueue.value || [];

  try {
    return (kitchenQueue.value || []).filter((o) => {
      const idStr = (o && o.id_order !== undefined && o.id_order !== null) ? o.id_order.toString() : '';
      return idStr.includes(term);
    });
  } catch (e) {
    // En caso de datos inesperados, devolver lista sin filtrar
    return kitchenQueue.value || [];
  }
});

watch([searchTerm], ([newSearch]) => {


  // Implementar filtros si es necesario
}, { immediate: true });



const openDetailsModal = (order) => {
  selectedOrder.value = order;
  isModalOpen.value = true;
};

const closeDetailsModal = () => {
  isModalOpen.value = false;
  setTimeout(() => { selectedOrder.value = null; }, 300);
};

const markAsReady = async (orderId) => {
  const isConfirmed = await alert.show({
    variant: 'warning',
    title: '¿Marcar como listo?',
    message: 'Este pedido será marcado como listo para servir. ¿Deseas continuar?',
    confirmButtonText: 'Sí, marcar listo',
    cancelButtonText: 'Cancelar',
  });

  if (isConfirmed) {
    await handleUpdateStatus(orderId, 'ready');
    await fetchOrdersForKitchen();
  }
};

onMounted(async () => {
  await fetchOrdersForKitchen();

  console.log("kitchenQueue en mounted:: ", kitchenQueue.value);
});

onUnmounted(() => {
  orderStore.setKitchenQueue([]);
});
</script>

<template>
  <AppLayout>
    <HeaderSection>

      <template #title>Dashboard de Cocina</template>
      <template #subtitle>Gestiona los pedidos en la cola de cocina</template>
    </HeaderSection>

    <ToolsBar v-model:searchTerm="searchTerm" placeholderSearch="Numero de orden..." :showCreate="false"
      :loading="isLoading" searchLabel="Buscar pedidos">
    </ToolsBar>

    <!-- Contenido Principal -->
    <div class="content-area">
      <!-- Skeleton Loading -->
      <div v-if="isLoading" class="orders-skeleton">
        <div v-for="n in 4" :key="n" class="skeleton-card">
          <div class="skeleton-wrapper">
            <div class="skeleton-image"></div>
            <div class="skeleton-content">
              <div class="skeleton-info">
                <div class="skeleton-line skeleton-line-lg"></div>
                <div class="skeleton-line skeleton-line-md"></div>
                <div class="skeleton-line skeleton-line-full"></div>
                <div class="skeleton-line skeleton-line-sm"></div>
              </div>
              <div class="skeleton-actions">
                <div class="skeleton-button"></div>
                <div class="skeleton-button"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Estado de Error -->
      <div v-else-if="errors.fetchKitchenQueue" class="state-container">
        <i-mdi-alert-circle-outline class="w-8 h-8 text-red-500" />
        <p class="mt-3 text-red-700">¡Ups! Hubo un problema</p>
        <p class="text-sm text-gray-500">{{ errors.fetchKitchenQueue }}</p>
      </div>

      <!-- Estado Vacío -->
      <div v-else-if="!filteredOrders || filteredOrders.length === 0" class="state-container">
        <i-mdi-food-off-outline class="w-8 h-8 text-gray-400" />
        <p class="mt-3 text-gray-600">No hay pedidos confirmados.</p>
      </div>

      <!-- Lista de Pedidos -->
      <div v-else class="orders-grid">
        <div v-for="order in filteredOrders" :key="order.id_order" class="order-card-container">
          <div class="order-card-inner">
            <OrderCard :order="order" @view-details="openDetailsModal" class="order-card-component">
              <!-- Slot de acciones para CADA tarjeta -->
              <template #actions>
                <div class="order-actions-wrapper">
                  <div v-if="order.order_status_name === 'CONFIRMED'" class="pending-actions">
                    <BaseButton variant="success" size="sm" @click.stop="markAsReady(order.id_order)">
                      <template #icon-left>
                        <i-mdi-check-circle class="w-4 h-4" />
                      </template>
                      <span class="hidden sm:inline">Marcar Listo</span>
                      <span class="sm:hidden">Listo</span>
                    </BaseButton>
                  </div>


                </div>
              </template>
            </OrderCard>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal para Detalles -->
    <BaseModal v-model="isModalOpen" :title="`Detalle del Pedido #${selectedOrder?.id_order}`" max-width="lg">
      <OrderDetails v-if="selectedOrder" :order="selectedOrder" />
      <template #footer>
        <BaseButton variant="secondary" @click="closeDetailsModal">Cerrar</BaseButton>
      </template>
    </BaseModal>
  </AppLayout>
</template>

<style scoped>
/* IMPORTANTE: Referencia al archivo de tema central para que @apply funcione. */
@reference "../../style.css";

.content-area {
  @apply mt-6;
}

.state-container {
  @apply flex flex-col items-center justify-center text-center py-16 bg-gray-50/50 rounded-lg;
}

/* Grid principal de órdenes */
.orders-grid {
  @apply grid grid-cols-1 lg:grid-cols-2 gap-4;
}

/* Contenedor de cada card */
.order-card-container {
  @apply w-full;
}

.order-card-inner {
  @apply h-full flex flex-col;
}

.order-card-component {
  @apply flex-1 h-full;
}

/* Skeleton loading */
.orders-skeleton {
  @apply grid grid-cols-1 lg:grid-cols-2 gap-4;
}

.skeleton-card {
  @apply animate-pulse;
}

.skeleton-wrapper {
  @apply bg-white rounded-xl border border-gray-200 p-6 flex gap-4 min-h-[180px] shadow-sm;
}

.skeleton-image {
  @apply w-20 h-20 lg:w-24 lg:h-24 rounded-lg bg-gray-200 flex-shrink-0;
}

.skeleton-content {
  @apply flex-1 flex flex-col justify-between;
}

.skeleton-info {
  @apply space-y-3 flex-1;
}

.skeleton-line {
  @apply bg-gray-200 rounded h-3;
}

.skeleton-line-lg {
  @apply w-3/4 h-4;
}

.skeleton-line-md {
  @apply w-1/2;
}

.skeleton-line-full {
  @apply w-full;
}

.skeleton-line-sm {
  @apply w-2/3;
}

.skeleton-actions {
  @apply flex gap-2 mt-4;
}

.skeleton-button {
  @apply flex-1 h-9 bg-gray-300 rounded-md;
}

/* Acciones de las cards */
.order-actions-wrapper {
  @apply w-full;
}

.pending-actions {
  @apply flex gap-2 w-full;
}

.status-display {
  @apply flex justify-center w-full;
}

.status-badge {
  @apply inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium transition-all;
}

.status-ready {
  @apply bg-emerald-50 text-emerald-700 border border-emerald-200;
}
</style>