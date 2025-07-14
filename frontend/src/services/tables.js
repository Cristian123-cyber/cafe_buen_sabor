import api from "./api.js";

export const tablesService = {
  /**
   * Obtiene el token QR actual para una mesa específica.
   * @param {string | number} tableId El ID de la mesa.
   * @returns {Promise<Object>} La respuesta de la API, que debe contener el token del QR.
   */
  getQrForTable: async (tableId) => {
    try {
      // Llamamos al endpoint definido en tu documentación.
      const response = await api.get(`/tables/${tableId}/qr`);
     

      console.log('qr nuevo obtenido', response);


     
      return response.data;
    } catch (error) {
      console.error(`Error al obtener el QR para la mesa ${tableId}:`, error);
      // Relanzamos el error para que el composable pueda manejarlo.
      throw error;
    }
  },

  
};