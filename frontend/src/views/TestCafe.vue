<template>
  <div class="max-w-sm mx-auto bg-white rounded-xl shadow-lg overflow-hidden m-4">
    <!-- Header de la tarjeta -->
    <div class="bg-gradient-to-r from-amber-600 to-orange-600 p-4">
      <h2 class="text-white text-xl font-bold">☕ Café Especial</h2>
      <p class="text-amber-100 text-sm">Prueba de Tailwind CSS</p>
    </div>
    
    <!-- Contenido -->
    <div class="p-6">
      <h3 class="text-gray-900 text-lg font-semibold mb-2">{{ nombreCafe }}</h3>
      <p class="text-gray-600 text-sm mb-4">{{ descripcion }}</p>
      
      <!-- Precio -->
      <div class="flex items-center justify-between mb-4">
        <span class="text-2xl font-bold text-gray-900">${{ precio }}</span>
        <div class="flex items-center">
          <span class="text-yellow-400 text-sm mr-1">★</span>
          <span class="text-gray-600 text-sm">{{ rating }}/5</span>
        </div>
      </div>
      
      <!-- Botones -->
      <div class="flex space-x-3">
        <button 
          @click="agregarCarrito"
          class="flex-1 bg-amber-600 hover:bg-amber-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 transform hover:scale-105"
        >
          Agregar al carrito
        </button>
        <button 
          @click="toggleFavorito"
          :class="[
            'px-4 py-2 rounded-lg border-2 transition-all duration-200',
            esFavorito 
              ? 'bg-red-100 border-red-300 text-red-600 hover:bg-red-200' 
              : 'bg-gray-100 border-gray-300 text-gray-600 hover:bg-gray-200'
          ]"
        >
          {{ esFavorito ? '❤️' : '🤍' }}
        </button>
      </div>
      
      <!-- Indicadores de estado -->
      <div class="mt-4 flex flex-wrap gap-2">
        <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
          Disponible
        </span>
        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
          Café Premium
        </span>
        <span v-if="esOrganico" class="inline-block bg-emerald-100 text-emerald-800 text-xs px-2 py-1 rounded-full">
          Orgánico
        </span>
      </div>
    </div>
    
    <!-- Footer con animación -->
    <div class="bg-gray-50 px-6 py-3 border-t">
      <p class="text-xs text-gray-500 text-center">
        <span class="animate-pulse">🔥</span>
        {{ mensajeEstado }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

// Props del componente
const props = defineProps({
  nombreCafe: {
    type: String,
    default: 'Espresso Colombiano'
  },
  descripcion: {
    type: String,
    default: 'Un café intenso y aromático con notas de chocolate y caramelo. Perfecto para comenzar el día.'
  },
  precio: {
    type: Number,
    default: 4.50
  },
  rating: {
    type: Number,
    default: 4.8
  },
  esOrganico: {
    type: Boolean,
    default: true
  }
})

// Estado reactivo
const esFavorito = ref(false)
const enCarrito = ref(false)

// Computed properties
const mensajeEstado = computed(() => {
  if (enCarrito.value) return '¡Agregado al carrito!'
  return 'Haz clic para agregar al carrito'
})

// Métodos
const agregarCarrito = () => {
  enCarrito.value = !enCarrito.value
  console.log(`${props.nombreCafe} ${enCarrito.value ? 'agregado al' : 'removido del'} carrito`)
}

const toggleFavorito = () => {
  esFavorito.value = !esFavorito.value
  console.log(`${props.nombreCafe} ${esFavorito.value ? 'agregado a' : 'removido de'} favoritos`)
}
</script>