<template>
    <!-- Estado de carga -->
  <div v-if="loading" class="qr-minimal-container">
    <LoadingState>

    </LoadingState>
   
  </div>

  <div v-else class="qr-minimal-container">
    <!-- Header con título e info de mesa -->
    <div class="qr-header">
      <div class="table-info">
        
        <p class="qr-subtitle">Código QR para menú digital</p>
      </div>
      
      <!-- Status indicator -->
      <div class="status-indicator" :class="{ 'active': timeLeft > 0, 'expired': timeLeft <= 0 }">
        <div class="status-dot"></div>
        <span class="status-text">{{ timeLeft > 0 ? 'Activo' : 'Pausado' }}</span>
      </div>
    </div>

    <!-- QR Code Container -->
    <div class="qr-container" ref="qrContainerRef">
      <div class="qr-wrapper">
        <qrcode-vue 
          :value="qrURL" 
          :size="qrSize" 
          level="H" 
          background="transparent" 
          foreground="#1a1a1a"
          class="qr-code" 
        />
      </div>
      
      <!-- Overlay cuando está expirado -->
      <div v-if="timeLeft <= 0" class="expired-overlay">
        <i-mdi-refresh class="w-6 h-6 text-white" />
        <span class="text-white text-sm font-medium">Código expirado</span>
      </div>
    </div>

    <!-- Timer bar (solo cuando está activo) -->
    <div v-if="timeLeft > 0" class="timer-container">
      <div class="timer-info">
        <i-mdi-timer class="w-4 h-4 text-gray-500" />
        <span class="timer-text">{{ formattedTimeLeft }}</span>
      </div>
      <div class="timer-bar">
        <div 
          class="timer-progress" 
          :style="{ width: `${percentageLeft}%` }"
        ></div>
      </div>
    </div>

    <!-- Footer con instrucciones muy simples -->
    <div class="qr-footer">
      <p class="instruction-text">
        <i-mdi-camera class="w-4 h-4" />
        Escanear con cámara del teléfono
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import QrcodeVue from 'qrcode.vue';
import { useResizeObserver } from '@vueuse/core';

const props = defineProps({
  qr_token: {
    type: String,
    required: true
  },
  tableId: {
    type: [String, Number],
    required: true
  },
  timeLeft: {
    type: Number,
    required: true // en milisegundos
  },
  totalDuration: {
    type: Number,
    required: true // en milisegundos
  },
  loading: {
    type: Boolean
  }
});

// URL del QR
const qrURL = computed(() => 
  `http://localhost:5173/session/validate?token=${props.qr_token}&table=${props.tableId}`
);

// Lógica de tamaño responsive del QR
const qrContainerRef = ref(null);
const qrSize = ref(200);

useResizeObserver(qrContainerRef, (entries) => {
  const { width } = entries[0].contentRect;
  // Para modales, mantenemos un tamaño más compacto
  const containerWidth = Math.min(width - 32, 280); // Max 280px, padding 16px cada lado
  qrSize.value = Math.max(160, containerWidth);
});

// Cálculos del timer
const percentageLeft = computed(() => {
  if (props.totalDuration <= 0) return 0;
  return Math.max(0, (props.timeLeft / props.totalDuration) * 100);
});

const formattedTimeLeft = computed(() => {
  const safeTimeLeft = Math.max(0, props.timeLeft);
  const minutes = Math.floor(safeTimeLeft / 60000);
  const seconds = Math.floor((safeTimeLeft % 60000) / 1000);
  return `${minutes}:${seconds.toString().padStart(2, '0')}`;
});
</script>

<style scoped>
@reference "../../style.css";



.qr-minimal-container {
  @apply w-full max-w-sm mx-auto bg-white rounded-2xl shadow-lg overflow-hidden;
  font-family: system-ui, -apple-system, sans-serif;
}

/* Header */
.qr-header {
  @apply flex items-center justify-between p-4 bg-gray-50 border-b border-gray-100;
}

.table-info {
  @apply flex-1;
}

.table-title {
  @apply text-lg font-semibold text-gray-900 mb-0.5;
}

.qr-subtitle {
  @apply text-sm text-gray-600 m-0;
}

.status-indicator {
  @apply flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium;
}

.status-indicator.active {
  @apply bg-green-100 text-green-700;
}

.status-indicator.expired {
  @apply bg-blue-100 text-blue-700;
}

.status-dot {
  @apply w-2 h-2 rounded-full;
}

.status-indicator.active .status-dot {
  @apply bg-green-500;
}

.status-indicator.expired .status-dot {
  @apply bg-blue-500;
}

/* QR Container */
.qr-container {
  @apply relative p-6 bg-white flex items-center justify-center;
  min-height: 200px;
}

.qr-wrapper {
  @apply relative bg-white p-4 rounded-xl shadow-sm border border-gray-200;
  transition: all 0.2s ease;
}

.qr-wrapper:hover {
  @apply shadow-md;
}

.qr-code {
  @apply block w-full h-auto;
}

.expired-overlay {
  @apply absolute inset-0 bg-black/60 flex flex-col items-center justify-center gap-2 rounded-xl;
}

/* Timer */
.timer-container {
  @apply px-4 pb-4;
}

.timer-info {
  @apply flex items-center justify-center gap-2 mb-2;
}

.timer-text {
  @apply text-sm font-mono font-semibold text-gray-700;
}

.timer-bar {
  @apply w-full h-1.5 bg-gray-200 rounded-full overflow-hidden;
}

.timer-progress {
  @apply h-full bg-gradient-to-r from-blue-500 to-blue-600 rounded-full transition-all duration-300 ease-out;
}

/* Footer */
.qr-footer {
  @apply px-4 pb-4 border-t border-gray-100 pt-3;
}

.instruction-text {
  @apply flex items-center justify-center gap-2 text-sm text-gray-600 m-0;
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .qr-minimal-container {
    @apply max-w-full mx-2;
  }
  
  .qr-container {
    @apply p-4;
    min-height: 180px;
  }
  
  .table-title {
    @apply text-base;
  }
  
  .qr-subtitle {
    @apply text-xs;
  }
}

/* Animation for status changes */
.status-indicator {
  transition: all 0.3s ease;
}

.qr-wrapper {
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}
</style>