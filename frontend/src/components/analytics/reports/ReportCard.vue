<script setup>
defineProps({
  title: {
    type: String,
    required: true,
  },
  description: {
    type: String,
    required: true,
  }
});
</script>

<template>
  <div class="report-card">
    <div class="card-header">
      <!-- Contenedor del ícono con gradiente y animación -->
      <div class="icon-container">
        <slot name="icon"></slot>
      </div>
      
      <div class="title-group">
        <h3 class="title">{{ title }}</h3>
        <p class="description">{{ description }}</p>
      </div>
    </div>
    
    <div class="card-body">
      <!-- Slot para los campos del formulario (ej. selectores de fecha) -->
      <slot name="form-fields"></slot>
    </div>
    
    <div class="card-footer">
      <!-- Slot para el botón de acción -->
      <slot name="action-button"></slot>
    </div>
  </div>
</template>

<style scoped>
@reference "../../../style.css"; /* Ajusta la ruta si es necesario */

.report-card {
  @apply relative;
  @apply bg-primary;
  @apply rounded-xl;
  @apply shadow-sm hover:shadow-xl;
  @apply p-6 sm:p-8;
  @apply flex flex-col gap-6;
  @apply transition-all duration-300 ease-out;
  @apply backdrop-blur-sm;
  @apply overflow-hidden;
  
}



/* Efecto de brillo sutil al hacer hover */
.report-card::before {
  @apply absolute inset-0 opacity-0 transition-opacity duration-300;
  content: '';
  background: linear-gradient(135deg, 
    rgba(59, 130, 246, 0.05) 0%, 
    rgba(147, 51, 234, 0.05) 100%
  );
  pointer-events: none;
}

.report-card:hover::before {
  @apply opacity-100;
}

.card-header {
  @apply flex items-start gap-4 sm:gap-6;
}

.icon-container {
  @apply relative;
  @apply w-12 h-12 sm:w-14 sm:h-14;
  @apply rounded-xl;
  @apply bg-gradient-to-br from-blue-50 to-indigo-100;
  @apply dark:from-blue-900/20 dark:to-indigo-900/30;
  @apply border border-accent/50;
  @apply flex items-center justify-center;
  @apply flex-shrink-0;
  @apply transition-all duration-300;
  @apply shadow-sm;
}



.icon-container :slotted(*) {
  @apply w-6 h-6 sm:w-7 sm:h-7;
  @apply text-accent;
  @apply transition-colors duration-300;
}

.title-group {
  @apply flex flex-col gap-2;
  @apply min-w-0 flex-1; /* Para que el texto se trunque correctamente en mobile */
}

.title {
  @apply text-xl sm:text-2xl font-bold;
  @apply text-gray-900 dark:text-gray-50;
  @apply leading-tight;
  @apply transition-colors duration-300;
}

.description {
  @apply text-sm sm:text-base;
  @apply text-gray-600 dark:text-gray-300;
  @apply leading-relaxed;
  @apply transition-colors duration-300;
}

.card-body {
  @apply mt-2 mb-4;
  @apply empty:hidden;
  @apply transition-all duration-300;
}

.card-footer {
  @apply mt-auto pt-6;
  @apply transition-colors duration-300;
}

/* Mejoras responsive adicionales */
@media (max-width: 640px) {
  .report-card {
    @apply p-5;
    @apply gap-4;
  }
  
  .card-header {
    @apply gap-4;
  }
  
  .title {
    @apply text-lg;
  }
  
  .description {
    @apply text-sm;
  }
  
  .card-footer {
    @apply pt-4;
  }
}

/* Animación de entrada suave */
@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.report-card {
  animation: slideInUp 0.4s ease-out;
}



/* Mejora para modo oscuro */
.dark .report-card {
  @apply shadow-2xl;
  box-shadow: 
    0 4px 6px -1px rgba(0, 0, 0, 0.3), 
    0 2px 4px -1px rgba(0, 0, 0, 0.2);
}

.dark .report-card:hover {
  box-shadow: 
    0 20px 25px -5px rgba(0, 0, 0, 0.4), 
    0 10px 10px -5px rgba(0, 0, 0, 0.2);
}


</style>