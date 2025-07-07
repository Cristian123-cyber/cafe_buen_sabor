<template>
  <div ref="notificationCenterRef" class="relative">
    <NotificationBell
      :count="unreadCount"
      .maxCount="3"
      @click="toggleDropdown"
      :aria-expanded="isOpen"
    />

    <transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <div v-if="isOpen" class="notification-panel">
        <!-- Cabecera del panel -->
        <header class="panel-header">
          <h3 class="panel-title">Notificaciones</h3>
        </header>

        <!-- Pestañas para filtrar -->
        <div class="panel-tabs">
          <button
            class="tab-button"
            :class="{ 'is-active': activeTab === 'unread' }"
            @click="setActiveTab('unread')"
          >
            <span class="tab-text">No leídas</span>
            <span class="tab-text-short">Sin leer</span>
          </button>
          <button
            class="tab-button"
            :class="{ 'is-active': activeTab === 'all' }"
            @click="setActiveTab('all')"
          >
            <span class="tab-text">Todas</span>
            <span class="tab-text-short">Todas</span>
          </button>
        </div>

        <!-- Cuerpo del panel -->
        <div class="panel-body">
          <!-- Estado de carga -->
          <div v-if="isLoading" class="state-feedback">
            <i-svg-spinners-ring-resize class="w-6 h-6 sm:w-8 sm:h-8 text-accent" />
            <p class="text-sm sm:text-base">Cargando...</p>
          </div>

          <!-- Estado vacío -->
          <div
            v-else-if="!filteredNotifications.length"
            class="state-feedback"
          >
            <i-mdi-bell-off-outline class="w-6 h-6 sm:w-8 sm:h-8 text-text-muted" />
            <p class="text-sm sm:text-base">{{ emptyStateMessage }}</p>
          </div>

          <!-- Lista de notificaciones -->
          <ul v-else class="notification-list">
            <NotificationItem
              v-for="notification in filteredNotifications"
              :key="notification.id"
              :notification="notification"
              @read="handleMarkAsRead"
            />
          </ul>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useNotificationStore } from '../../stores/notificationsS';
import { useToasts } from '../../composables/useToast';
import { useAlert } from '../../composables/useAlert';
const alert = useAlert();

const { addToast } = useToasts();

// Estado local del componente
const isOpen = ref(false);
const notificationCenterRef = ref(null);
const activeTab = ref('unread');

// Integración con Pinia Store
const notificationStore = useNotificationStore();
const unreadCount = computed(() => notificationStore.unreadCount);
const notifications = computed(() => notificationStore.notifications);
const isLoading = computed(() => notificationStore.isLoading);
const hasUnread = computed(() => notificationStore.hasUnread);

// Lógica de filtrado
const filteredNotifications = computed(() => {
  if (activeTab.value === 'unread') {
    return notifications.value.filter(n => !n.is_read);
  }
  return notifications.value;
});

const emptyStateMessage = computed(() => {
  return activeTab.value === 'unread'
    ? 'No tienes notificaciones no leídas'
    : 'No hay notificaciones para mostrar';
});

// Funciones de control
const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
};
const closeDropdown = () => {
  isOpen.value = false;
};
const setActiveTab = (tabName) => {
  activeTab.value = tabName;
};

// Manejadores de eventos
const handleMarkAsRead = async (id) => {
  const isConfirmed = await alert.show({
    variant: 'warning',
    title: '¿Confirmar Pedido?',
    message: 'El pedido se enviará a la cocina y no podrá modificarse. ¿Deseas continuar?',
    confirmButtonText: 'Sí, enviar',
    cancelButtonText: 'Aún no',
  });

  if (isConfirmed) {
    addToast({
      message: 'Notification marcada como leida',
      title: 'Exito',
      type: 'success',
      duration: 3000
    });
    
    
  }
  setTimeout(closeDropdown, 300);
};

// Ciclo de vida
watch(isOpen, (newValue) => {
  if (newValue) {
    activeTab.value = 'unread';
    notificationStore.fetchNotifications();
  }
});

const handleClickOutside = (event) => {
  if (notificationCenterRef.value && !notificationCenterRef.value.contains(event.target)) {
    closeDropdown();
  }
};

onMounted(() => {
  notificationStore.startPolling();
  document.addEventListener('mousedown', handleClickOutside);
});

onUnmounted(() => {
  notificationStore.stopPolling();
  document.removeEventListener('mousedown', handleClickOutside);
});
</script>

<style scoped>
@reference "../../style.css";

/* Panel principal - Responsive */
.notification-panel {
  @apply absolute top-full bg-surface rounded-lg shadow-2xl border border-border-light;
  @apply z-50; /* Asegurar que esté por encima de otros elementos */
  
  /* Móvil: ocupa casi todo el ancho con márgenes laterales */
  @apply w-[calc(100vw-1.5rem)] max-w-sm;
  
  /* Desktop: ancho fijo más pequeño */
  @apply sm:w-96 sm:max-w-md;
  
  /* Posicionamiento responsive */
  @apply right-0 sm:right-0; /* Mantener alineado a la derecha en desktop */
  
  /* Ajustar posición en móvil para centrar mejor */
  @apply -translate-x-[calc(50vw-50%-1rem)] sm:translate-x-0;
  
  /* Altura máxima responsive */
  @apply max-h-[70vh] sm:max-h-[80vh];
  @apply mt-2;
  
  /* Origin responsive */
  @apply origin-top-right sm:origin-top-right;
}

/* Cabecera del panel */
.panel-header {
  @apply flex items-center justify-between border-b border-border-light;
  @apply p-3 sm:p-4; /* Padding responsive */
}

.panel-title {
  @apply font-semibold text-text;
  @apply text-sm sm:text-base; /* Tamaño responsive */
}

/* Pestañas - Responsive */
.panel-tabs {
  @apply flex items-stretch border-b border-border-light;
  @apply min-h-[44px]; /* Altura mínima para touch targets */
}

.tab-button {
  @apply flex-1 py-2 px-2 text-center font-medium text-text-muted;
  @apply border-b-2 border-transparent;
  @apply transition-all duration-200 ease-in-out;
  @apply hover:bg-hover;
  @apply text-xs sm:text-sm; /* Tamaño responsive */
  @apply min-h-[44px] flex items-center justify-center; /* Touch target */
}

.tab-button.is-active {
  @apply text-accent border-accent;
}

/* Texto de pestañas responsive */
.tab-text {
  @apply hidden sm:inline;
}

.tab-text-short {
  @apply inline sm:hidden;
}

/* Cuerpo del panel */
.panel-body {
  @apply overflow-y-auto;
  @apply max-h-[50vh] sm:max-h-[60vh]; /* Altura máxima responsive */
}

/* Estado de feedback */
.state-feedback {
  @apply flex flex-col items-center justify-center gap-3 sm:gap-4 text-text-muted;
  @apply p-6 sm:p-8; /* Padding responsive */
}

/* Lista de notificaciones */
.notification-list {
  @apply list-none p-0 m-0;
}

/* Media queries adicionales para casos específicos */
@media (max-width: 390px) {
  .notification-panel {
    /* En pantallas muy pequeñas, centra el panel */
    @apply w-[calc(100vw-1rem)];
    /* Fórmula para centrar: mover hacia la izquierda la mitad del viewport menos la mitad del elemento */
    transform: translateX(calc(-50vw + 50% + 0.5rem));
  }
}

@media (min-width: 391px) and (max-width: 480px) {
  .notification-panel {
    /* En pantallas pequeñas pero no ultra pequeñas */
    @apply w-[calc(100vw-1.5rem)];
    transform: translateX(calc(-50vw + 50% + 0.75rem));
  }
}

@media (max-width: 480px) {
  .panel-header {
    @apply p-2.5;
  }
  
  .panel-title {
    @apply text-xs;
  }
  
  .tab-button {
    @apply text-xs py-1.5;
  }
}

/* Mejoras para dispositivos con hover limitado (touch) */
@media (hover: none) {
  .tab-button:hover {
    @apply bg-transparent;
  }
  
  .tab-button:active {
    @apply bg-hover;
  }
}
</style>