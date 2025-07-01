<template>
  <button
    type="button"
    class="notification-bell"
    aria-label="Ver notificaciones"
  >
    <!-- Icono de la campana -->
    <i-mdi-bell class="w-6 h-6 text-text" />

    <!-- Badge de notificación (usa nuestro BaseBadge) -->
    <!-- Se muestra solo si hay notificaciones (count > 0) -->
    <BaseBadge
      v-if="showBadge"
      color="error"
      dot
      class="absolute top-1 right-1 transform-gpu"
    >
      
    </BaseBadge>
  </button>
</template>

<script setup>
import { computed } from 'vue';
import BaseBadge from '../ui/BaseBadge.vue';

/**
 * @name NotificationBell
 * @description Un botón de campana reutilizable que muestra un indicador de notificaciones.
 * @example
 * <!-- Sin notificaciones -->
 * <NotificationBell :count="0" />
 * <!-- Con 3 notificaciones -->
 * <NotificationBell :count="3" @click="openNotifications" />
 * <!-- Con muchas notificaciones -->
 * <NotificationBell :count="12" :max-count="9" />
 * <!-- Como un simple punto indicador -->
 * <NotificationBell :count="1" :show-dot="true" />
 */

const props = defineProps({
  /**
   * El número total de notificaciones no leídas.
   */
  count: {
    type: Number,
    default: 0,
    required: true,
  },
  /**
   * Si es `true`, muestra un simple punto en lugar del contador numérico.
   * Útil para un indicador más sutil.
   */
  showDot: {
    type: Boolean,
    default: false,
  },
  /**
   * El número máximo a mostrar. Si `count` excede este valor,
   * se mostrará como "maxCount+".
   */
  maxCount: {
    type: Number,
    default: 9,
  },
});

// Determina si el badge debe ser visible
const showBadge = computed(() => props.count > 0);


</script>

<style scoped>
@reference "../../style.css";





.notification-bell {
  @apply relative inline-flex items-center justify-center bg-transparent;
  /* Espaciado y forma */
  @apply p-2 rounded-full;
  /* Transición suave para el color de fondo */
  @apply transition-colors duration-200 ease-in-out;
  /* Efecto de hover solicitado */
  @apply hover:bg-surface-dark/15;
  /* Estilos de foco para accesibilidad */
  @apply focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent;
}

/* 
  El BaseBadge ya tiene su propia transición de `scale`, por lo que al hacer
  hover en el botón, el badge también reaccionará.
  El `transform-gpu` en el badge en el template ayuda a que la animación 
  de escala sea más fluida.
*/
</style>