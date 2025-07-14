<template>
  <div class="product-card">
    <!-- Contenedor de la grilla -->
    <div class="product-grid">

      <!-- Columna Izquierda: Imagen -->
      <section class="lg:col-span-7">
        <div class="product-image-container">
          <img :src="product.product_image_url || 'https://via.placeholder.com/600'" :alt="product.product_name"
            class="product-image" />
          <div v-if="product.tags && product.tags.length > 0" class="product-tag">
            {{ product.tags[0] }}
          </div>
        </div>
      </section>

      <!-- Columna Derecha: Detalles -->
      <section class="lg:col-span-5">
        <div class="product-details-container">
          <!-- Título y Categoría -->
          <div>
            <div class="flex items-center gap-2 mb-3">
              <span v-if="product.category_name" class="product-category-badge">
                {{ product.category_name }}
              </span>
            </div>
            <h2 class="product-title">{{ product.product_name }}</h2>
            <p class="product-description">{{ product.product_desc }}</p>
            <!-- Ingredientes (solo si es un producto preparado) -->
            <p v-if="product.product_types_id_type === 1 && product.ingredients?.length > 0" class="ingredients-text">
              <span class="font-semibold text-gray-800">Ingredientes:</span>
              {{ ingredientNames }}
            </p>
          </div>

          <!-- Sección de Precio -->
          <div>
            <div class="flex items-baseline gap-2 mb-6">
              <span class="product-price">{{ formattedPrice }}</span>
              <span class="product-price-unit">/ Cada uno</span>
            </div>
          </div>

          <!-- Sección de Cantidad -->
          <div>
            <h3 class="quantity-title">Cantidad</h3>
            <div class="flex items-center gap-4">
              <div class="quantity-control">
                <button class="quantity-btn" @click="decrementQuantity" :disabled="quantity <= 1">
                  <i-mdi-minus />
                </button>
                <span class="quantity-input">{{ quantity }}</span>
                <button class="quantity-btn" @click="incrementQuantity" :disabled="quantity >= 20">
                  <i-mdi-plus />
                </button>
              </div>
            </div>
          </div>

          <!-- CTA para Escritorio -->
          <div class="hidden lg:block pt-6">
            <div class="cta-container">
              <div class="total-display">
                <p class="total-label">Total:</p>
                <p class="total-amount">{{ formattedTotalPrice }}</p>
              </div>
              <button class="add-to-cart-btn" @click="$emit('addToCart')">
                <i-mdi-cart-plus />
                <span>Agregar al Pedido</span>
              </button>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

// Definimos las props que el componente espera recibir.
// La validación de tipo y `required` asegura la robustez del componente.
const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
  quantity: {
    type: Number,
    required: true,
  },
  totalPrice: {
    type: Number,
    required: true,
  }
});

// Definimos los eventos que este componente puede emitir hacia el padre.
// 'update:quantity' permite el uso de v-model:quantity en el componente padre.
const emit = defineEmits(['update:quantity', 'addToCart']);

// Formateadores para precios, usando la API de Internacionalización de JS.
const formatCurrency = (value) => {
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
  }).format(value);
};

const ingredientNames = computed(() => {


  return props.product.ingredients?.map(ing => ing.ingredient_name).join(', ');
 
});

const formattedPrice = computed(() => formatCurrency(props.product.product_price || 0));
const formattedTotalPrice = computed(() => formatCurrency(props.totalPrice));

// Funciones para manejar la cantidad.
// Emiten el evento 'update:quantity' para que el padre actualice el estado.
const incrementQuantity = () => {
  if (props.quantity < 20) { // Límite máximo
    emit('update:quantity', props.quantity + 1);
  }
};

const decrementQuantity = () => {
  if (props.quantity > 1) { // Límite mínimo
    emit('update:quantity', props.quantity - 1);
  }
};
</script>

<style scoped lang="css">
/* Obligatorio para que Tailwind v4 procese @apply en este componente */
@reference "../../style.css";

.product-card {
  @apply bg-white rounded-xl shadow-sm overflow-hidden;
}

.product-grid {
  @apply lg:grid lg:grid-cols-12 lg:items-start;
}

.product-image-container {
  @apply aspect-square relative;
}
.ingredients-text {
  @apply mt-4 text-sm text-gray-700 leading-relaxed;
}

.product-image {
  @apply w-full h-full object-cover;
}

.product-tag {
  @apply absolute top-4 right-4 bg-white/90 backdrop-blur-sm text-gray-700 px-3 py-1 rounded-full text-sm font-medium shadow-sm;
}

.product-details-container {
  @apply p-6 lg:p-8 space-y-6;
}

.product-category-badge {
  @apply bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm font-medium;
}

.product-title {
  @apply text-2xl lg:text-4xl font-bold text-gray-900 mb-3;
}

.product-description {
  @apply text-gray-600 leading-relaxed;
}

.product-price {
  @apply text-3xl font-bold text-gray-900;
}

.product-price-unit {
  @apply text-lg text-gray-500;
}

.quantity-title {
  @apply text-lg font-semibold text-gray-800 mb-3;
}

.quantity-control {
  @apply flex items-center border border-gray-300 rounded-lg overflow-hidden;
}

.quantity-btn {
  @apply w-12 h-12 flex items-center justify-center hover:bg-gray-100 text-gray-700 font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed;
}

.quantity-input {
  @apply w-16 h-12 text-center font-medium text-gray-800 flex items-center justify-center;
}

.cta-container {
  @apply flex flex-col sm:flex-row gap-4;
}

.total-display {
  @apply flex-1 text-left;
}

.total-label {
  @apply text-sm text-gray-600;
}

.total-amount {
  @apply text-2xl font-bold text-gray-900;
}

.add-to-cart-btn {
  @apply bg-accent hover:bg-accent-dark text-white px-8 py-3 rounded-xl font-medium transition-colors duration-200 flex items-center justify-center gap-2 flex-1;
}
</style>