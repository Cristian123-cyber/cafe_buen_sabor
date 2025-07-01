<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';

const props = defineProps({
  id: { type: Number, required: true },
  message: { type: String, required: true },
  title: { type: String, default: '' },
  type: {
    type: String,
    default: 'info',
    validator: (value) => ['success', 'error', 'warning', 'info'].includes(value),
  },
  duration: { type: Number, default: 5000 },
});

const emit = defineEmits(['close']);

// --- Lógica del Temporizador ---
const timer = ref(null);
const startTime = ref(0);
const remainingTime = ref(props.duration);

// --- ESTADO VISUAL ---
const progress = ref(100);
const isPaused = ref(false);

const pauseTimer = () => {
  if (timer.value) {
    isPaused.value = true;
    clearTimeout(timer.value);
    timer.value = null;
    const elapsedTime = Date.now() - startTime.value;
    remainingTime.value -= elapsedTime;
  }
};

const resumeTimer = () => {
  if (remainingTime.value > 0) {
    isPaused.value = false;
    startTime.value = Date.now();
    timer.value = setTimeout(() => {
      close();
    }, remainingTime.value);
  }
};

const close = () => {
  clearTimeout(timer.value);
  timer.value = null;
  emit('close', props.id);
};

onMounted(() => {
  resumeTimer();
});

onUnmounted(() => {
  clearTimeout(timer.value);
});

// --- Configuración Visual Dinámica ---
const iconMap = {
  success: 'i-mdi-check-circle-outline',
  error: 'i-mdi-alert-circle-outline',
  warning: 'i-mdi-alert-outline',
  info: 'i-mdi-information-outline',
};
const iconComponent = computed(() => iconMap[props.type]);
const toastClass = computed(() => `toast--${props.type}`);
</script>

<template>
  <div
    class="toast-card"
    :class="toastClass"
    role="alert"
    aria-live="assertive"
    aria-atomic="true"
    @mouseenter="pauseTimer"
    @mouseleave="resumeTimer"
  >
    <!-- Icono con fondo circular -->
    <div class="toast-icon-container">
      <div class="toast-icon-background">
        <i-mdi-check-circle-outline v-if="props.type === 'success'" class="toast-icon" />
        <i-mdi-alert-circle-outline v-if="props.type === 'error'" class="toast-icon" />
        <i-mdi-alert-outline v-if="props.type === 'warning'" class="toast-icon" />
        <i-mdi-information-outline v-if="props.type === 'info'" class="toast-icon" />
      </div>
    </div>

    <!-- Contenido del Toast -->
    <div class="toast-content">
      <p v-if="title" class="toast-title">{{ title }}</p>
      <p class="toast-message">{{ message }}</p>
    </div>

    <!-- Botón de Cierre -->
    <button 
      @click="close" 
      class="toast-close-button" 
      aria-label="Cerrar notificación"
    >
      <i-mdi-close class="w-4 h-4" />
    </button>
    
    <!-- Barra de Progreso - Nueva versión -->
    <div class="toast-progress-container">
      <div 
        class="toast-progress-bar" 
        :style="{ 
          animationDuration: `${props.duration}ms`, 
          animationPlayState: isPaused ? 'paused' : 'running' 
        }"
      ></div>
    </div>
  </div>
</template>

<style scoped>
@reference "../../style.css";

@keyframes progress {
  from {
    transform: scaleX(1);
  }
  to {
    transform: scaleX(0);
  }
}

.toast-card {
  @apply relative flex w-full max-w-sm items-start gap-3 p-4 rounded-xl shadow-lg;
  @apply bg-surface border border-none;
  @apply transform-gpu transition-all duration-300 ease-in-out;
  @apply overflow-hidden;
}

/* --- Variantes de Color --- */
.toast--success {
  @apply text-text;
}
.toast--success .toast-icon-background {
  @apply bg-success/10;
}
.toast--success .toast-icon {
  @apply text-success;
}

.toast--error {
  @apply text-text;
}
.toast--error .toast-icon-background {
  @apply bg-error/10;
}
.toast--error .toast-icon {
  @apply text-error;
}

.toast--warning {
  @apply text-text;
}
.toast--warning .toast-icon-background {
  @apply bg-warning/10;
}
.toast--warning .toast-icon {
  @apply text-warning;
}

.toast--info {
  @apply text-text;
}
.toast--info .toast-icon-background {
  @apply bg-info/10;
}
.toast--info .toast-icon {
  @apply text-info;
}

/* Icono con fondo circular */
.toast-icon-container {
  @apply flex-shrink-0;
}
.toast-icon-background {
  @apply flex items-center justify-center;
  @apply w-8 h-8 rounded-full;
}
.toast-icon {
  @apply w-5 h-5;
}

/* Contenido */
.toast-content {
  @apply flex-grow py-0.5;
}
.toast-title {
  @apply font-semibold text-sm leading-tight;
}
.toast-message {
  @apply text-sm text-text-muted leading-tight mt-0.5;
}

/* Botón de cierre */
.toast-close-button {
  @apply flex-shrink-0;
  @apply w-6 h-6 flex items-center justify-center;
  @apply rounded-full transition-colors;
  @apply text-text-muted hover:text-text;
  @apply hover:bg-surface-darker;
  @apply focus:outline-none focus:ring-2 focus:ring-accent/50;
}

/* Barra de progreso mejorada */
.toast-progress-container {
  @apply absolute bottom-0 left-0 right-0 h-1 overflow-hidden;
  @apply bg-surface-darker; /* Fondo de la barra de progreso */
}
.toast-progress-bar {
  @apply h-full w-full origin-left;
  @apply transform-gpu;
  animation-name: progress;
  animation-timing-function: linear;
  animation-fill-mode: forwards;
}
.toast--success .toast-progress-bar {
  @apply bg-success;
}
.toast--error .toast-progress-bar {
  @apply bg-error;
}
.toast--warning .toast-progress-bar {
  @apply bg-warning;
}
.toast--info .toast-progress-bar {
  @apply bg-info;
}
</style>