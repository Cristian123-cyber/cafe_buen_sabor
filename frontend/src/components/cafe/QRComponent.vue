<template>
  <div class="page-container" :class="{ 'lg:items-start': !withDetails, 'lg:items-center': withDetails }">
    <div class="main-card" :class="{
      'flex-col lg:flex-row max-w-sm lg:max-w-4xl': withDetails,
      'flex-col max-w-sm': !withDetails
    }">


      <div class="qr-column" :class="{ 'lg:w-1/2': withDetails, 'w-full': !withDetails }">
        <!--
          CAMBIO CLAVE 1:
          Añadimos una ref para poder medir este elemento desde el script.
        -->
        <div ref="qrWrapperRef" class="qr-code-wrapper">
          <qrcode-vue :value="qrURL" :size="qrSize" level="H" background="transparent" foreground="#212121"
            class="qr-code" />
        </div>
      </div>

      <div v-if="withDetails" class="info-column" :class="{ 'lg:w-1/2': withDetails }">
        <div class="info-content">
          <h2 class="welcome-title">Bienvenido, escanea para continuar</h2>
          <p class="welcome-subtitle">Usa la cámara de tu teléfono para acceder a nuestro menú digital.



          </p>

          <div v-if="!message && message === null" class="timer-section">
            <div class="timer-header">
              <i-mdi-timer-outline class="w-5 h-5" />
              <span class="timer-label">El código se actualiza en:</span>
              <span class="timer-value">{{ formattedTimeLeft }}</span>
            </div>
            <div class="progress-track">
              <div class="progress-fill" :style="{ width: `${percentageLeft}%` }"></div>
            </div>
          </div>

          <div v-else class="message-section">
            <p class="text-sm text-text-light">{{ message }}</p>
          </div>

          <div class="instructions-container">
            <p class="break-all"> {{ qrURL }} </p>

            <div class="instruction-item">
              <div class="step-icon-wrapper">
                <i-mdi-camera-outline class="w-6 h-6" />
              </div>
              <span class="step-text">Abre tu cámara</span>
            </div>
            <div class="instruction-item">
              <div class="step-icon-wrapper">
                <i-mdi-qrcode-scan class="w-6 h-6" />
              </div>
              <span class="step-text">Escanea el código</span>
            </div>
            <div class="instruction-item">
              <div class="step-icon-wrapper">
                <i-mdi-food-fork-drink class="w-6 h-6" />
              </div>
              <span class="step-text">Disfruta el menú</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'; // Quitamos onMounted, onUnmounted, watch
import QrcodeVue from 'qrcode.vue';
import { useResizeObserver } from '@vueuse/core';

// MODIFICADO: Nuevas props más simples.
const props = defineProps({
  qr_token: {
    type: String,
    required: true
  },
  tableId: {
    type: [String, Number],
    required: true
  },
  // Recibimos el tiempo restante y la duración total desde el padre.
  timeLeft: {
    type: Number,
    required: true // en milisegundos
  },
  totalDuration: {
    type: Number,
    required: true // en milisegundos
  },
  withDetails: {
    type: Boolean,
    default: true // Si queremos mostrar detalles adicionales
  },
  message: {
    type: String,
    default: null
  } 
});

// URL del QR (sin cambios)
const qrURL = computed(() => `http://localhost:5173/session/validate?token=${props.qr_token}&table=${props.tableId}`);

// Lógica de tamaño del QR (sin cambios)
const qrWrapperRef = ref(null);
const qrSize = ref(256);
useResizeObserver(qrWrapperRef, (entries) => {
  const { width } = entries[0].contentRect;
  qrSize.value = Math.max(128, width);
});

// MODIFICADO: Los computados ahora usan las props directamente.
const percentageLeft = computed(() => {
  // Evitar división por cero si la duración es 0
  if (props.totalDuration <= 0) return 0;
  return (props.timeLeft / props.totalDuration) * 100;
});

const formattedTimeLeft = computed(() => {
  // Asegurarnos de que no mostramos valores negativos
  const safeTimeLeft = Math.max(0, props.timeLeft);
  const minutes = Math.floor(safeTimeLeft / 60000);
  const seconds = Math.floor((safeTimeLeft % 60000) / 1000);
  return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
});

// ELIMINADO: toda la lógica de startTimer, onMounted, onUnmounted y watch
// ya no es necesaria en este componente.
</script>

<style scoped>
@reference "../../style.css";

/* Contenedor de página para centrar la tarjeta */
.page-container {
  /*
    CAMBIO CLAVE 4:
    - min-h-dvh: Usa la altura dinámica del viewport, que se ajusta mejor en móviles
      cuando aparecen/desaparecen las barras del navegador.
    - items-start lg:items-center: En móviles, alinea la tarjeta arriba para verla
      de inmediato. En pantallas grandes, la centra verticalmente.
    - overflow-y-auto: Si el contenido sigue siendo muy alto, permite el scroll
      de la página de forma natural.
  */
  @apply w-full min-h-dvh flex items-start justify-center bg-secondary-dark p-4 font-cafes overflow-y-auto;
}

.message-section {
  @apply w-full text-center p-4 bg-primary-dark rounded-lg;
}

/* Tarjeta principal con layout flexible */
.main-card {
  /*
    MODIFICADO:
    - Se eliminan 'max-w-sm', 'lg:max-w-4xl', 'flex-col', 'lg:flex-row'.
    - Estas clases ahora están en el :class del template.
    - Mantenemos solo los estilos base que aplican a AMBOS casos.
  */
  @apply w-full flex bg-surface rounded-2xl shadow-lg overflow-hidden my-8 lg:my-0;
}

/* --- Columna Izquierda: Código QR --- */
.qr-column {
  /*
    MODIFICADO:
    - Se elimina 'lg:w-1/2'. Ahora está en el :class del template.
  */
  @apply p-6 lg:p-8 flex items-center justify-center bg-secondary-light;
}

.qr-code-wrapper {
  /*
    El padding aquí crea el marco blanco.
    El `useResizeObserver` medirá el ancho *dentro* de este padding.
  */
  @apply w-full max-w-xs bg-surface p-4 rounded-xl border border-border-light shadow-md;
}

.qr-code {
  /*
    Ahora que el `:size` es dinámico, este CSS asegura que el <canvas>
    se muestre correctamente, centrado y sin desbordarse.
  */
  @apply block mx-auto w-full max-w-full h-auto;
}

.info-column {
  /*
    MODIFICADO:
    - Se elimina 'lg:w-1/2'. Ahora está en el :class del template.
  */
  @apply flex items-center;
}

.info-content {
  /* Reducimos el gap y padding en pantallas pequeñas para ahorrar espacio vertical */
  @apply w-full flex flex-col gap-4 lg:gap-6 p-6 lg:p-8 text-left;
}

.welcome-title {
  @apply text-2xl sm:text-3xl font-bold text-text;
}

.welcome-subtitle {
  @apply text-base text-text-muted -mt-4;
}

/* Sección del Temporizador */
.timer-section {
  @apply w-full;
}

.timer-header {
  @apply flex items-center gap-2 mb-2 text-sm text-text-muted;
}

.timer-value {
  @apply font-mono font-semibold text-accent-dark text-base;
}


.progress-track {
  @apply w-full h-2 bg-secondary-dark rounded-full overflow-hidden;
}

.progress-fill {
  /*
    ANTES (El problema): La animación dura 1 segundo, igual que el intervalo de actualización
    @apply h-full bg-accent rounded-full transition-all duration-1000 ease-linear;
  */

  /*
    AHORA (La solución): Una transición muy rápida que se siente instantánea pero es suave.
    - transition-property-width: Más eficiente, solo animamos el ancho.
    - duration-200: 200ms es lo suficientemente rápido para parecer instantáneo.
    - ease-out: La animación comienza rápido y se desacelera, lo que la hace sentir más responsiva.
  */
  @apply h-full bg-accent rounded-full transition-all duration-200 ease-out;
}


/* Instrucciones Simplificadas */
.instructions-container {
  @apply w-full flex flex-col gap-4 border-t border-border-light pt-6;
}

.instruction-item {
  @apply flex items-center gap-4;
}

.step-icon-wrapper {
  @apply flex-shrink-0 w-10 h-10 flex items-center justify-center bg-primary text-text-muted rounded-lg;
}

.step-text {
  @apply text-gray-500 font-medium text-sm sm:text-base;
}
</style>