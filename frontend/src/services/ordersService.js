import api from './api.js'; // Tu instancia de Axios configurada

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
      const response = await api.post('/orders', data);
      console.log(response);
      return response.data;
    } catch (error) {
      console.error('Error al crear la orden:', error.response?.data || error.message);
      throw error; // Lanza el error para que el store lo maneje
    }
  },


  getAllOrders: async () => {
    try {
      const response = await api.get('/orders');
      return response.data;
    } catch (error) {
      console.error('Error al obtener la ordenesssss:', error.response?.data || error.message);
      throw error; // Lanza el error para que el store lo maneje
    }
  },


  getOrdersBySessionId: async (id) => {
    try {
      const response = await api.get(`/orders/session/${id}`);
      console.warn(response.data);
      return response.data;
    } catch (error) {
      console.error('Error al obtener la orden:', error.response?.data || error.message);
      throw error; // Lanza el error para que el store lo maneje
    }

  }

}
