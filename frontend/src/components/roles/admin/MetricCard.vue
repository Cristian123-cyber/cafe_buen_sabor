<script setup>
import { computed, defineProps } from 'vue';

const props = defineProps({
  // Título de la tarjeta. Ej: "Ingresos (Hoy)"
  title: {
    type: String,
    required: true,
  },
  // Valor principal a mostrar. Ej: "$1,250.75" o "12 / 20"
  value: {
    type: String,
    required: true,
  },
  // Porcentaje de cambio para el indicador de tendencia. Ej: 15, -5
  trendValue: {
    type: Number,
    default: null,
  },
  // Texto que acompaña a la tendencia. Ej: "vs ayer"
  trendText: {
    type: String,
    default: '',
  },
  // Valor de 0 a 100 para la barra de progreso. Si se provee, reemplaza al indicador de tendencia.
  progress: {
    type: Number,
    default: null,
    validator: (value) => value >= 0 && value <= 100,
  }
});

// Clase CSS dinámica para la tendencia (positiva, negativa o neutral)
const trendClasses = computed(() => {
  if (props.trendValue > 0) {
    return 'trend-positive';
  }
  if (props.trendValue < 0) {
    return 'trend-negative';
  }
  return 'trend-neutral';
});

// Formatea el valor de la tendencia para añadir el signo '+'
const formattedTrendValue = computed(() => {
  if (props.trendValue > 0) {
    return `+${props.trendValue}%`;
  }
  if (props.trendValue !== null) {
    return `${props.trendValue}%`;
  }
  return '';
});

// Estilo en línea para la barra de progreso, calculado a partir de la prop
const progressStyle = computed(() => {
  return { width: `${props.progress}%` };
});
</script>

<template>
  <div class="metric-card group">
    <!-- Fila superior: Título e Icono -->
    <div class="card-header">
      <div class="flex-1 pr-4">
        <h3 class="card-title">
          {{ title }}
        </h3>
      </div>
      <div class="icon-wrapper">
        <slot name="icon">
          <!-- Fallback icon por si no se provee uno -->
          <i-mdi-help-rhombus-outline class="card-icon" />
        </slot>
      </div>
    </div>

    <!-- Valor principal -->
    <p class="card-value">
      {{ value }}
    </p>

    <!-- Fila inferior: Barra de Progreso o Indicador de Tendencia -->
    <div class="mt-auto">
      <!-- Caso 1: Mostrar la barra de progreso si la prop 'progress' existe -->
      <div v-if="progress !== null" class="space-y-2">
        <div class="progress-wrapper">
          <div class="progress-bar-lg">
            <div 
              class="progress-bar"
              :style="progressStyle"
            >
              <div class="progress-shine"></div>
            </div>
          </div>
          <span class="text-sm font-bold text-accent min-w-max">{{ progress }}%</span>
        </div>
        <span class="text-xs font-medium tracking-wider text-gray-500 normal-case">{{ trendText }}</span>
      </div>

      <!-- Caso 2: Mostrar el indicador de tendencia si no hay barra de progreso -->
      <div v-else :class="trendClasses" class="trend-wrapper">
        <template v-if="trendValue !== null">
          <div 
            v-if="trendValue > 0" 
            class="trend-icon-positive"
          >
            <i-mdi-trending-up class="trend-icon text-success" />
          </div>
          <div 
            v-if="trendValue < 0" 
            class="trend-icon-negative"
          >
            <i-mdi-trending-down class="trend-icon text-error" />
          </div>
          <span class="trend-value">{{ formattedTrendValue }}</span>
        </template>
        <span class="trend-text">{{ trendText }}</span>
      </div>
    </div>
  </div>
</template>

<style scoped>
@reference "../../../style.css";

/* Animaciones personalizadas */
@keyframes shine {
  0% { transform: translateX(-100%); }
  50% { transform: translateX(100%); }
  100% { transform: translateX(100%); }
}

@keyframes pulse-trend {
  0%, 100% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.1); opacity: 0.8; }
}

/* Utilidades personalizadas */
.animate-pulse-trend {
  animation: pulse-trend 2s ease-in-out infinite;
}

/* Componente principal */
.metric-card {
  @apply relative flex flex-col p-4 sm:p-6 rounded-xl sm:rounded-2xl transition-all duration-500 ease-out;
  @apply border border-gray-200;
  background: linear-gradient(145deg, #ffffff 0%, #f8f8f8 50%, #f0f0f0 100%);
  box-shadow: 
    0 4px 20px rgba(0, 0, 0, 0.05),
    0 1px 3px rgba(0, 0, 0, 0.05),
    inset 0 1px 0 rgba(255, 255, 255, 0.9);
}

.metric-card:hover {
  @apply -translate-y-2;
  box-shadow: 
    0 12px 40px rgba(0, 0, 0, 0.08),
    0 4px 12px rgba(0, 0, 0, 0.05),
    inset 0 1px 0 rgba(255, 255, 255, 0.9),
    0 0 0 1px rgba(203, 161, 53, 0.2),
    0 0 30px rgba(203, 161, 53, 0.1);
  background: linear-gradient(145deg, #ffffff 0%, #fafafa 50%, #f2f2f2 100%);
}

.metric-card::before {
  content: '';
  @apply absolute inset-0 rounded-xl sm:rounded-2xl pointer-events-none;
  background: linear-gradient(145deg, rgba(203, 161, 53, 0.1), transparent 50%, rgba(203, 161, 53, 0.05));
  mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
  mask-composite: xor;
  padding: 1px;
}

/* --- Estilos de los Elementos --- */

.card-header {
  @apply flex justify-between items-start mb-1;
}
.card-title {
  @apply font-bold text-sm sm:text-xs tracking-wider text-gray-600 uppercase;
}
.card-icon {
  @apply w-6 h-6 sm:w-5 sm:h-5 text-gray-600; 
}
.card-value {
  @apply text-4xl font-bold text-gray-700 leading-none mb-4 transition-all duration-300;
}
.icon-wrapper {
  @apply flex items-center justify-center p-2 sm:p-3 rounded-full;
  @apply bg-gray-100 border border-gray-200 transition-all duration-300;
  background: linear-gradient(135deg, #f5f5f5 0%, #eaeaea 100%);
  box-shadow: 
    inset 0 1px 0 rgba(255, 255, 255, 0.9),
    0 2px 8px rgba(0, 0, 0, 0.05);
}
.group:hover .icon-wrapper {
  @apply border-accent/30;
  background: linear-gradient(135deg, #f7f7f7 0%, #ececec 100%);
  box-shadow: 
    inset 0 1px 0 rgba(203, 161, 53, 0.1),
    0 4px 12px rgba(0, 0, 0, 0.08);
}

/* --- Barra de Progreso --- */

.progress-wrapper {
  @apply flex items-center gap-3 sm:gap-4;
}
.progress-bar-lg {
  @apply flex-1 h-2 bg-gray-100 border border-gray-400 rounded-full overflow-hidden shadow-inner;
}
.progress-bar {
  @apply h-full bg-gradient-to-r from-yellow-500 via-accent to-yellow-300 rounded-full transition-all duration-700 ease-out relative overflow-hidden;
  box-shadow: 
    0 0 8px rgba(203, 161, 53, 0.3),
    inset 0 1px 0 rgba(255, 255, 255, 0.3);
}
.progress-shine {
  @apply absolute inset-0 rounded-full;
  background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.5) 50%, transparent 100%);
  animation: shine 2s ease-in-out infinite;
  transform: translateX(-100%);
}

/* --- Sección de Tendencia (Corregida) --- */

.trend-wrapper {
  @apply flex items-center gap-3;
}

.trend-icon-positive, .trend-icon-negative {
  @apply flex items-center justify-center w-7 h-7 rounded-full backdrop-blur-sm;
  animation: pulse-trend 2s ease-in-out infinite;
}
.trend-icon-positive {
  @apply border border-green-300 bg-green-100/80;
}
.trend-icon-negative {
  @apply border border-red-300 bg-red-100/80;
}
.trend-icon {
  @apply w-4 h-4;
}
.trend-value {
  @apply font-bold text-base;
}
.trend-text {
  @apply text-xs font-medium tracking-wider text-gray-500 normal-case;
}

/* Clases de color y brillo para la tendencia */
.trend-positive .trend-value {
  @apply text-green-600;
  text-shadow: 0 0 10px rgba(34, 197, 94, 0.2);
}
.trend-negative .trend-value {
  @apply text-red-600;
  text-shadow: 0 0 10px rgba(211, 47, 47, 0.2);
}
.trend-neutral .trend-value {
  @apply text-gray-600;
}
</style>