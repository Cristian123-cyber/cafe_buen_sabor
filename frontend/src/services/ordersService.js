import api from "./api.js"; // Tu instancia de Axios configurada

/**
 * product-service.js
 * * Este servicio encapsula todas las llamadas a la API para la gestión de ordenes
 * Su única responsabilidad es realizar las peticiones HTTP y devolver los datos o lanzar errores.
 */
export const orderService = {
  /**
   *
   * Corresponde a: GET /api/orders
   * @returns {Promise<Array>} Una promesa que resuelve a un array de productos.
   */
  createOrder: async (data) => {
    try {
      const response = await api.post("/orders", data);
      console.log(response);
      return response.data;
    } catch (error) {
      console.error(
        "Error al crear la orden:",
        error.response?.data || error.message
      );
      throw error; // Lanza el error para que el store lo maneje
    }
  },

  getAllOrders: async () => {
    try {
      const response = await api.get("/orders");
      return response.data;
    } catch (error) {
      console.error(
        "Error al obtener la ordenesssss:",
        error.response?.data || error.message
      );
      throw error; // Lanza el error para que el store lo maneje
    }
  },

  getOrdersBySessionId: async (id) => {
    try {
      const response = await api.get(`/orders/session/${id}`);
      console.warn(response.data);
      return response.data;
    } catch (error) {
      console.error(
        "Error al obtener la orden:",
        error.response?.data || error.message
      );
      throw error; // Lanza el error para que el store lo maneje
    }
  },
  getOrdersByTableId: async (tableId) => {
    try {
      const response = await api.get(`/tables/${tableId}/active-orders`);
      return response.data;
    } catch (error) {
      console.error(
        `Error al obtener órdenes para la mesa ${tableId}:`,
        error.response?.data || error.message
      );
      throw error;
    }
  },
  /**
   * Actualiza el estado de un pedido específico.
   * Corresponde a: PATCH /api/orders/{id}/status
   * @param {number} orderId - El ID del pedido a actualizar.
   * @param {string} status - El nuevo estado ('CONFIRMED', 'CANCELLED').
   * @returns {Promise<Object>} La respuesta de la API con el pedido actualizado.
   */
  updateOrderStatus: async (orderId, action) => {
    try {
      const response = await api.put(`/orders/${orderId}/${action}`);
      return response.data;
    } catch (error) {
      console.error(`Error al actualizar el pedido ${orderId}:`, error.response?.data || error.message);
      throw error;
    }
  },

  /**
   * Actualiza el estado de múltiples pedidos en una sola llamada.
   * Corresponde a: POST /api/orders/bulk-status-update
   * @param {number[]} orderIds - Un array de IDs de pedidos.
   * @param {string} status - El nuevo estado a aplicar a todos.
   * @returns {Promise<Object>} La respuesta de la API.
   */
  bulkUpdateStatus: async (orderIds, action) => {
    try {
      const response = await api.put(`/orders/${action}`, { 
        order_ids: orderIds,
      });
      return response.data;
    } catch (error) {
      console.error('Error en la actualización masiva de pedidos:', error.response?.data || error.message);
      throw error;
    }
  },
};
