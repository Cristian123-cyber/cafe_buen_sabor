<template>
  <li class="cart-item-card">
    <div
      class="item-image"
      :style="{ backgroundImage: `url(${item.product_image_url || 'https://via.placeholder.com/150'})` }"
    ></div>

    <div class="item-info">
      <p class="item-name">{{ item.name }}</p>
      
      <div class="quantity-controls">
        <button @click="decreaseQuantity" class="quantity-button">
          <i-mdi-minus class="w-5 h-5" />
        </button>
        <span class="quantity-text">{{ item.quantity }}</span>
        <button @click="increaseQuantity" class="quantity-button">
          <i-mdi-plus class="w-5 h-5" />
        </button>
      </div>
    </div>

    <div class="item-price-section">
      <p class="item-price">{{ formatCurrency(itemTotal) }}</p>
      <button @click="removeItem" class="remove-button" aria-label="Eliminar producto">
        <i-mdi-trash-can-outline class="w-5 h-5" />
      </button>
    </div>
  </li>
</template>

<script setup>
import { computed } from 'vue';

// --- PROPS ---
const props = defineProps({
  /**
   * Objeto que contiene los datos del item del carrito.
   * Espera: { product_id, name, price, quantity, image_url }
   */
  item: {
    type: Object,
    required: true,
  },
});

// --- EMITS ---
const emit = defineEmits(['updateQuantity', 'removeItem']);

// --- COMPUTED ---
/**
 * Calcula el precio total para este item (precio * cantidad).
 */
const itemTotal = computed(() => {
  return props.item.price * props.item.quantity;
});

// --- METHODS ---
/**
 * Emite un evento para incrementar la cantidad del producto.
 */
const increaseQuantity = () => {
  emit('updateQuantity', props.item.product_id, props.item.quantity + 1);
};

/**
 * Emite un evento para decrementar la cantidad.
 * No permite bajar de 1. Si se quiere eliminar, se usa el botón de eliminar.
 */
const decreaseQuantity = () => {
  if (props.item.quantity > 1) {
    emit('updateQuantity', props.item.product_id, props.item.quantity - 1);
  }
};

/**
 * Emite un evento para eliminar el producto del carrito.
 */
const removeItem = () => {
  emit('removeItem', props.item.product_id);
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
/* Referenciamos el archivo de tema para poder usar @apply con nuestras variables personalizadas si fuera necesario */
@reference "../../style.css";

.cart-item-card {
  @apply flex items-center gap-3 sm:gap-4 bg-white rounded-xl shadow-sm p-4 hover:shadow-md transition-shadow;
}

.item-image {
  @apply bg-center bg-no-repeat aspect-square bg-cover rounded-lg size-14 sm:size-16 flex-shrink-0;
}

.item-info {
  @apply flex-1 min-w-0;
}

.item-name {
  @apply text-[#111418] text-sm sm:text-base font-medium leading-normal truncate;
}

.quantity-controls {
  @apply flex items-center gap-2 mt-1;
}

.quantity-button {
  @apply flex items-center justify-center size-7 rounded-md bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors;
}

.quantity-text {
  @apply text-sm font-semibold text-gray-800 w-6 text-center;
}

.item-price-section {
    @apply flex-shrink-0 flex flex-col items-end justify-between self-stretch;
}

.item-price {
  @apply text-[#111418] text-sm sm:text-base font-semibold;
}

.remove-button {
    @apply text-gray-400 hover:text-red-500 transition-colors mt-auto;
}
</style>