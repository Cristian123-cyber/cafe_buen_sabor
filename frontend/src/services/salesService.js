import api from "./api.js";

/**
 * sales-service.js
 * Este servicio encapsula todas las llamadas a la API para la gestión de ventas
 * Su única responsabilidad es realizar las peticiones HTTP y devolver los datos o lanzar errores.
 */
export const salesService = {
  /**
   * Obtener todas las ventas
   * Corresponde a: GET /api/sales
   * @param {Object} options - Opciones de paginación y ordenamiento
   * @returns {Promise<Array>} Una promesa que resuelve a un array de ventas.
   */
  getAllSales: async (options = {}) => {
    try {
      const params = new URLSearchParams();
      if (options.page) params.append('page', options.page);
      if (options.limit) params.append('limit', options.limit);
      if (options.orderBy) params.append('orderBy', options.orderBy);
      
      const response = await api.get(`/sales?${params.toString()}`);
      return response.data;
    } catch (error) {
      console.error(
        "Error al obtener las ventas:",
        error.response?.data || error.message
      );
      throw error;
    }
  },

  /**
   * Obtener una venta específica por ID
   * Corresponde a: GET /api/sales/{id}
   * @param {number} id - ID de la venta
   * @returns {Promise<Object>} Una promesa que resuelve a los datos de la venta.
   */
  getSaleById: async (id) => {
    try {
      const response = await api.get(`/sales/${id}`);
      return response.data;
    } catch (error) {
      console.error(
        "Error al obtener la venta:",
        error.response?.data || error.message
      );
      throw error;
    }
  },

  /**
   * Crear una nueva venta
   * Corresponde a: POST /api/sales
   * @param {Object} data - Datos de la venta
   * @returns {Promise<Object>} Una promesa que resuelve a la venta creada.
   */
  createSale: async (data) => {
    try {
      const response = await api.post("/sales", data);
      return response.data;
    } catch (error) {
      console.error(
        "Error al crear la venta:",
        error.response?.data || error.message
      );
      throw error;
    }
  },

  /**
   * Actualizar una venta existente
   * Corresponde a: PUT /api/sales/{id}
   * @param {number} id - ID de la venta
   * @param {Object} data - Datos a actualizar
   * @returns {Promise<Object>} Una promesa que resuelve a la venta actualizada.
   */
  updateSale: async (id, data) => {
    try {
      const response = await api.put(`/sales/${id}`, data);
      return response.data;
    } catch (error) {
      console.error(
        "Error al actualizar la venta:",
        error.response?.data || error.message
      );
      throw error;
    }
  },

  /**
   * Eliminar una venta
   * Corresponde a: DELETE /api/sales/{id}
   * @param {number} id - ID de la venta
   * @returns {Promise<Object>} Una promesa que resuelve al resultado de la eliminación.
   */
  deleteSale: async (id) => {
    try {
      const response = await api.delete(`/sales/${id}`);
      return response.data;
    } catch (error) {
      console.error(
        "Error al eliminar la venta:",
        error.response?.data || error.message
      );
      throw error;
    }
  },

  /**
   * Obtener recaudo mensual
   * Corresponde a: GET /api/sales/monthly-revenue
   * @returns {Promise<Object>} Una promesa que resuelve a los datos de recaudo mensual.
   */
  getMonthlyRevenue: async () => {
    try {
      const response = await api.get("/sales/monthly-revenue");
      return response.data;
    } catch (error) {
      console.error(
        "Error al obtener el recaudo mensual:",
        error.response?.data || error.message
      );
      throw error;
    }
  },

  /**
   * Obtener ingresos por fecha
   * Corresponde a: GET /api/sales/revenue-by-date
   * @param {Object} options - Opciones de fecha (from, to)
   * @returns {Promise<Object>} Una promesa que resuelve a los datos de ingresos por fecha.
   */
  getRevenueByDate: async (options = {}) => {
    try {
      const params = new URLSearchParams();
      if (options.from) params.append('from', options.from);
      if (options.to) params.append('to', options.to);
      
      const response = await api.get(`/sales/revenue-by-date?${params.toString()}`);
      return response.data;
    } catch (error) {
      console.error(
        "Error al obtener ingresos por fecha:",
        error.response?.data || error.message
      );
      throw error;
    }
  },

  /**
   * Obtener ingresos por empleado
   * Corresponde a: GET /api/sales/revenue-by-employee
   * @returns {Promise<Object>} Una promesa que resuelve a los datos de ingresos por empleado.
   */
  getRevenueByEmployee: async () => {
    try {
      const response = await api.get("/sales/revenue-by-employee");
      return response.data;
    } catch (error) {
      console.error(
        "Error al obtener ingresos por empleado:",
        error.response?.data || error.message
      );
      throw error;
    }
  },

  /**
   * Obtener ingresos por mesa
   * Corresponde a: GET /api/sales/revenue-by-table
   * @returns {Promise<Object>} Una promesa que resuelve a los datos de ingresos por mesa.
   */
  getRevenueByTable: async () => {
    try {
      const response = await api.get("/sales/revenue-by-table");
      return response.data;
    } catch (error) {
      console.error(
        "Error al obtener ingresos por mesa:",
        error.response?.data || error.message
      );
      throw error;
    }
  },

  /**
   * Obtener cantidad de mesas atendidas por mesero
   * Corresponde a: GET /api/employees/tables-served
   * @returns {Promise<Object>} Una promesa que resuelve a los datos de mesas atendidas por mesero.
   */
  getTablesServedByWaiter: async () => {
    try {
      const response = await api.get("/employees/tables-served");
      return response.data;
    } catch (error) {
      console.error(
        "Error al obtener mesas atendidas por mesero:",
        error.response?.data || error.message
      );
      throw error;
    }
  },

  /**
   * Obtener resumen de ventas por empleado
   * Corresponde a: GET /api/employees/{id}/sales-summary
   * @param {number} employeeId - ID del empleado
   * @returns {Promise<Object>} Una promesa que resuelve al resumen de ventas del empleado.
   */
  getEmployeeSalesSummary: async (employeeId) => {
    try {
      const response = await api.get(`/employees/${employeeId}/sales-summary`);
      return response.data;
    } catch (error) {
      console.error(
        "Error al obtener resumen de ventas del empleado:",
        error.response?.data || error.message
      );
      throw error;
    }
  }
};

