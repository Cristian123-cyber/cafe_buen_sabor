// src/stores/notifications.js

import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { notificationService } from '../services/notification.js';

export const useNotificationStore = defineStore('notifications', () => {
  // --- STATE ---
  const notifications = ref([]);
  const unreadCount = ref(0);
  const isLoading = ref(false);
  const pollingIntervalId = ref(null);

  // --- GETTERS ---
  const hasUnread = computed(() => unreadCount.value > 0);

  // --- ACTIONS ---

  /**
   * Obtiene la lista completa de notificaciones y actualiza el estado.
   * Se usa para poblar el dropdown de notificaciones.
   */
  const fetchNotifications = async () => {
    isLoading.value = true;
    try {
      const response = await notificationService.getNotifications();
      // Asumiendo que la API devuelve { data: [...], meta: { unread_count: X } }
      notifications.value = response.data;
      unreadCount.value = response.meta.unread_count;
    } catch (error) {
      // El manejo de errores ya se loguea en el servicio, aquí podrías
      // mostrar una notificación al usuario si fuese necesario.
    } finally {
      isLoading.value = false;
    }
  };

  /**
   * Obtiene solo el contador de no leídas. Es la función que se usará para el polling.
   * Es más ligera que traer toda la lista cada vez.
   */
  const fetchUnreadCount = async () => {
    try {
      const data = await notificationService.getUnreadCount();
      unreadCount.value = data.count;
    } catch (error) {
      // Si el polling falla, no interrumpimos al usuario, el error ya se logueó.
    }
  };

  /**
   * Marca una notificación como leída y actualiza el estado localmente
   * para una respuesta instantánea en la UI (actualización optimista).
   */
  const markNotificationAsRead = async (notificationId) => {
    const notification = notifications.value.find(n => n.id === notificationId);
    // Si la notificación ya está leída o no existe, no hacemos nada.
    if (!notification || notification.is_read) {
      return;
    }
    
    try {
      await notificationService.markAsRead(notificationId);
      // Actualización optimista: asumimos éxito y actualizamos la UI inmediatamente.
      notification.is_read = true;
      unreadCount.value = Math.max(0, unreadCount.value - 1);
    } catch (error) {
      // Si la API falla, podríamos revertir el estado si fuera crítico,
      // pero por ahora solo lo logueamos.
    }
  };

  /**
   * Marca todas las notificaciones como leídas y actualiza el estado localmente.
   */
  const markAllNotificationsAsRead = async () => {
    if (unreadCount.value === 0) return;
    
    try {
      await notificationService.markAllAsRead();
      // Actualización optimista
      unreadCount.value = 0;
      notifications.value.forEach(n => {
        if (!n.is_read) {
          n.is_read = true;
        }
      });
    } catch (error) {
       // El error ya se logueó en la capa de servicio.
    }
  };

  /**
   * Inicia el polling para verificar nuevas notificaciones cada 15 segundos.
   * Debe llamarse cuando un usuario logueado (ej. Mesero) entra a su dashboard.
   */
  const startPolling = () => {
    // Prevenir múltiples intervalos si se llama varias veces
    if (pollingIntervalId.value) return;

    // Hacemos una llamada inicial para tener el dato lo antes posible
    fetchUnreadCount();

    pollingIntervalId.value = setInterval(() => {
      fetchUnreadCount();
    }, 15000); // 15 segundos
    console.log('Notification polling started.');
  };

  /**
   * Detiene el polling. Debe llamarse cuando el usuario se desloguea
   * o sale de la vista que requiere notificaciones en tiempo real.
   */
  const stopPolling = () => {
    if (pollingIntervalId.value) {
      clearInterval(pollingIntervalId.value);
      pollingIntervalId.value = null;
      console.log('Notification polling stopped.');
    }
  };

  return {
    // State
    notifications,
    unreadCount,
    isLoading,
    // Getters
    hasUnread,
    // Actions
    fetchNotifications,
    fetchUnreadCount,
    markNotificationAsRead,
    markAllNotificationsAsRead,
    startPolling,
    stopPolling,
  };
});