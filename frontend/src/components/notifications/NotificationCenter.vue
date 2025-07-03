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
        <!-- Cabecera del panel (sin cambios) -->
        <header class="panel-header">
          <h3 class="panel-title">Notificaciones</h3>
          <button
            v-if="hasUnread"
            @click="handleMarkAllAsRead"
            class="mark-all-button"
          >
            Marcar todas como leídas
          </button>
        </header>

        <!-- NUEVO: Pestañas para filtrar -->
        <div class="panel-tabs">
          <button
            class="tab-button"
            :class="{ 'is-active': activeTab === 'unread' }"
            @click="setActiveTab('unread')"
          >
            No leídas
          </button>
          <button
            class="tab-button"
            :class="{ 'is-active': activeTab === 'all' }"
            @click="setActiveTab('all')"
          >
            Todas
          </button>
        </div>

        <!-- Cuerpo del panel -->
        <div class="panel-body">
          <!-- Estado de carga -->
          <div v-if="isLoading" class="state-feedback">
            <i-svg-spinners-ring-resize class="w-8 h-8 text-accent" />
            <p>Cargando...</p>
          </div>

          <!-- Estado vacío (ahora con mensaje dinámico) -->
          <div
            v-else-if="!filteredNotifications.length"
            class="state-feedback"
          >
            <i-mdi-bell-off-outline class="w-8 h-8 text-text-muted" />
            <p>{{ emptyStateMessage }}</p>
          </div>

          <!-- Lista de notificaciones (ahora usa la lista filtrada) -->
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
import { useToasts } from '../../composables/useToast'; // Mantén tu composable
import { useAlert } from '../../composables/useAlert'; // Mantén tu composable
const alert = useAlert();


const { addToast } = useToasts(); // Mantén tu instancia de toast

// --- Estado local del componente ---
const isOpen = ref(false);
const notificationCenterRef = ref(null);
const activeTab = ref('unread'); // 'unread' o 'all'

// --- Integración con Pinia Store ---
const notificationStore = useNotificationStore();
const unreadCount = computed(() => notificationStore.unreadCount);
const notifications = computed(() => notificationStore.notifications);
const isLoading = computed(() => notificationStore.isLoading);
const hasUnread = computed(() => notificationStore.hasUnread);

// --- LÓGICA DE FILTRADO (COMPUTEDS) ---

/**
 * Filtra las notificaciones basándose en la pestaña activa.
 * @returns {Array} La lista de notificaciones a mostrar.
 */
const filteredNotifications = computed(() => {
  if (activeTab.value === 'unread') {
    // Devuelve solo las no leídas
    return notifications.value.filter(n => !n.is_read);
  }
  // Para 'all', devuelve todas
  return notifications.value;
});

/**
 * Mensaje a mostrar cuando no hay notificaciones en la pestaña actual.
 * @returns {string}
 */
const emptyStateMessage = computed(() => {
  return activeTab.value === 'unread'
    ? 'No tienes notificaciones no leídas'
    : 'No hay notificaciones para mostrar';
});

// --- Funciones de control ---
const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
};
const closeDropdown = () => {
  isOpen.value = false;
};
const setActiveTab = (tabName) => {
  activeTab.value = tabName;
};

// --- Manejadores de eventos ---
const handleMarkAsRead = async (id) => {
  //notificationStore.markNotificationAsRead(id);
 /*   */ // Tu lógica de toast sigue siendo válida aquí

  const isConfirmed = await alert.show({
    variant: 'warning',
    title: '¿Confirmar Pedido?',
    message: 'El pedido se enviará a la cocina y no podrá modificarse. ¿Deseas continuar?',
    confirmButtonText: 'Sí, enviar',
    cancelButtonText: 'Aún no',
  });

  if (isConfirmed){
    addToast({
    message: 'Notification marcada como leida',
    title: 'Exito',
    type: 'success',
    duration: 3000
  });

  }
   setTimeout(closeDropdown, 300);
};

const handleMarkAllAsRead = () => {
  notificationStore.markAllNotificationsAsRead();
};

// --- Ciclo de vida (sin cambios, excepto el watch) ---
watch(isOpen, (newValue) => {
  if (newValue) {
    // Al abrir, siempre reseteamos a la pestaña 'unread' y cargamos si es necesario
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

/* Estilos existentes (sin cambios) */
.notification-panel { @apply absolute top-full right-0 mt-2 w-screen max-w-sm bg-surface rounded-lg shadow-2xl border border-border-light origin-top-right; }
.panel-header { @apply flex items-center justify-between p-4 border-b border-border-light; }
.panel-title { @apply text-base font-semibold text-text; }
.mark-all-button { @apply text-xs font-semibold text-accent hover:text-accent-dark transition-colors duration-200; }
.panel-body { @apply max-h-[60vh] overflow-y-auto; } /* Reducimos un poco la altura para dar espacio a las tabs */
.state-feedback { @apply flex flex-col items-center justify-center gap-4 p-8 text-text-muted; }
.notification-list { @apply list-none p-0 m-0; }

/* NUEVOS ESTILOS PARA LAS PESTAÑAS */
.panel-tabs {
  @apply flex items-stretch border-b border-border-light;
}
.tab-button {
  @apply flex-1 py-2.5 text-center text-sm font-medium text-text-muted;
  @apply border-b-2 border-transparent; /* Borde inferior por defecto */
  @apply transition-all duration-200 ease-in-out;
  @apply hover:bg-hover;
}
.tab-button.is-active {
  @apply text-accent border-accent; /* Estilo para la pestaña activa */
}
</style>