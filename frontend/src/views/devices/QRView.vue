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
      <!-- Header compacto con ícono y título en una sola línea -->
      <div class="table-header">
        <div class="table-badge">
          <i-mdi-seat-outline class="w-6 h-6 text-accent" />
          <span class="table-title">Mesa {{ tableId }}</span>
        </div>
      </div>

      <!-- Componente QR -->
      <div class="qr-section">
        <QRComponent :qr_token="qrToken" :tableId="tableId" />
      </div>

      <!-- Mensaje de bienvenida -->
      <div class="welcome-message">
        <div class="welcome-icon">
          <i-mdi-food-fork-drink class="w-8 h-8 text-accent" />
        </div>
        <div class="welcome-text">
          <h3>¡Bienvenido a Café Buen Sabor!</h3>
          <p>Escanea el código QR para explorar nuestro delicioso menú</p>
        </div>
      </div>

    </div>
  </DisplayLayout>
</template>

<script setup>
import { useRoute } from 'vue-router';
import { useTableDisplay } from '../../composables/useTableQR.js';

const route = useRoute();
const tableId = route.params.table_id;
const { qrToken, isLoading, error } = useTableDisplay(tableId);
</script>

<style scoped>
@reference "../../style.css";

/* Estados generales */
.loading-state,
.error-state,
.qr-ready-state {
  @apply w-full h-full flex flex-col items-center justify-center gap-6 sm:gap-8 px-4 sm:px-6;
}

/* Estado de carga */
.loading-state {
  @apply text-center;
}

.loading-spinner {
  @apply mb-4;
}

.loading-text h2 {
  @apply text-2xl sm:text-3xl font-bold text-text-light mb-2;
}

.loading-text p {
  @apply text-lg text-text-muted;
}

/* Estado de error */
.error-state {
  @apply text-center;
}

.error-icon {
  @apply mb-4;
}

.error-content h2 {
  @apply text-2xl sm:text-3xl font-bold text-error mb-2;
}

.error-content p {
  @apply text-lg text-text-muted mb-6;
}

.retry-button {
  @apply inline-flex items-center gap-2 px-6 py-3 bg-accent text-primary font-semibold rounded-lg;
  @apply hover:bg-accent-dark transition-all duration-200 transform hover:scale-105;
  @apply shadow-lg hover:shadow-xl;
}

.qr-ready-state {
  @apply max-w-2xl mx-auto flex flex-col items-center justify-start mt-6 sm:mt-10 gap-6;
}
.table-header {
  @apply flex justify-center mb-4;
}

.table-badge {
  @apply inline-flex items-center gap-3 px-5 py-2 rounded-full border border-accent/40 bg-accent/10;
  box-shadow: 0 0 12px rgba(203, 161, 53, 0.3);
}

.table-title {
  @apply text-xl sm:text-2xl font-semibold text-text-light;
  background: linear-gradient(to right, #f4c430, #ecb300, #cba135);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.table-number {
  @apply text-lg sm:text-xl font-bold;
}



/* Sección QR */
.qr-section {
  @apply w-full flex justify-center mb-4 sm:mb-6;
}

/* Mensaje de bienvenida */
.welcome-message {
  @apply flex flex-col sm:flex-row items-center gap-3 sm:gap-4 text-center sm:text-left;
  @apply p-3 sm:p-4 bg-primary-light/30 rounded-2xl border border-accent/20;
  backdrop-filter: blur(10px);
}

.welcome-icon {
  @apply flex-shrink-0;
}

.welcome-text h3 {
  @apply text-lg sm:text-xl font-bold text-text-light mb-1 sm:mb-2;
}

.welcome-text p {
  @apply text-sm sm:text-base text-text-muted;
}

/* Footer informativo */
.info-footer {
  @apply flex items-center justify-center gap-4 mt-4 sm:mt-6;
  @apply text-xs sm:text-sm text-text-muted;
}

.info-item {
  @apply flex items-center gap-2;
}

.info-divider {
  @apply w-1 h-1 bg-text-muted rounded-full;
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .table-header {
    @apply gap-2 mb-3;
  }

  .table-title h1 {
    @apply text-2xl;
  }

  .welcome-message {
    @apply p-3 text-center;
  }

  .info-footer {
    @apply flex-col gap-2 mt-3;
  }

  .info-divider {
    @apply hidden;
  }
}

/* Landscape mobile adjustments */
@media (max-height: 600px) and (orientation: landscape) {

  .loading-state,
  .error-state,
  .qr-ready-state {
    @apply py-2;
  }

  .qr-ready-state {
    @apply gap-2;
  }

  .table-header {
    @apply mb-2;
  }

  .table-title h1 {
    @apply text-2xl;
  }

  .qr-section {
    @apply mb-2;
  }

  .welcome-message {
    @apply p-2 text-xs;
  }

  .info-footer {
    @apply mt-2 text-xs;
  }
}

/* Tablet adjustments */
@media (min-width: 768px) and (max-width: 1024px) {
  .qr-ready-state {
    @apply gap-6;
  }

  .table-title h1 {
    @apply text-4xl;
  }
}

/* Animaciones */
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.qr-ready-state>* {
  animation: fade-in 0.6s ease-out;
}

.qr-ready-state>*:nth-child(1) {
  animation-delay: 0.1s;
}

.qr-ready-state>*:nth-child(2) {
  animation-delay: 0.2s;
}

.qr-ready-state>*:nth-child(3) {
  animation-delay: 0.3s;
}

.qr-ready-state>*:nth-child(4) {
  animation-delay: 0.4s;
}
</style>