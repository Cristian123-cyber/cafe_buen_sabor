<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  loading: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: null
  }
});
</script>

<template>
  <div class="chart-card">
    <header class="card-header">
      <div class="title-section">
        <h3 class="card-title">{{ title }}</h3>
      </div>
      <div class="card-actions">
        <slot name="actions"></slot>
      </div>
    </header>

    <main class="card-content">
      <div v-if="loading" class="state-wrapper">
        <i-svg-spinners-bars-scale-fade class="h-16 w-16 text-blue-500 opacity-80" />
      </div>
      
      <div v-else-if="error" class="state-wrapper">
        <div class="error-content">
          <i-mdi-alert-circle-outline class="h-12 w-12 text-red-400" />
          <h4 class="error-title">Error al cargar el gráfico</h4>
          <p class="error-text">No se pudieron cargar los datos.</p>
          <p class="error-details">{{ error }}</p>
        </div>
      </div>

      <div v-else class="chart-wrapper">
        <slot></slot>
      </div>
    </main>
  </div>
</template>

<style scoped>
@reference '../../style.css';

/* Mantengo exactamente los mismos estilos base */
.chart-card {
  @apply mx-2 flex flex-col rounded-2xl border border-gray-200/60 bg-white shadow-lg backdrop-blur-sm transition-all duration-300 hover:shadow-xl hover:shadow-gray-200/50 hover:border-gray-300/70 sm:mx-0;
  min-height: 400px;
  min-width: 300px;
  max-height: 90vh; /* Nuevo: Limito la altura máxima */
  height: auto; /* Ajuste para mejor comportamiento responsive */
}

.card-header {
  @apply flex flex-col gap-4 rounded-t-2xl border-b border-gray-100/80 bg-primary p-4 sm:flex-row sm:items-center sm:justify-between sm:gap-2 sm:p-5;
  flex-shrink: 0;
}

.title-section {
  @apply flex flex-col gap-1;
}

.card-title {
  @apply text-lg font-bold leading-tight tracking-tight text-text-light sm:text-xl;
}

.card-actions {
  @apply flex flex-shrink-0 items-center gap-2;
}

.card-content {
  @apply flex flex-1 flex-col bg-gradient-to-br from-white via-gray-50/30 to-white;
  padding: 0;
  min-height: 320px;
  max-height: calc(100% - 72px); /* Nuevo: Considera altura del header */
  overflow: hidden; /* Nuevo: Evita desbordamientos */
}

.chart-wrapper {
  @apply w-full flex-1 flex flex-col;
  padding: 16px;
  min-height: 300px;
  max-height: 100%; /* Nuevo: Limita altura del gráfico */
  overflow: auto; /* Nuevo: Scroll si es necesario */
}

.state-wrapper {
  @apply flex flex-1 items-center justify-center p-4;
  min-height: 320px;
  max-height: 100%; /* Nuevo: Control de altura */
}

.error-content {
  @apply flex flex-col items-center justify-center gap-3 text-center sm:gap-4;
}

.error-title {
  @apply text-lg font-semibold text-gray-800;
}

.error-text {
  @apply text-gray-600;
}

.error-details {
  @apply max-w-md break-words rounded-md bg-primary/80 px-3 py-2 text-sm text-text-light;
}

/* Mejoras solo en responsive */
@media (max-width: 640px) {
  .chart-card {
    min-height: 350px;
    margin: 0 8px;
    max-height: 80vh; /* Ajuste para móviles */
  }
  
  .chart-wrapper {
    padding: 12px;
    min-height: 250px;
  }
  
  .state-wrapper {
    min-height: 270px;
  }
}

@media (min-width: 641px) and (max-width: 1024px) {
  .chart-card {
    min-height: 420px;
    max-height: 70vh; /* Ajuste para tablets */
  }
  
  .chart-wrapper {
    min-height: 340px;
  }
}

@media (min-width: 1025px) {
  .chart-card {
    min-height: 480px;
    max-height: 65vh; /* Ajuste para desktop */
  }
  
  .chart-wrapper {
    min-height: 380px;
  }
}
</style>