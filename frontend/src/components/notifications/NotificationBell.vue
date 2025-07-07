<template>
  <button type="button" class="notification-bell" aria-label="Ver notificaciones">
    <!-- Icono de la campana -->
    <span ref="bellRef" class="notification-bell-icon-wrapper">
      <i-mdi-bell class="notification-bell-icon" />
    </span>

    <!-- 
      Badge de contador integrado.
      Se muestra solo si hay notificaciones no leídas.
      Añadimos `v-if` para evitar renderizar el span si no es necesario.
      Usamos `v-text` para el contenido por ser ligeramente más performante
      para texto simple que la interpolación {{ }}.
    -->
    <span v-if="hasUnreadNotifications" v-text="displayCount" class="notification-badge">
    </span>
  </button>
</template>

<script setup>
import { computed, ref, onBeforeUnmount, onMounted, watch } from 'vue';

/**
 * @name NotificationBell
 * @description Un botón de campana que muestra un contador de notificaciones no leídas.
 * @example
 * <!-- Sin notificaciones -->
 * <NotificationBell :count="0" />
 * <!-- Con 3 notificaciones -->
 * <NotificationBell :count="3" @click="openNotifications" />
 * <!-- Con 12 notificaciones (mostrará "9+") -->
 * <NotificationBell :count="12" :max-count="9" />
 */

const props = defineProps({
  /**
   * El número total de notificaciones no leídas.
   */
  count: {
    type: Number,
    required: true,
    default: 0,
    validator: (value) => value >= 0, // El contador no puede ser negativo
  },
  /**
   * El número máximo a mostrar en el badge. Si `count` excede este valor,
   * se mostrará como "maxCount+".
   */
  maxCount: {
    type: Number,
    default: 9,
  },
});

/**
 * Determina si se deben mostrar notificaciones.
 * Es más semántico que simplemente `props.count > 0`.
 * @returns {boolean}
 */
const hasUnreadNotifications = computed(() => props.count > 0);

/**
 * Calcula el texto que se mostrará en el badge.
 * Si `count` supera `maxCount`, muestra "maxCount+".
 * @returns {string}
 */
const displayCount = computed(() => {
  if (props.count > props.maxCount) {
    return `${props.maxCount}+`;
  }
  return props.count.toString();
});

const bellRef = ref(null);
let shakeInterval = null;

onMounted(() => {
  // Ejecuta cada X segundos si hay notificaciones
  watch(hasUnreadNotifications, (newVal) => {
    if (newVal) {
      startShaking();
    } else {
      stopShaking();
    }
  });

  if (hasUnreadNotifications.value) {
    startShaking();
  }
});

onBeforeUnmount(() => {
  stopShaking();
});

function startShaking() {
  stopShaking(); // Limpia cualquier anterior
  shakeInterval = setInterval(() => {
    if (bellRef.value) {
      bellRef.value.classList.add('bell-shake');
      setTimeout(() => {
        bellRef.value?.classList.remove('bell-shake');
      }, 600); // coincide con la duración del keyframe
    }
  }, 6000); // cada 6 segundos
}

function stopShaking() {
  if (shakeInterval) {
    clearInterval(shakeInterval);
    shakeInterval = null;
  }
}

</script>

<style scoped>
/* 
  Referencia al archivo de tema central para poder usar @apply con 
  las variables CSS personalizadas (--color-status-error, etc.).
  Asegúrate de que la ruta sea correcta desde este componente.
*/
@reference "../../style.css";

.notification-bell-icon-wrapper {
  @apply relative inline-flex;
}
.notification-bell {
  @apply relative inline-flex items-center justify-center;
  /* Espaciado y forma */
  @apply p-2 rounded-full;
  /* Transición suave para el color de fondo */
  @apply transition-colors duration-200 ease-in-out;
  /* Efecto de hover (usando variables del tema) */
  @apply hover:bg-primary-dark/10;
  /* Estilos de foco para accesibilidad (usando variables del tema) */
  @apply focus:outline-none;
}

/* Badge de notificaciones rediseñado */
.notification-badge {
  @apply absolute -top-1.5 -right-1.5 z-20;
  @apply min-w-6 h-6 px-1 rounded-full;
  @apply text-xs font-bold text-white flex items-center justify-center;
  @apply border border-white/20 shadow-lg;
  @apply transform transition-all duration-300 ease-out;

  /* Gradiente + efecto glassmorphism sutil */
  background: linear-gradient(135deg, #ff4e50, #f9d423);
  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px);

  /* Glow externo suave */
  box-shadow:
    0 0 0 2px rgba(255, 255, 255, 0.3),
    0 2px 6px rgba(255, 78, 80, 0.6),
    0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Pulso renovado */
.badge-pulse {
  @apply absolute inset-0 rounded-full;
  background: rgba(255, 78, 80, 0.4);
  animation: pulse-ring-new 2s ease-out infinite;
}

@keyframes pulse-ring-new {
  0% {
    transform: scale(0.9);
    opacity: 0.8;
  }

  70% {
    transform: scale(1.4);
    opacity: 0.1;
  }

  100% {
    transform: scale(1.6);
    opacity: 0;
  }
}

/* Hover con glow */
.notification-bell:hover .notification-badge {
  transform: scale(1.1);
  box-shadow:
    0 0 0 3px rgba(255, 255, 255, 0.3),
    0 6px 16px rgba(255, 78, 80, 0.6),
    0 3px 6px rgba(0, 0, 0, 0.25);
}

/* Active */
.notification-bell:active .notification-badge {
  transform: scale(0.95);
  filter: brightness(0.95);
}

/* Animación inicial */
.notification-badge {
  animation: badge-appear-premium 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes badge-appear-premium {
  0% {
    transform: scale(0);
    opacity: 0;
  }

  50% {
    transform: scale(1.3);
    opacity: 0.6;
  }

  100% {
    transform: scale(1);
    opacity: 1;
  }
}

.notification-bell-icon {
  @apply w-6 h-6 text-text-muted relative z-10 transition-colors duration-300 ease-in-out;
   color: #facc15;
  filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.15));

  
}

.notification-bell:hover .notification-bell-icon {
  color: #fde047;
  filter: drop-shadow(0 3px 6px rgba(250, 204, 21, 0.5));
}



/* Animación de sacudida */
@keyframes bell-shake-subtle {

  0%,
  100% {
    transform: translateX(0);
  }

  20% {
    transform: translateX(-2px);
  }

  40% {
    transform: translateX(2px);
  }

  60% {
    transform: translateX(-1px);
  }

  80% {
    transform: translateX(1px);
  }
}

/* Clase que se agrega dinámicamente */
.bell-shake {
  animation: bell-shake-subtle 0.6s ease-in-out;
}
</style>