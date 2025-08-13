<template>
  <DisplayLayout>
    <!-- Estado de carga -->
    <div v-if="isLoading" class="loading-state">
      <div class="loading-spinner">
        <i-svg-spinners-bars-scale-fade class="w-16 h-16 text-accent" />
      </div>
      <div class="loading-text">
        <h2>Preparando tu mesa...</h2>
        <p>Cargando información del QR</p>
      </div>
    </div>

    <!-- Estado de error -->
    <div v-else-if="error" class="error-state">
      <div class="error-icon">
        <i-mdi-alert-circle class="w-20 h-20 text-error" />
      </div>
      <div class="error-content">
        <h2>Error de Conexión</h2>
        <p>{{ error }}</p>
        <button @click="() => window.location.reload()" class="retry-button">
          <i-mdi-refresh class="w-5 h-5" />
          Reintentar
        </button>
      </div>
    </div>

     <!-- Estado principal - QR listo -->
    <div v-else-if="qrToken" class="qr-ready-state">
      

      <!-- El padding de este contenedor creará el espacio para el header -->
      <div class="qr-section">
    
  

        <QRComponent 
        :qr_token="qrToken" 
        :tableId="tableId" 
        :timeLeft="timeLeft"
        :totalDuration="totalDuration"
        :message="message"
        />
      </div>
    </div>
  </DisplayLayout>
</template>

<script setup>
import { useRoute } from 'vue-router';
import { useTableDisplay } from '../../composables/useTableQR.js';

const route = useRoute();
const tableId = route.params.table_id;
// El composable ahora devuelve todo lo que necesitamos
const { qrToken, isLoading, error, timeLeft, totalDuration, message } = useTableDisplay(tableId);
</script>

<style scoped>
@reference "../../style.css";

/* Estados generales (sin cambios) */
.loading-state,
.error-state {
  @apply w-full h-full flex flex-col items-center justify-center text-center gap-6 sm:gap-8 px-4 sm:px-6;
}
.loading-spinner { @apply mb-4; }
.loading-text h2 { @apply text-2xl sm:text-3xl font-bold text-text mb-2; }
.loading-text p { @apply text-lg text-text-muted; }
.error-icon { @apply mb-4; }
.error-content h2 { @apply text-2xl sm:text-3xl font-bold text-error mb-2; }
.error-content p { @apply text-lg text-text-muted mb-6; }
.retry-button {
  @apply inline-flex items-center gap-2 px-6 py-3 bg-accent text-primary font-semibold rounded-lg;
  @apply hover:bg-accent-dark transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl;
}

/* --- Layout Integrado (Solución Mejorada) --- */

.qr-ready-state {
  /* // MODIFICADO: Añadimos 'relative' para que sea el contexto de posicionamiento del header.
     // Añadimos padding superior para que el contenido no se pegue al borde de la pantalla. */
  @apply w-full relative flex flex-col items-center justify-start pt-12 sm:pt-16;
}

.table-header {
  /* // NUEVO: Posicionamiento absoluto para "flotar" sobre la tarjeta QR. */
  @apply absolute top-0 left-1/2 -translate-x-1/2 z-10;
  /* 'left-1/2 -translate-x-1/2' es la técnica estándar para centrar horizontalmente un elemento absoluto.
     'z-10' asegura que esté por encima de la tarjeta QR. */
}

.table-badge {
  /* // MODIFICADO: Ajustamos el estilo para que se vea como un "chip" premium. */
  @apply inline-flex items-center gap-3 px-5 py-2 rounded-full border border-border-light bg-surface;
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1); /* Sombra más pronunciada */
}

.table-title {
  @apply text-xl sm:text-2xl font-semibold text-text;
}

.qr-section {
  /* // MODIFICADO: Añadimos padding superior para dejar espacio al 'table-badge' flotante. */
  @apply w-full flex justify-center pt-1;
  /* Este padding asegura que el QRComponent comience justo debajo del header, creando una apariencia integrada. */
}


/* Animaciones (sin cambios) */
@keyframes fade-in {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.qr-ready-state > * {
  animation: fade-in 0.5s ease-out forwards;
  opacity: 0;
}

.qr-ready-state > *:nth-child(1) { animation-delay: 0.1s; }
.qr-ready-state > *:nth-child(2) { animation-delay: 0.2s; }

</style>