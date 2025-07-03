// src/services/notifications.js

import api from "./api.js";

/**
 * Servicio para gestionar las notificaciones de la API.
 */
export const notificationService = {
  /**
   * Obtiene la lista completa de notificaciones del usuario actual.
   * @returns {Promise<Object>} La respuesta de la API con los datos y la meta-información.
   */
  getNotifications: async () => {
    try {
      //const response = await api.get('/notifications');
      // La API devuelve un objeto con { data: [...], meta: {...} }

      const response = {
        data: [
          // --- Notificaciones NO LEÍDAS ---

          {
            id: 1,
            type: "ORDER_READY",
            title: "Pedido listo para recoger",
            message: "El pedido #101 para la Mesa 5 está listo.",
            is_read: false,
            created_at: new Date(Date.now() - 2 * 60 * 1000).toISOString(), // Hace 2 minutos
            metadata: {
              order_id: 101,
              table_id: 5, // Metadato para redirigir al mesero
            },
          },
          {
            id: 2,
            type: "ORDER_READY",
            title: "Pedido listo para recoger",
            message: "El pedido #102 para la Mesa 3 está listo.",
            is_read: false,
            created_at: new Date(Date.now() - 15 * 60 * 1000).toISOString(), // Hace 15 minutos
            metadata: {
              order_id: 102,
              table_id: 3,
            },
          },
          {
            id: 3,
            type: "ORDER_CONFIRMED",
            title: "Nuevo pedido en cocina",
            message: "El pedido #105 de la Mesa 8 ha sido confirmado.",
            is_read: false,
            created_at: new Date(Date.now() - 35 * 60 * 1000).toISOString(), // Hace 35 minutos
            metadata: {
              order_id: 105,
              table_id: 8,
            },
          },

          // --- Notificaciones LEÍDAS ---

          {
            id: 4,
            type: "ORDER_CANCELLED",
            title: "Pedido cancelado",
            message: "El cliente de la Mesa 2 ha cancelado un ítem.",
            is_read: true,
            created_at: new Date(Date.now() - 2 * 60 * 60 * 1000).toISOString(), // Hace 2 horas
            metadata: {
              order_id: 99,
              table_id: 2,
            },
          },
          {
            id: 5,
            type: "ORDER_READY",
            title: "Pedido listo para recoger",
            message: "El pedido #98 para la Mesa 1 está listo.",
            is_read: true,
            created_at: new Date(Date.now() - 5 * 60 * 60 * 1000).toISOString(), // Hace 5 horas
            metadata: {
              order_id: 98,
              table_id: 1,
            },
          },
          {
            id: 6,
            type: "SYSTEM_ALERT", // Un tipo de notificación genérico/diferente
            title: "Aviso del sistema",
            message: 'El menú de "Bebidas Calientes" ha sido actualizado.',
            is_read: true,
            created_at: new Date(
              Date.now() - 24 * 60 * 60 * 1000
            ).toISOString(), // Ayer
            metadata: {}, // Sin metadatos accionables
          },
          {
            id: 7,
            type: "ORDER_READY",
            title: "Pedido listo para recoger",
            message: "El pedido #97 para la Mesa 7 está listo.",
            is_read: true,
            created_at: new Date(
              Date.now() - 2 * 24 * 60 * 60 * 1000
            ).toISOString(), // Hace 2 días
            metadata: {
              order_id: 97,
              table_id: 7,
            },
          },
        ],
        meta: {
          unread_count: 4,
        },
      };
      return response;
    } catch (error) {
      console.error("Error al obtener las notificaciones:", error);
      throw error;
    }
  },

  /**
   * Obtiene únicamente el contador de notificaciones no leídas.
   * Ideal para polling por su bajo peso.
   * @returns {Promise<Object>} La respuesta con el contador. Ej: { count: 5 }
   */
  getUnreadCount: async () => {
    try {
      //const response = await api.get('/notifications/unread-count');

      const response = {
        data: {
          count: 4,
        },
      };
      return response.data;
    } catch (error) {
      console.error("Error al obtener el contador de no leídas:", error);
      throw error;
    }
  },

  /**
   * Marca una notificación específica como leída.
   * @param {string|number} notificationId El ID de la notificación.
   * @returns {Promise<Object>} La respuesta de la API.
   */
  markAsRead: async (notificationId) => {
    try {
      const response = await api.put(`/notifications/${notificationId}/read`);
      return response.data;
    } catch (error) {
      console.error(
        `Error al marcar la notificación ${notificationId} como leída:`,
        error
      );
      throw error;
    }
  },

  /**
   * Marca todas las notificaciones del usuario como leídas.
   * @returns {Promise<Object>} La respuesta de la API.
   */
  markAllAsRead: async () => {
    try {
      const response = await api.put("/notifications/mark-all-read");
      return response.data;
    } catch (error) {
      console.error(
        "Error al marcar todas las notificaciones como leídas:",
        error
      );
      throw error;
    }
  },
};
