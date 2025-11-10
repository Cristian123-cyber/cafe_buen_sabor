<!-- src/views/waiter/TableOrdersView.vue -->
<script setup>
import { onMounted, onUnmounted, ref, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { storeToRefs } from 'pinia';
import { useOrderStore } from '../../stores/orderS';
import { useOrders } from '../../composables/useOrders';
import { useAlert } from '../../composables/useAlert'; // Para confirmaciones
import { useSalesStore } from '../../stores/salesS';
import { useAuthStore } from '../../stores/authS';
import { useInvoiceGenerator } from '../../utils/InvoicesGenerator.js';
import { tablesService } from '../../services/tables.js';
import { toTypedSchema } from '@vee-validate/zod';

import { useTablesStore } from '../../stores/tablesStore.js';
import * as z from 'zod';


const orderStore = useOrderStore();
const tablesStore = useTablesStore();
const salesStore = useSalesStore();
const authStore = useAuthStore();

// Configurar información de la empresa (opcional)
const { download, open, getBlob } = useInvoiceGenerator({
    name: 'Cafe Buen Sabor',
    address: 'Calle 123 #45-67, Cartago Valle del Cauca, Colombia',
    phone: '+57 300 123 4567',
    email: 'ventas@cafebuensabor.com',
    taxId: 'NIT: 900.123.456-7'
});


const { ordersForCurrentTable, errors, isLoading } = storeToRefs(orderStore);
const { fetchOrdersByTableId, handleUpdateStatus } = useOrders();

const alert = useAlert();
const router = useRouter();
const route = useRoute();

const tableId = ref(route.params.id);
const tableNumber = ref(route.query.table_number);
const isBulkConfirmLoading = ref(false);
const isBulkCancelLoading = ref(false);



// --- MODAL ---
const isModalOpen = ref(false);
const selectedOrder = ref(null);

const isModalPaymentOpen = ref(false);

const paymentSchema = toTypedSchema(
    z.object({
        payment_method: z.string().min(1, 'Seleccione un método de pago'),


    })
);


const formData = ref({
    payment_method: 'CONTADO',
    orders: [],
    cashier_id: null
});

// --- COMPUTED ---
/**
 * Filtra y devuelve solo los pedidos que no estan completados.
 * @returns {Array}
 */
const pendingOrders = computed(() => {
    return ordersForCurrentTable.value.filter(
        (order) => order.order_status_name === 'READY'
    );
});

const formRef = ref(null);

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
    router.push({ name: 'CashierTables' });
};

// Nueva función para liberar mesa
const handleCloseTable = async () => {
    const isConfirmed = await alert.show({
        variant: 'warning',
        title: '¿Liberar mesa?',
        message: 'Esta acción cerrará la sesión de la mesa. ¿Deseas continuar?',
        confirmButtonText: 'Sí, liberar',
        cancelButtonText: 'Cancelar',
    });

    if (isConfirmed) {
        try {
            // Aquí iría la lógica para liberar la mesa
            const result = await tablesService.update(tableId.value, { table_status: 'FREE' });

            console.log(result);

            // Por ahora solo mostramos un mensaje de éxito
            alert.show({
                title: 'Mesa liberada',
                message: 'La mesa ha sido liberada exitosamente',
                variant: 'success',
            });

            tablesStore.fetchTables(false);
            router.push({
                name: 'CashierTables',
            });

        } catch (error) {
            alert.show({
                title: 'Error',
                message: 'No se pudo liberar la mesa',
                variant: 'error',
            });
        }
    }
};

const openFormPayment = (orders) => {
    isModalPaymentOpen.value = true;

    formData.value.orders = orders;

    formData.value.cashier_id = authStore.user.id;

    formData.value.payment_method = 'CONTADO';


}



// --- ACCIONES INDIVIDUALES ---
const closeOrder = async (dataPay) => {




    const isConfirmed = await alert.show({
        variant: 'warning',
        title: '¿Desea facturar este pedido?',
        message: 'Este pedido será facturado. ¿Deseas continuar?',
        confirmButtonText: 'Sí, confirmar',
        cancelButtonText: 'Cancelar',
    });

    if (isConfirmed) {

        const data = formData.value

        data.payment_method = dataPay.payment_method;



        try {
            const dataFactura = await salesStore.createSale(data);

            alert.show({
                title: 'Éxito',
                message: 'Pedido facturado con éxito',
                variant: 'success',
            });

           

            open(dataFactura.data);

            isModalPaymentOpen.value = false;





        } catch (error) {

            alert.show({
                title: "Error al facturar orden",
                message: "No se pudieron facturar las ordenes.",
                variant: "error",
            });



        }

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
const closeAllOrders = async () => {
    const isConfirmed = await alert.show({
        variant: 'warning',
        title: '¿Facturar todos los pedidos listos?',
        message: 'Esta acción facturara todos los pedidos listos. ¿Deseas continuar?',
        confirmButtonText: 'Sí, Facturar',
        cancelButtonText: 'Cancelar',
    });
    if (isConfirmed) {

        isBulkConfirmLoading.value = true;
        const ids = pendingOrders.value.map(o => o.id_order);

        const data = {
            'orders': ids,
            'cashier_id': authStore.user.id,
            'payment_method': 'CONTADO'
        }


        try {
            console.log(data);
            const dataFactura = await salesStore.createSale(data);

            alert.show({
                title: 'Ordenes facturadas',
                message: 'Las órdenes se han facturado con éxito',
                type: 'success'
            });


            download(dataFactura.data);


        } catch (error) {
            alert.show({
                title: "Error al facturar orden",
                message: "No se pudieron facturar las ordenes.",
                variant: "error",
            });
        }

        isBulkConfirmLoading.value = false;



    }
};



// --- CICLO DE VIDA ---
onMounted(async () => {
    if (tableId.value) {
        await fetchOrdersByTableId(tableId.value);
        console.log("ordersForCurrentTable en mounted:: ", ordersForCurrentTable.value);
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
                    <h1 class="header-title">Mesa #{{ tableNumber }}</h1>
                    <p class="header-subtitle">Gestión de pedidos</p>
                </div>

                <div class="header-actions">
                    <BaseButton variant="secondary" @click="handleCloseTable" class="release-table-btn">
                        <template #icon-left>
                            <i-mdi-lock-open-outline class="w-4 h-4" />
                        </template>
                        Liberar mesa
                    </BaseButton>

                    <div class="header-status" v-if="ordersForCurrentTable && ordersForCurrentTable.length > 0">
                        <span class="orders-count">{{ ordersForCurrentTable.length }}</span>
                        <span class="orders-label">pedidos totales</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Barra de Acciones Masivas -->
        <div v-if="pendingOrders.length > 0" class="improved-bulk-actions">
            <div class="bulk-content">
                <div class="bulk-info">
                    <div class="pending-indicator"></div>
                    <div class="bulk-text">
                        <p class="bulk-subtitle">Tienes <span class="pending-count">{{ pendingOrders.length }}</span> {{
                            pendingOrders.length > 1 ? 'pedidos' : 'pedido' }}
                            {{ pendingOrders.length > 1 ? 'pendientes' : 'pendiente' }} para facturar </p>
                    </div>
                </div>
                <div class="bulk-buttons">
                    <BaseButton @click="openFormPayment(pendingOrders.map(o => o.id_order))"
                        :loading="isBulkConfirmLoading" variant="success" size="sm">
                        <template #icon-left>
                            <i-mdi-check-all class="w-4 h-4" />
                        </template>
                        <span>Facturar Mesa</span>
                    </BaseButton>

                </div>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="content-area">
            <div v-if="isLoading" class="orders-skeleton">
                <div v-for="n in 4" :key="n" class="skeleton-card">
                    <!-- Card simulada -->
                    <div class="skeleton-wrapper">
                        <!-- Imagen simulada -->
                        <div class="skeleton-image"></div>

                        <!-- Contenido (texto + botón falso) -->
                        <div class="skeleton-content">
                            <!-- Bloque de info completa (status, id, fecha, etc.) -->
                            <div class="skeleton-info">
                                <div class="skeleton-line skeleton-line-lg"></div>
                                <div class="skeleton-line skeleton-line-md"></div>
                                <div class="skeleton-line skeleton-line-full"></div>
                                <div class="skeleton-line skeleton-line-sm"></div>
                            </div>

                            <!-- Botones simulados -->
                            <div class="skeleton-actions">
                                <div class="skeleton-button"></div>
                                <div class="skeleton-button"></div>
                            </div>
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
            <div v-else class="orders-grid">
                <div v-for="order in ordersForCurrentTable" :key="order.id_order" class="order-card-container">
                    <div class="order-card-inner">
                        <OrderCard :order="order" @view-details="openDetailsModal" class="order-card-component">
                            <!-- Slot de acciones para CADA tarjeta -->
                            <template #actions>
                                <div class="order-actions-wrapper">
                                    <div v-if="order.order_status_name === 'READY' || order.order_status_name === 'PENDING'"
                                        class="pending-actions">
                                        <BaseButton v-if="order.order_status_name === 'READY'" variant="success"
                                            size="sm" @click.stop="openFormPayment([order.id_order])">
                                            <template #icon-left>
                                                <i-line-md-circle-filled-to-confirm-circle-filled-transition
                                                    class="w-4 h-4" />
                                            </template>
                                            <span class="hidden sm:inline">Facturar orden</span>
                                            <span class="sm:hidden">Facturar orden</span>
                                        </BaseButton>
                                        <BaseButton variant="danger" size="sm"
                                            @click.stop="cancelOrder(order.id_order)">
                                            <template #icon-left>
                                                <i-material-symbols-cancel-rounded class="w-4 h-4" />
                                            </template>
                                            <span class="hidden sm:inline">Cancelar</span>
                                            <span class="sm:hidden">X</span>
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
        <BaseModal v-model="isModalPaymentOpen" title="Metodo de pago" max-width="lg">
            <BaseForm ref="formRef" :initial-values="formData" :validation-schema="paymentSchema" @submit="closeOrder">

                <template #default>



                    <BaseFormRow>
                        <BaseSelect name="payment_method" label="Metodo de pago" :options="[{
                            value: 'CONTADO', label: 'Contado'
                        }, {
                            value: 'TRANSFERENCIA', label: 'Transferencia'
                        }]" option-label="label" option-value="value" placeholder="Selecciona un metodo de pago">
                        </BaseSelect>
                    </BaseFormRow>

                </template>
            </BaseForm>


            <template #footer>
                <BaseButton variant="secondary" @click="isModalPaymentOpen = false">Cerrar</BaseButton>
                <BaseButton variant="accent" @click="formRef.submit()">Confirmar</BaseButton>
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

.header-actions {
    @apply flex items-center gap-4;
}

.release-table-btn {
    @apply flex items-center gap-2 transition-all duration-200;
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
    @apply text-lg font-semibold text-gray-700;
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

/* Acciones de las cards */
.order-actions-wrapper {
    @apply w-full;
}

.pending-actions {
    @apply flex gap-2 w-full;
}

.action-btn {
    @apply flex-1 justify-center transition-all duration-200 hover:scale-[1.02] font-medium;
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

.status-completed {
    @apply bg-blue-50 text-blue-700 border border-blue-200;
}
</style>