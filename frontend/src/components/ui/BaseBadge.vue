<template>
  <span :class="badgeClasses">
    <!-- Contenido del badge (icono + texto) -->
    <template v-if="!dot">
      <span class="shrink-0" v-if="$slots.icon">
        <slot name="icon"></slot>
      </span>
      <span :class="{ 'ml-1.5': $slots.icon }">
        <slot></slot>
      </span>
    </template>
    
    <!-- Para el modo punto, se puede añadir texto para lectores de pantalla -->
    <span v-if="dot" class="sr-only">
      <slot name="counter">Notificación</slot>
    </span>
  </span>
</template>

<script setup>
import { computed } from 'vue';

/**
 * @name BaseBadge
 * @description Componente versátil para mostrar estados, contadores o notificaciones.
 * @example
 * <BaseBadge color="success">Activo</BaseBadge>
 * <BaseBadge color="error" variant="outline">Error</BaseBadge>
 * <BaseBadge color="accent" dot />
 * <BaseBadge color="info">
 *   <template #icon><i-mdi-information /></template>
 *   Información
 * </BaseBadge>
 */

const props = defineProps({
  /**
   * Define la paleta de colores semántica del badge.
   * Se mapea a las variables de color definidas en app.css.
   */
  color: {
    type: String,
    default: 'neutral',
    validator: (value) => 
      ['primary', 'secondary', 'accent', 'success', 'warning', 'error', 'info', 'neutral'].includes(value),
  },
  /**
   * Si es `true`, el badge se renderiza como un pequeño punto,
   * ideal para indicadores de notificación sin texto.
   */
  dot: {
    type: Boolean,
    default: false,
  }
});

// Calcula las clases CSS dinámicamente basadas en las props
const badgeClasses = computed(() => [
  'badge', // Clase base con estilos comunes
  props.dot ? 'badge-dot' : 'badge-content', // Clase para tamaño (punto o contenido)
  `badge-${props.color}-solid` // Clase para el estilo de color/variante
]);
</script>

<style scoped>
@reference "../../style.css";


/* --- 1. Clase Base --- */
/* Estilos comunes a todos los badges con efecto de hover */
.badge {
  @apply inline-flex items-center justify-center rounded-full font-semibold;
  /* Transiciones suaves para el cambio de tamaño y color */
  @apply transition-all duration-200 ease-out;
  /* Efecto de crecimiento al pasar el cursor */
  @apply hover:scale-105;
  /* Promueve el elemento a una capa de GPU para un transform más suave */
  @apply transform-gpu;
  /* Para accesibilidad en el foco */
  @apply focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent;
}

/* --- 2. Clases de Tamaño --- */
/* Estilos para badges con contenido (texto/icono) */
.badge-content {
  @apply px-3 py-1 text-xs;
}
/* Estilos para badges en modo punto */
.badge-dot {
  @apply h-2.5 w-2.5;
}

/* --- 3. Clases de Color y Variante --- */
/* Aquí definimos todas las combinaciones posibles. Esto mantiene la lógica fuera de JS */
/* y hace que el sistema sea más predecible y fácil de depurar. */

/* == Primary == */
.badge-primary-solid { @apply bg-primary text-text-light; }


/* == Secondary == */
.badge-secondary-solid { @apply bg-secondary-dark text-text; }


/* == Accent == */
.badge-accent-solid { @apply bg-accent text-primary; }


/* == Success == */
.badge-success-solid { @apply bg-success text-white; }


/* == Warning == */
.badge-warning-solid { @apply bg-warning text-primary; }


/* == Error == */
.badge-error-solid { @apply bg-error text-white; }


/* == Info == */
.badge-info-solid { @apply bg-info text-white; }


/* == Neutral (Default) == */
.badge-neutral-solid { @apply bg-primary-dark text-text-light; }





</style>