
import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useLoadingStore = defineStore('loading', () => {
  // --- STATE ---
  // Un contador para saber cuántas peticiones están activas.
  const activeRequests = ref(0);

  // --- GETTERS ---
  // Un booleano computado que nos dice si la app está "cargando".
  // Es mucho más limpio que comprobar `activeRequests > 0` en los componentes.
  const isLoading = computed(() => activeRequests.value > 0);

  // --- ACTIONS ---
  /**
   * Se llama cuando una petición a la API comienza.
   */
  function requestStarted() {
    activeRequests.value++;
  }

  /**
   * Se llama cuando una petición a la API termina (éxito o error).
   */
  function requestFinished() {
    // Nos aseguramos de que el contador nunca sea negativo.
    if (activeRequests.value > 0) {
      activeRequests.value--;
    }
  }

  return { 
    isLoading, 
    requestStarted, 
    requestFinished 
  };
});