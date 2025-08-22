<template>
    <ClientLayout title="Mis Pedidos">
        <!-- Contenedor principal de la vista -->
        <div class="page-container">

            <!-- Estado de Carga -->
            <!-- Estado de Carga -->
            <div v-if="orderStore.isLoading" class="orders-skeleton flex flex-col gap-3">
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
            <div v-else-if="error" class="state-container">
                <i-mdi-alert-circle-outline class="w-8 h-8 text-red-500" />
                <p class="mt-3 text-red-700">¡Ups! Hubo un problema</p>
                <p class="text-sm text-gray-500">{{ error }}</p>
            </div>

            <!-- Estado Vacío -->
            <div v-else-if="!orders || orders.length === 0" class="state-container">
                <i-mdi-food-off-outline class="w-8 h-8 text-gray-400" />
                <p class="mt-3 text-gray-600">Aún no has realizado ningún pedido.</p>
                <p class="text-sm text-gray-500">¡Explora nuestro menú para empezar!</p>
            </div>

            <!-- Lista de Pedidos -->
            <div v-else class="orders-list">
                <OrderCard v-for="order in orders" :key="order.id_order" :order="order"
                    @view-details="openDetailsModal" />
            </div>

        </div>

        <!-- Modal para los Detalles del Pedido -->
        <!-- Asumo que tienes un componente BaseModal global -->
        <BaseModal v-model="isModalOpen" title="Detalle de pedido" max-width="lg">

            <OrderDetails v-if="selectedOrder" :order="selectedOrder" />

            <template #footer>
                <BaseButton variant="secondary" @click="closeDetailsModal">
                    Cerrar
                </BaseButton>
            </template>
        </BaseModal>


    </ClientLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';


// --- Importaciones de Lógica (Stores y Composables) ---
import { useOrderStore } from '../../stores/orderS';
import { useSessionStore } from '../../stores/clientSessionS';
import { useOrders } from '../../composables/useOrders';

// --- Estado Local para la UI ---
const isModalOpen = ref(false);
const selectedOrder = ref(null);

// --- Instancias de Stores y Composables ---
const orderStore = useOrderStore();
const sessionStore = useSessionStore();
// Asumo que tu composable `useOrders` expone su propio estado de carga y error,
// lo cual es una excelente práctica para encapsular la lógica.
const { fetchOrdersCurrentSession } = useOrders();

// --- Datos Computados ---
// La fuente de verdad para los pedidos es el store de Pinia.
const orders = computed(() => orderStore.ordersCurrentSession);

const error = computed(() => orderStore.errors.fecthOrdersCurrentSession);

// --- Métodos de Interacción ---
/**
 * Abre el modal y establece el pedido seleccionado para mostrar sus detalles.
 * @param {object} order - El objeto del pedido emitido por OrderCard.
 */
const openDetailsModal = (order) => {

    selectedOrder.value = order;
    isModalOpen.value = true;
};

/**
 * Cierra el modal y limpia el estado del pedido seleccionado.
 */
const closeDetailsModal = () => {
    isModalOpen.value = false;
    // Pequeña demora para que la transición de cierre del modal sea suave
    // antes de limpiar los datos, evitando un parpadeo del contenido.
    setTimeout(() => {
        selectedOrder.value = null;
    }, 300);
};

// --- Hook de Ciclo de Vida ---
onMounted(async () => {
    const sessionId = sessionStore.sessionInfo?.id;

    // Realizamos la petición solo si tenemos una sesión de cliente válida.
    if (sessionId) {
        await fetchOrdersCurrentSession(sessionId);
    } else {
        // Si no hay sesión, es un caso de error que el composable debería manejar.
        // El 'error' reactivo se actualizará y la UI mostrará el mensaje de error.
        console.error("No se pudo obtener los pedidos: ID de sesión del cliente no encontrado.");
    }
});
</script>

<style scoped>
/* Referencia al archivo de tema central para que @apply funcione */
@reference "../../style.css";

.page-container {
    @apply w-full mx-auto px-4 py-6;
}

/* Contenedor para los mensajes de estado (Carga, Error, Vacío) */
.state-container {
    @apply flex flex-col items-center justify-center text-center py-16 bg-gray-50/50 rounded-lg;
}

/* Lista vertical minimalista para las tarjetas de pedido */
.orders-list {
    @apply flex flex-col gap-3;
}

.orders-skeleton {
    @apply w-full;
}
</style>