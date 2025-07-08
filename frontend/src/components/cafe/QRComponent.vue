<!-- src/components/tables/DisplayQRCard.vue -->
<template>
  <div class="qr-card-container">
    <!-- Contenedor principal con borde dorado y sombra profunda -->
    <div class="qr-card">
      
      <!-- QR Code con animación de pulso -->
      <div class="qr-code-wrapper qr-pulse">
        <qrcode-vue 
          :value="qrURL" 
          :size="250"
          level="H"
          background="#FFFFFF"
          foreground="#000000"
          class="rounded-lg"
        />
      </div>
      
      <!-- Temporizador visual rediseñado -->
      <div class="timer-container">
        <div class="flex justify-between items-center mb-1 text-sm">
          <span class="text-text-muted">Actualización en:</span>
          <span class="font-mono font-semibold text-accent">{{ formattedTimeLeft }}</span>
        </div>
        <div class="progress-bar">
          <div 
            class="progress-fill"
            :style="{ width: `${percentageLeft}%` }"
          ></div>
        </div>
      </div>
      
      <!-- Sección de Instrucciones con Iconos -->
      <div class="instructions-container">
        <div class="instruction-step">
          <i-mdi-camera-outline class="w-7 h-7" />
          <span>Abre tu cámara</span>
        </div>
        <i-mdi-arrow-right-thin class="w-6 h-6 text-text-muted mx-2" />
        <div class="instruction-step">
          <i-mdi-scan-helper class="w-7 h-7" />
          <span>Apunta al código</span>
        </div>
        <i-mdi-arrow-right-thin class="w-6 h-6 text-text-muted mx-2" />
        <div class="instruction-step">
          <i-mdi-food-fork-drink class="w-7 h-7" />
          <span>¡Pide y disfruta!</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import QrcodeVue from 'qrcode.vue';

// NOTA: Se ha eliminado la prop 'tableId' para evitar redundancia.
// El tamaño del QR ahora se maneja internamente para mejor responsive.
const props = defineProps({
  qr_token: {
    type: String,
    required: true
  },
  tableId: { // Se mantiene para construir la URL, pero no se muestra
    type: [String, Number],
    required: true
  },
  refreshInterval: {
    type: Number,
    default: 10 * 60 * 1000 // 10 minutos
  }
});

// La URL se construye aquí, pero podría venir completa desde el backend también.
const qrURL = computed(() => `https://cafebuensabor.app/menu?token=${props.qr_token}`);

// --- Lógica del Temporizador (sin cambios) ---
const timeLeft = ref(props.refreshInterval);
const percentageLeft = computed(() => (timeLeft.value / props.refreshInterval) * 100);
const formattedTimeLeft = computed(() => {
  const minutes = Math.floor(timeLeft.value / 60000);
  const seconds = Math.floor((timeLeft.value % 60000) / 1000);
  return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
});

let timerInterval = null;

const startTimer = () => {
  clearInterval(timerInterval);
  timeLeft.value = props.refreshInterval;
  
  timerInterval = setInterval(() => {
    timeLeft.value -= 1000;
    
    if (timeLeft.value <= 0) {
      timeLeft.value = props.refreshInterval; // Reinicia para el siguiente ciclo
    }
  }, 1000);
};

onMounted(startTimer);
onUnmounted(() => clearInterval(timerInterval));

// Reinicia el temporizador si el token cambia (por el polling de la vista padre)
watch(() => props.qr_token, startTimer);
</script>

<style scoped>
@reference "../../style.css";

.qr-card-container {
  @apply w-full max-w-sm p-2;
}

.qr-card {
  @apply bg-primary rounded-2xl p-6 shadow-2xl border border-accent/20 flex flex-col items-center gap-6;
  transition: all 0.3s ease;
}

.qr-code-wrapper {
  @apply p-3 bg-white rounded-lg shadow-lg;
  /* La animación se define abajo */
}

/* Animación de pulso para el QR */
@keyframes pulse {
  0%, 100% {
    box-shadow: 0 0 0 0px #CBA135;
  }
  50% {
    box-shadow: 0 0 10px 8px rgba(255, 215, 92, 0); /* Usamos un rgba transparente para el final del glow */
  }
}

.qr-pulse {
  animation: pulse 2.5s infinite;
  border: 4px solid #CBA135;
}

.timer-container {
  @apply w-full;
}

.progress-bar {
  @apply bg-primary-light rounded-full h-2 w-full overflow-hidden;
}

.progress-fill {
  @apply bg-accent h-full rounded-full;
  transition: width 1s linear;
}

.instructions-container {
  @apply w-full flex items-center justify-center p-3 rounded-lg bg-primary-light text-text-light;
}

.instruction-step {
  @apply flex flex-col items-center text-center text-xs gap-1;
}
</style>