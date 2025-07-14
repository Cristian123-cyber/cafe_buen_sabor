<!-- src/components/tables/DisplayQRCard.vue -->
<template>
  <div class="qr-display-container">
    <!-- QR Code Principal con efectos -->
     <!-- QR Code Principal con efectos -->
    <div class="qr-main-wrapper">
      <div class="qr-glow-effect"></div>
      <div class="qr-code-container">
        <div class="qr-code-wrapper">
          <qrcode-vue 
            :value="qrURL" 
            :size="qrSize" 
            level="H" 
            background="#FFFFFF" 
            foreground="#212121" 
            class="qr-code" 
          />
        </div>
      </div>
    </div>

    <!-- Temporizador elegante -->
    <div class="timer-section">
      <div class="timer-header">
        <p>URL del QR: {{ qrURL }}</p>
        <i-mdi-timer-outline class="w-5 h-5 text-accent" />
        <span class="timer-label">Se actualiza en</span>
        <span class="timer-value">{{ formattedTimeLeft }}</span>
      </div>
      <div class="progress-container">
        <div class="progress-track">
          <div class="progress-bar-fill" :style="{ width: `${percentageLeft}%` }"></div>
        </div>
      </div>
    </div>

    <!-- Instrucciones paso a paso -->
    <div class="instructions-flow">
      <div class="instruction-item">
        <div class="step-circle">
          <i-mdi-camera-outline class="w-5 h-5" />
        </div>
        <div class="step-text">Abre la cámara</div>
      </div>

      <div class="instruction-item">
        <div class="step-circle">
          <i-mdi-scan-helper class="w-5 h-5" />
        </div>
        <div class="step-text">Escanea el código</div>
      </div>

      <div class="instruction-item">
        <div class="step-circle">
          <i-mdi-food-fork-drink class="w-5 h-5" />
        </div>
        <div class="step-text">Explora el menú</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import QrcodeVue from 'qrcode.vue';

const props = defineProps({
  qr_token: {
    type: String,
    required: true
  },
  tableId: {
    type: [String, Number],
    required: true
  },
  refreshInterval: {
    type: Number,
    default: 10 * 60 * 1000 // 10 minutos
  }
});

// URL del QR
const qrURL = computed(() => `http://localhost:5173/session/validate?token=${props.qr_token}&table=${props.tableId}`);
const qrSize = computed(() => {
  if (typeof window !== 'undefined') {
    const width = window.innerWidth;
    const height = window.innerHeight;


    // Tamaños optimizados para todos los dispositivos
    if (width < 400) return 220;                 // Móviles pequeños
    if (width < 768) return Math.min(280, height * 0.6);  // Móviles grandes
    if (width < 1024) return Math.min(400, height * 0.7); // Tablets
    return Math.min(400, height * 0.6);          // Desktop
  }
  return 300; // Valor por defecto para SSR
});

// Lógica del temporizador
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
      timeLeft.value = props.refreshInterval;
    }
  }, 1000);
};

onMounted(startTimer);
onUnmounted(() => clearInterval(timerInterval));

watch(() => props.qr_token, startTimer);
</script>

<style scoped>
@reference "../../style.css";

.qr-display-container {
  @apply w-full max-w-lg mx-auto flex flex-col items-center gap-6 sm:gap-8 px-4 sm:px-6;
  box-sizing: border-box;
}


/* QR Code Principal - Ajustes responsive */
.qr-main-wrapper {
  @apply relative flex items-center justify-center w-full px-2 sm:px-0;
  box-sizing: border-box;
  max-width: 100%;
}

.qr-glow-effect {
  @apply absolute rounded-2xl blur-xl opacity-30;
  background: radial-gradient(circle, #FFD75C 0%, rgba(203, 161, 53, 0) 70%);
  animation: glow-pulse 3s ease-in-out infinite alternate;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
}
.qr-code-container {
  @apply relative bg-surface rounded-2xl;
  border: 2px solid #CBA135;
  box-shadow:
    0 10px 25px -5px rgba(203, 161, 53, 0.3),
    0 20px 40px -10px rgba(203, 161, 53, 0.1),
    inset 0 1px 0 rgba(255, 255, 255, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.25rem;
  width: fit-content;
  max-width: 95vw; /* Limita el ancho máximo */
  margin: 0 auto;
  box-sizing: border-box;
}

.qr-code-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
}

.qr-code {
  @apply rounded-lg;
  /* Asegurar que el QR esté perfectamente centrado */
  display: block !important;
  margin: 0 auto;
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
}

/* Resetear estilos del componente QR si es necesario */
.qr-code canvas,
.qr-code svg {
  display: block !important;
  margin: 0 auto !important;
}

/* Timer Section */
.timer-section {
  @apply w-full max-w-xs;
}

.timer-header {
  @apply flex items-center justify-center gap-2 mb-3 text-sm font-medium text-text-light;
}

.timer-label {
  @apply text-text-muted;
}

.timer-value {
  @apply font-mono text-accent font-bold text-lg;
}

.progress-container {
  @apply w-full;
}

.progress-track {
  @apply w-full h-2 bg-primary-light rounded-full overflow-hidden;
  box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.2);
}

.progress-bar-fill {
  @apply h-full rounded-full transition-all duration-1000 ease-linear;
  background: linear-gradient(90deg, #CBA135, #FFD75C);
  box-shadow:
    0 0 10px rgba(203, 161, 53, 0.5),
    inset 0 1px 0 rgba(255, 255, 255, 0.3);
}

/* Instrucciones */
.instructions-flow {
  @apply w-full grid grid-cols-3 gap-4 sm:gap-6;
}

.instruction-item {
  @apply flex flex-col items-center text-center gap-3 p-3 sm:p-4;
  transition: all 0.3s ease;
}

.instruction-item:hover {
  @apply transform -translate-y-1;
}

.step-circle {
  @apply w-12 h-12 sm:w-14 sm:h-14 bg-accent text-primary rounded-full flex items-center justify-center;
  @apply shadow-lg hover:shadow-xl transition-all duration-300;
  box-shadow:
    0 8px 25px rgba(203, 161, 53, 0.3),
    0 0 0 0 rgba(203, 161, 53, 0.4);
  animation: float 3s ease-in-out infinite;
}

.step-circle:hover {
  @apply bg-accent-light transform scale-110;
  box-shadow:
    0 12px 35px rgba(203, 161, 53, 0.4),
    0 0 20px rgba(203, 161, 53, 0.6);
}

.step-text {
  @apply text-text-light text-sm sm:text-base font-medium;
}

/* Animaciones mejoradas */
@keyframes glow-pulse {
  0% {
    opacity: 0.2;
    transform: scale(0.95);
  }
  100% {
    opacity: 0.4;
    transform: scale(1.05);
  }
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-8px);
  }
}

.step-circle:nth-child(1) {
  animation-delay: 0s;
}

.step-circle:nth-child(2) {
  animation-delay: 0.5s;
}

.step-circle:nth-child(3) {
  animation-delay: 1s;
}

/* Media queries específicas para tablets */
@media (min-width: 768px) and (max-width: 1024px) {
  .qr-display-container {
    @apply max-w-xl;
  }
  
  .qr-code-container {
    padding: 1.75rem;
  }
  
  .qr-glow-effect {
    width: 105%;
    height: 105%;
    top: -2.5%;
    left: -2.5%;
  }
}
/* Ajustes para desktop */
@media (min-width: 1024px) {
  .qr-display-container {
    @apply max-w-2xl;
  }
  
  .qr-code-container {
    padding: 2rem;
  }
}


/* Ajustes para landscape móvil */
@media (max-height: 600px) and (orientation: landscape) {
  .qr-main-wrapper {
    @apply px-2;
  }
  
  .qr-code-container {
    padding: 0.5rem;
  }
}
</style>