<template>
  <li
    class="notification-item"
    :class="{ 'is-unread': !notification.is_read }"
  >
    <!-- Icono y su fondo (sin cambios) -->
    <div class="item-icon-wrapper" :class="iconData.bgClass">
      <component
        :is="iconData.icon"
        class="item-icon"
        :class="iconData.textClass"
      />
    </div>

    <!-- Contenido del texto -->
    <div class="item-content">
      <p class="item-title">{{ notification.title }}</p>
      <p class="item-message">{{ notification.message }}</p>
      <time class="item-timestamp">{{ formattedTimestamp }}</time>
    </div>

    <!-- NUEVO: Botón para marcar como leído -->
    <div class="item-actions">
      <button
        v-if="!notification.is_read"
        class="mark-as-read-button"
        aria-label="Marcar como leído"
        @click="handleMarkAsRead"
      >
        <i-mdi-check class="w-5 h-5" />
      </button>
    </div>
  </li>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { formatDistanceToNow } from 'date-fns';
import { es } from 'date-fns/locale';

import { notificationIcons } from '../../iconSafeList.js'; 

// Para simplificar, definimos los iconos aquí directamente.
// Si usas una safelist, el import de arriba es correcto.
const icons = {
  OrderReady: notificationIcons.OrderReady,
  OrderConfirmed: notificationIcons.OrderConfirmed,
  OrderCancelled: notificationIcons.OrderCancelled,
  Default: notificationIcons.Default,
};

const props = defineProps({
  notification: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['read']);
const router = useRouter();

// iconData y formattedTimestamp no cambian, los omito por brevedad...
const iconData = computed(() => {
  // ... lógica sin cambios
  switch (props.notification.type) {
    case 'ORDER_READY': return { icon: icons.OrderReady, bgClass: 'bg-success-light', textClass: 'text-success' };
    case 'ORDER_CONFIRMED': return { icon: icons.OrderConfirmed, bgClass: 'bg-info-light', textClass: 'text-info' };
    case 'ORDER_CANCELLED': return { icon: icons.OrderCancelled, bgClass: 'bg-error-light', textClass: 'text-error' };
    default: return { icon: icons.Default, bgClass: 'bg-surface-dark', textClass: 'text-text' };
  }
});

const formattedTimestamp = computed(() => {
  // ... lógica sin cambios
  return formatDistanceToNow(new Date(props.notification.created_at), { addSuffix: true, locale: es });
});


/**
 * LÓGICA ACTUALIZADA: Separamos la navegación de la acción de marcar como leído.
 */

// Se dispara al hacer clic en el botón de check
const handleMarkAsRead = () => {
  // .stop en el template previene que este click se propague al <li>
  emit('read', props.notification.id);
};


</script>

<style scoped>
@reference "../../style.css";

.notification-item {
  @apply flex items-start gap-3 p-4;
  @apply border-b border-border-light;
  @apply transition-colors duration-200 ease-in-out;
  
  /*  [CORRECCIÓN 1]: Es crucial decirle al <li> que no permita que sus hijos se desborden. */
  @apply overflow-hidden;
}
.notification-item:not(.is-unread) {
  /* Si ya está leída, no necesita ser un puntero de cursor */
  @apply cursor-default;
}
.notification-item.is-unread {
  @apply cursor-pointer hover:bg-hover;
}

.notification-item:last-child {
  @apply border-b-0;
}
.notification-item.is-unread {
  @apply bg-surface-dark;
}

.item-icon-wrapper {
  /* Este elemento no debe encogerse */
  @apply flex-shrink-0;
  @apply w-10 h-10 rounded-full flex items-center justify-center;
}

.item-content {
  /* [CORRECCIÓN 2]: La clave del problema. */
  /* Le decimos que crezca para ocupar el espacio disponible */
  @apply flex-grow;
  /* PERO, también le damos un ancho mínimo de 0. Esto es un "hack" conocido de flexbox */
  /* que le obliga a respetar los límites del contenedor padre en lugar de calcular su tamaño */
  /* basándose en su contenido interno. Es la solución más común a este problema. */
  @apply min-w-0;
}

.item-icon { @apply w-6 h-6; }

.item-title {
  @apply font-semibold text-sm text-text;
  /* [CORRECCIÓN 3]: Asegurarnos de que el texto se trunca si es muy largo */
  @apply truncate; /* o `whitespace-normal` si prefieres que salte de línea */
}

.item-message {
  @apply text-sm text-text-muted mt-0.5;
  /* [CORRECCIÓN 4]: Lo mismo para el mensaje */
  @apply truncate; /* o `whitespace-normal` */
}
.item-timestamp { @apply text-xs text-text-muted mt-1 block; }


.mark-as-read-button {
  @apply w-8 h-8 flex items-center justify-center;
  @apply rounded-full text-text-muted;
  @apply transition-all duration-200 ease-in-out;
  @apply hover:bg-success-light hover:text-success focus:outline-none focus:ring-2 focus:ring-success;
}

.item-actions {
  /* Este elemento tampoco debe encogerse. */
  @apply flex-shrink-0 ml-auto self-center;
}

</style>