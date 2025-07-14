<script setup>
import { computed, defineEmits, defineProps } from 'vue';

// 1. DEFINICIÓN DE PROPS CON VALIDACIÓN TIPO TYPESCRIPT
// Se espera un objeto `product` con una estructura definida.
// Esto mejora la predictibilidad y previene errores.
const props = defineProps({
  product: {
    type: Object,
    required: true,
    // Validador para asegurar que el objeto tiene las propiedades mínimas
    validator: (p) => {
      return p && typeof p.id_product !== 'undefined' && p.product_name && p.product_price && p.product_image_url;
    }
  }
});

// 2. DEFINICIÓN DE EMITS
// Se define un evento `view-details` que enviará el ID del producto
// al componente padre cuando el usuario haga clic en la tarjeta.
const emit = defineEmits(['view-details']);

// 3. COMPUTED PROPERTY PARA FORMATEAR EL PRECIO
// Mantenemos la lógica de presentación fuera del template.
// Usamos la API de Internacionalización para un formato de moneda correcto.
// Se asume COP (Pesos Colombianos) por el contexto.
const formattedPrice = computed(() => {
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    maximumFractionDigits: 0, // No mostrar centavos para COP
  }).format(props.product.product_price);
});

// 4. MANEJADOR DE EVENTOS
// Esta función emite el evento al componente padre con el ID del producto.
const handleCardClick = () => {
  emit('view-details', props.product.id_product);
};
</script>

<template>
  <div class="product-card group" @click="handleCardClick">
    <img
      class="product-card-image"
      :src="product.product_image_url"
    ></img>

    <div class="product-card-info">

      <p class="product-card-name">{{ product.product_name }}</p>
      <p class="product-card-price">{{ formattedPrice }}</p>
    </div>
  </div>
</template>

<style scoped>
/*
  (IMPORTANTE)
  Se importa el contexto del tema de Tailwind CSS v4.
  Esto es obligatorio para usar @apply con nuestras variables CSS personalizadas.
  La ruta debe ser relativa desde la ubicación de este componente.
*/
@reference "../../style.css";

/*
  Se crean clases semánticas usando @apply para mantener el HTML limpio.
  Estas clases utilizan las variables definidas en tu archivo `app.css`.
*/
.product-card {
  @apply flex flex-col gap-3 cursor-pointer transition-shadow duration-300;
}


.product-card-image {
  @apply w-full object-cover rounded-xl bg-gray-100 group-hover:scale-105 transition-transform duration-300;
  aspect-ratio: 3 / 4;
}

.product-card-info {
  @apply px-1 text-left;
}

.product-card-name {
  @apply text-text font-medium leading-normal line-clamp-2;
  @apply text-sm sm:text-base;
}

.product-card-price {
  @apply text-gray-500 leading-normal; /* Usando color de texto secundario */
  @apply text-xs sm:text-sm;
}
</style>