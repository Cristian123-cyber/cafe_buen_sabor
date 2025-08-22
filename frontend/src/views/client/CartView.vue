<template>
    <div class="p-0">


        <div class="relative min-h-screen bg-gray-100">

            <!-- Header -->
            <header class="cart-header">
                <div class="mx-auto flex max-w-7xl items-center p-3 sm:p-4 justify-between">
                    <button @click="goBack" class="back-button">
                        <!-- Icono de unplugin-icons. Reemplaza el SVG original -->
                        <i-mdi-arrow-left class="w-5 h-5" />
                    </button>
                    <h1 class="flex-1 text-center text-lg font-bold tracking-tight text-text-light sm:text-xl">
                        Carrito de Compras
                    </h1>
                    <!-- Spacer to balance the back button and perfectly center the title -->
                    <div class="size-10 sm:size-12 shrink-0"></div>
                </div>
            </header>

            <main class="main-content">
                <div v-if="!cartStore.isEmpty" class="main-grid">
                    <section class="lg:col-span-7">
                        <ul role="list" class="space-y-2">
                            <CartItem v-for="item in cartStore.carrito" :key="item.product_id" :item="item"
                                @update-quantity="handleUpdateQuantity" @remove-item="handleRemoveItem" />
                        </ul>
                    </section>

                    <section class="summary-card-container">
                        <div class="summary-card">
                            <h2 class="summary-title">Resumen del Pedido</h2>

                            <div class="space-y-3">
                                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                    <p class="text-sm text-[#111418]">Subtotal</p>
                                    <p class="text-sm font-semibold text-[#111418]">{{
                                        formatCurrency(cartStore.subtotal) }}</p>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                    <p class="text-sm text-[#111418]">Propina sugerida</p>
                                    <p class="text-sm font-semibold text-[#111418]">{{ formatCurrency(suggestedTip) }}
                                    </p>
                                </div>
                                <div class="flex items-center justify-between pt-4">
                                    <p class="text-base font-bold text-[#111418]">Total</p>
                                    <p class="text-base font-bold text-[#0c7ff2]">{{ formatCurrency(total) }}</p>
                                </div>
                            </div>

                            <!-- CTA for Desktop - Inside the summary sidebar -->
                            <div class="mt-6 hidden lg:block">
                                <BaseButton @click="handlePlaceOrder" :loading="sendingOrder" class="checkout-button">
                                    <template #icon-left>

                                        <i-mdi-check-circle-outline class="w-5 h-5 mr-2" />

                                    </template>
                                    <span class="truncate">Realizar Pedido</span>
                                </BaseButton>

                            </div>
                        </div>
                    </section>
                </div>

                <div v-else class="empty-cart-container">
                    <div class="empty-cart-content">
                        <div class="flex items-center justify-center">

                            <i-mdi-cart-off class="text-gray-300 w-24 h-24" />
                        </div>
                        <h2 class="text-xl font-semibold text-gray-700 mt-4">Tu carrito está vacío</h2>
                        <p class="text-gray-500 mt-2">Parece que aún no has añadido productos.</p>
                        <button @click="goBack" class="mt-6 back-to-menu-button">
                            <i-mdi-arrow-left class="w-5 h-5 mr-2" />
                            <span>Volver al Menú</span>
                        </button>
                    </div>
                </div>

            </main>




        </div>
        <div v-if="!cartStore.isEmpty" class="mobile-cta-container">
            <BaseButton @click="handlePlaceOrder" :loading="sendingOrder" class="checkout-button">
                <template #icon-left>

                    <i-mdi-check-circle-outline class="w-5 h-5 mr-2" />

                </template>
                <span class="truncate">Realizar Pedido</span>
            </BaseButton>
        </div>


    </div>


</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useCartStore } from '../../stores/cartS';
import { useSessionStore } from '../../stores/clientSessionS';
import { useToasts } from '../../composables/useToast';
import { useOrderStore } from '../../stores/orderS';
import { useOrders } from '../../composables/useOrders';
import { useAlert } from '../../composables/useAlert';

const { addToast } = useToasts();

const { createOrder } = useOrders();

const sessionStore = useSessionStore();
const orderStore = useOrderStore();

const sendingOrder = computed(() => orderStore.isLoading);


const alert = useAlert()


// --- COMPOSABLES ---
const cartStore = useCartStore();
const router = useRouter();

// --- COMPUTED ---
/**
 * Calcula la propina sugerida (10% del subtotal).
 */
const suggestedTip = computed(() => cartStore.subtotal * 0.10);

/**
 * Calcula el total final sumando el subtotal y la propina.
 */
const total = computed(() => cartStore.subtotal + suggestedTip.value);

// --- METHODS ---
/**
 * Maneja la actualización de cantidad desde el componente hijo.
 * @param {number} productId - ID del producto a actualizar.
 * @param {number} newQuantity - Nueva cantidad.
 */
const handleUpdateQuantity = (productId, newQuantity) => {
    cartStore.updateQuantity(productId, newQuantity);
};

/**
 * Maneja la eliminación de un item desde el componente hijo.
 * @param {number} productId - ID del producto a eliminar.
 */
const handleRemoveItem = (productId) => {
    // Aquí podrías agregar una confirmación si lo deseas
    cartStore.removeFromCart(productId);
    // Feedback visual para el usuario
    addToast({
        title: `Producto eliminado.`,
        message: `Este producto ha sido removido de tu pedido.`,
        type: 'info',
        duration: 3000, // Dura más para dar tiempo a verlo
    });
};

/**
 * Lógica para procesar el pedido.
 * TODO: Implementar la llamada a la API para crear el pedido.
 */
const handlePlaceOrder = async () => {



    let productsData = [];

    cartStore.carrito.forEach((item) => {
        if (!item || typeof item !== 'object') return;

        const { product_id, quantity } = item;

        if (product_id != null && quantity != null) {
            productsData.push({ id_product: product_id, quantity: quantity });
        }
    });

    const sessionId = sessionStore.sessionInfo?.id;

    const dataPedido = { products: productsData, table_session_id: sessionId };
    console.log(dataPedido);

    try {



        const result = await createOrder(dataPedido);

        if (result.success) {





            await alert.show({
                title: 'Pedido enviado',
                message: `El ID de tu pedido es: #${result.data.id_order}. Ve a la seccion de ordenes para ver todos los pedidos de la mesa.`,
                variant: 'success',
            });

            cartStore.clearCart();

            router.replace({ name: 'ClientMenu' });
        } else {

            alert.show({
                title: 'Ha ocurrido un error',
                message: result.message ? result.message : 'Intentalo de nuevo o comunicate con el personal.',
                variant: 'error'
            });



        }

    } catch (error) {

        console.log(error);

        alert.show({
            title: 'Ha ocurrido un error',
            message: error.message ? error.message : 'Intentalo de nuevo o comunicate con el personal.',
            variant: 'error'
        });

    }



};

/**
 * Navega a la página anterior.
 */
const goBack = () => {
    router.back();
};

/**
 * Formatea un número como moneda local (ej. $1,234.56).
 * @param {number} value - El valor a formatear.
 */
const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
};
</script>

<style scoped>
@reference "../../style.css";

/* --- Header --- */
.cart-header {
    @apply sticky top-0 z-20 bg-primary shadow-sm;
}

.header-container {
    @apply mx-auto flex max-w-7xl items-center p-3 sm:p-4 justify-between;
}

.back-button {
    @apply flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-400 transition-colors duration-200;
}

.header-title {
    @apply flex-1 text-center text-lg font-bold tracking-tight text-text-light sm:text-xl;
}

.header-spacer {
    @apply size-10 sm:size-12 shrink-0;
}

/* --- Layout Principal --- */
.main-content {
    @apply mx-auto max-w-7xl px-3 sm:px-4 lg:px-8 py-6;
}

.main-grid {
    @apply lg:grid lg:grid-cols-12 lg:items-start lg:gap-x-12 xl:gap-x-16;
}

/* --- Resumen del Pedido --- */
.summary-card-container {
    @apply mt-8 rounded-xl bg-white shadow-sm lg:col-span-5 lg:mt-0 lg:sticky lg:top-24;
}

.summary-card {
    @apply p-4 sm:p-6 space-y-4;
}

.summary-title {
    @apply text-lg font-medium text-gray-900;
}

.summary-details {
    @apply space-y-3 text-sm text-[#111418];
}

.summary-row {
    @apply flex items-center justify-between py-2 border-b border-gray-100;
}

.summary-total-row {
    @apply flex items-center justify-between pt-4 text-base;
}

/* --- Carrito Vacío --- */
.empty-cart-container {
    @apply flex items-center justify-center py-16 lg:py-24;
}

.empty-cart-content {
    @apply text-center;
}

.back-to-menu-button {
    @apply inline-flex items-center justify-center rounded-lg bg-accent px-5 py-3 text-sm font-medium text-white hover:bg-accent-dark transition-colors shadow-sm;
}


/* --- Botones de Acción (Checkout) --- */
.checkout-button {
    @apply w-full cursor-pointer items-center justify-center overflow-hidden rounded-xl h-12 sm:h-14 px-5 bg-accent hover:bg-accent-dark text-white text-base font-bold leading-normal tracking-[0.015em] transition-all shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98] flex;
}

.mobile-cta-container {
    @apply sticky bottom-0 z-10 bg-white p-3 sm:p-4 lg:hidden shadow-lg;
}

.mobile-cta-container .checkout-button {
    @apply text-sm sm:text-base;
}
</style>