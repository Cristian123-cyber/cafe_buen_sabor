
<template>
  <li class="notification-item group" :class="{
    'is-unread': notification.is_read === 'UNREAD',
    'is-read': notification.is_read === 'READ'
  }">
    <div class="notification-main">
      <div class="notification-icon-wrapper">
        <div class="notification-icon" :class="iconData.bgClass">
          <component :is="iconData.icon" class="icon" :class="iconData.textClass" />
        </div>
        
      </div>

      <div class="notification-body">
        <div class="notification-header">
          <div class="title-group">
            <h3 class="notification-title">{{ notification.title }}</h3>
            <div class="metadata-inline">
              <span v-if="notification.table_number" class="table-badge">
                Mesa {{ notification.table_number }}
              </span>
            </div>
          </div>
          <time class="notification-time">{{ formattedTimestamp }}</time>
        </div>

        <p class="notification-message">{{ notification.message }}</p>

        <div v-if="notification.is_read === 'READ' && notification.employe_name" class="notification-footer">
          <span class="employee-info">
            Leído por {{ notification.employe_name }}
          </span>
        </div>
      </div>
    </div>

    <div class="notification-actions">
      <button
        v-if="notification.is_read === 'UNREAD'"
        class="mark-read-btn"
        aria-label="Marcar como leído"
        title="Marcar como leído"
        @click="handleMarkAsRead"
      >
        <i-mdi-check class="w-4 h-4" />
      </button>
      <div
        v-if="notification.is_read === 'UNREAD'"
        class="unread-indicator"
      ></div>
    </div>
  </li>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { formatDistanceToNow } from 'date-fns';
import { es } from 'date-fns/locale';

import { notificationIcons } from '../../iconSafeList.js';

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

const iconData = computed(() => {
  switch (props.notification.notification_type) {
    case 'ORDER_READY':
      return {
        icon: icons.OrderReady,
        bgClass: 'bg-success-light',
        textClass: 'text-success'
      };
    case 'ORDER_CONFIRMED':
      return {
        icon: icons.OrderConfirmed,
        bgClass: 'bg-info-light',
        textClass: 'text-info'
      };
    case 'ORDER_CANCELLED':
      return {
        icon: icons.OrderCancelled,
        bgClass: 'bg-error-light',
        textClass: 'text-error'
      };
    default:
      return {
        icon: icons.Default,
        bgClass: 'bg-secondary-dark',
        textClass: 'text-text-muted'
      };
  }
});

const formattedTimestamp = computed(() => {
  return formatDistanceToNow(new Date(props.notification.created_at), {
    addSuffix: true,
    locale: es
  });
});

const handleMarkAsRead = () => {
  emit('read', props.notification.id_notification);
};

onMounted(() => {
  console.log(props.notification)
})
</script>

<style scoped>
@reference "../../style.css";

/* --- Contenedor Principal --- */
.notification-item {
  @apply flex items-start justify-between gap-4;
  @apply px-5 py-4;
  @apply transition-all duration-200;
  @apply relative;
}

.notification-item.is-unread {
  @apply bg-info/5;
}

.notification-item:hover {
  @apply bg-surface-dark/60;
}

/* --- Main Content Container --- */
.notification-main {
  @apply flex gap-4 flex-1 min-w-0;
}

/* --- Icon Wrapper con línea vertical --- */
.notification-icon-wrapper {
  @apply flex flex-col items-center gap-2 pt-0.5;
  @apply flex-shrink-0;
}

.notification-icon {
  /* CAMBIO: De rounded-xl a rounded-full */
  @apply w-10 h-10 rounded-full flex items-center justify-center;
  @apply transition-all duration-300;
  @apply shadow-sm;
}

.notification-item:hover .notification-icon {
  @apply scale-110 shadow-md;
}

.icon {
  @apply w-5 h-5;
}


.notification-item:last-child .notification-line {
  @apply opacity-0;
}

.notification-item.is-unread .notification-line {
  /* CAMBIO: Color sólido para estado no leído */
  @apply bg-info/30;
}

/* --- Body Content --- */
.notification-body {
  @apply flex-1 min-w-0;
  @apply space-y-1.5; /* Ajustado ligeramente de space-y-2 */
}

.notification-header {
  /* CAMBIO: Header ahora es flex-col para apilar título y hora */
  @apply flex flex-col items-start gap-1;
}

.title-group {
  @apply flex items-center gap-2 flex-wrap;
  @apply flex-1 min-w-0;
}

.notification-title {
  @apply text-base font-semibold text-text;
  @apply leading-tight;
}

.metadata-inline {
  @apply flex items-center gap-2;
}

.table-badge {
  @apply inline-flex items-center;
  @apply text-xs font-medium;
  @apply bg-primary text-white;
  @apply px-2.5 py-1 rounded-lg;
  @apply whitespace-nowrap;
}

.notification-time {
  @apply text-xs text-text-muted;
  /* CAMBIO: Eliminado uppercase y tracking-wide para un look más suave */
  @apply font-medium;
  @apply whitespace-nowrap;
  @apply pt-0.5; /* Pequeño espacio superior */
}

.notification-message {
  @apply text-sm text-text-muted leading-relaxed;
  @apply pt-1; /* Espacio extra para separar del header */
}

/* --- Footer --- */
.notification-footer {
  @apply flex items-center gap-2;
  @apply pt-1;
}

.employee-info {
  @apply text-xs text-text-muted;
  @apply italic;
}

/* --- Acciones --- */
.notification-actions {
  @apply flex items-start gap-2.5;
  @apply pt-1 flex-shrink-0;
}

.mark-read-btn {
  @apply w-9 h-9 flex items-center justify-center rounded-full;
  @apply bg-success/20 text-success;
  @apply transition-all duration-200;
  @apply opacity-0 lg:group-hover:opacity-100;
  @apply border border-transparent;
}

@media (max-width: 1024px) {
  .mark-read-btn {
    @apply opacity-100;
  }
}

.mark-read-btn:hover {
  @apply bg-success/80 text-white border-success/20;
  
}

.mark-read-btn:active {
  @apply scale-95;
}

.unread-indicator {
  @apply w-2.5 h-2.5 bg-info rounded-full;
  @apply flex-shrink-0;
  @apply ring-4 ring-info/20;
  @apply mt-2.5;
  /* CAMBIO: Se oculta en hover para dar paso al botón */
  @apply transition-opacity duration-200;
  @apply lg:group-hover:opacity-0;
}

/* --- Estados --- */
.notification-item.is-read {
  @apply opacity-60;
}

.notification-item.is-read:hover {
  @apply opacity-80;
}

/* --- Responsive --- */
@media (max-width: 640px) {
  .notification-item {
    @apply px-4 py-3 gap-3;
  }

  .notification-main {
    @apply gap-3;
  }

  .notification-icon {
    @apply w-9 h-9 rounded-full; /* CAMBIO: Sincronizado a rounded-full */
  }

  .icon {
    @apply w-4.5 h-4.5;
  }

  .notification-title {
    @apply text-sm;
  }

  .notification-message {
    @apply text-xs pt-0; /* Ajuste responsive */
  }

  .notification-time {
    @apply text-[10px];
  }

  .table-badge {
    @apply text-[10px] px-2 py-0.5;
  }

  .employee-info {
    @apply text-[10px];
  }

  .notification-header {
    @apply gap-0.5; /* Ajuste responsive */
  }
}
</style>
