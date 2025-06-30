import { defineStore } from 'pinia';
import { ref, reactive } from 'vue';

// Generador de IDs únicos para cada notificación
const generateId = () => `notif_${Math.random().toString(36).substr(2, 9)}`;

export const useNotificationStore = defineStore('notifications', () => {
  const notifications = ref([]);

  /**
   * Añade una nueva notificación a la lista.
   * @param {object} notification - { message, type, duration }
   * type puede ser 'success', 'error', 'info', 'warning'
   */
  const addNotification = ({ message, type = 'info', duration = 5000 }) => {
    const id = generateId();
    notifications.value.push({ id, message, type });

    // Eliminar la notificación después de la duración especificada
    setTimeout(() => {
      removeNotification(id);
    }, duration);
  };

  /**
   * Elimina una notificación por su ID.
   */
  const removeNotification = (id) => {
    const index = notifications.value.findIndex(n => n.id === id);
    if (index !== -1) {
      notifications.value.splice(index, 1);
    }
  };

  return { notifications, addNotification, removeNotification };
});