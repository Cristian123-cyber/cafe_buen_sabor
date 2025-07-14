<template>
    <div class="page-container">
        <!-- Header Fijo -->
        <header class="page-header">
            <div class="header-content">
                <button @click="goBack" class="back-button">
                    <!-- Icono de unplugin-icons. Reemplaza el SVG original -->
                    <i-mdi-arrow-left class="w-5 h-5" />
                </button>
                <h1 class="page-title">Detalle del Producto</h1>
                <!-- Espaciador para centrar el título -->
                <div class="w-10"></div>
            </div>
        </header>

        <!-- Contenido Principal -->
        <main class="main-content">
            <!-- Estado de Carga -->
            <div v-if="isLoading" class="flex justify-center items-center py-20 text-gray-300 text-8xl">
                
                <i-svg-spinners-270-ring> </i-svg-spinners-270-ring>


            </div>

            <!-- Estado de Error -->
            <div v-else-if="error" class="text-center py-20 text-red-500">
                <p>Error al cargar el producto: {{ error.message }}</p>

            </div>

            <!-- Contenido del Producto -->
            <ProductDetailCard v-else-if="product" :product="product" :total-price="totalPrice"
                v-model:quantity="quantity" @add-to-cart="handleAddToCart" />

            <!-- Producto No Encontrado -->
            <div v-else class="text-center py-20">
                <p>Producto no encontrado.</p>
            </div>
        </main>

        <!-- Footer Pegajoso para Móviles (se oculta en pantallas grandes) -->
        <footer v-if="product" class="mobile-footer">
            <div class="cta-container">
                <div class="total-display-mobile">
                    <p class="total-label">Total:</p>
                    <p class="total-amount-mobile">{{ formattedTotalPrice }}</p>
                </div>
                <button class="add-to-cart-btn-mobile" @click="handleAddToCart">
                    <i-mdi-cart-plus />
                    <span>Agregar al Pedido</span>
                </button>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
// Asumiendo la existencia de estas stores según tu arquitectura

import { useProductStore } from '../../stores/productS';
import { useCartStore } from '../../stores/cartS';
// import { useCartStore } from '../../stores/cart';
import { useToasts } from '../../composables/useToast'; // Importamos el composable
const { addToast } = useToasts(); // Lo instanciamos aquí









// --- Fin de los Mocks ---

// Hooks de Vue y Vue Router
const route = useRoute();
const router = useRouter();

// Stores de Pinia
const productsStore = useProductStore();
const cartStore = useCartStore();

// Estado local del componente
const quantity = ref(1);
const productId = ref(route.params.id);

// Datos computados del store
const product = ref(null);
const isLoading = computed(() => productsStore.isLoading);
const error = computed(() => productsStore.error);

// Lógica de cálculo
const totalPrice = computed(() => {
    if (product.value) {
        return product.value.product_price * quantity.value;
    }
    return 0;
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0
    }).format(value);
};

const formattedTotalPrice = computed(() => formatCurrency(totalPrice.value));


// Acciones del componente
const handleAddToCart = () => {
    if (!product.value) return;

    const itemToAdd = {
        product_id: product.value.id_product,
        name: product.value.product_name,
        price: product.value.product_price,
        quantity: quantity.value,
        product_image_url: product.value.product_image_url
    };

    cartStore.addToCart(itemToAdd);

    // Feedback visual para el usuario
    addToast({
        title: `Producto agregado.`,
        message: `Entra a tu carrito para finalizar la compra.`,
        type: 'info',
        duration: 2000, // Dura más para dar tiempo a verlo
    });

    // Opcional: Redirigir al menú o al carrito después de añadir
    //router.push({ name: 'ClientMenu', replace: true } );
};

const goBack = () => {
    router.back();
};



// Ciclo de vida: Cargar el producto cuando el componente se monta
onMounted(async () => {

    try {
        product.value = await productsStore.getProductById(productId.value);
        console.log('producto seteado: ', product.value);


    } catch (error) {

        console.log(error);


    }

});

</script>

<style scoped lang="css">
@reference "../../style.css";

.page-container {
    /* Usamos un color de fondo neutro del tema global */
    @apply bg-surface min-h-screen;
}

.page-header {
    @apply sticky top-0 z-50 bg-primary shadow-sm;
}

.header-content {
    @apply mx-auto flex max-w-7xl items-center p-4 lg:p-6;
}

.back-button {
    @apply flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-400 transition-colors duration-200;
}

.page-title {
    @apply text-lg lg:text-xl font-semibold text-text-light flex-1 text-center;
}

.main-content {
    @apply mx-auto max-w-7xl px-3 sm:px-4 lg:px-8 py-6 lg:py-8;
}

.mobile-footer {
    /* Fondo con transparencia y blur para un efecto moderno */
    @apply sticky bottom-0 z-40 bg-white/80 backdrop-blur-sm border-t border-gray-200 p-4 lg:hidden;
}

.cta-container {
    /* Reutilizamos esta clase para el footer */
    @apply flex flex-col sm:flex-row gap-4 max-w-md mx-auto;
}

.total-display-mobile {
    @apply flex-1 text-center sm:text-left;
}

.total-amount-mobile {
    @apply text-xl font-bold text-gray-900;
}

.add-to-cart-btn-mobile {
    @apply bg-accent hover:bg-accent-light text-white px-8 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2;
}

/* Reutilizamos la clase del label de total */
.total-label {
    @apply text-sm text-gray-600;
}
</style>