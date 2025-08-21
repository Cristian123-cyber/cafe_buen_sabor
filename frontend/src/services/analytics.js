// src/services/analytics.js
import MonthlyRevenueChart from "../components/analytics/charts/MonthlyRevenueChart.vue";
import api from "./api.js";

export const analyticsService = {
  getDashboardSummary: async () => {
    try {
      const response = await api.get("/analytics/dashboard-summary");

      return response.data.data;
    } catch (error) {
      console.error("Error fetching dashboard summary:", error);
      throw error;
    }
  },

  getYearlyRevenueData: async (options = {}) => {
    try {
      const response = await api.get(
        `/analytics/yearly-revenue?${
          options.year ? `year=${options.year}` : ""
        }`
      );

      return response.data.data;
    } catch (error) {
      console.log(error);
      throw error;
    }
  },

  getTopProductsData: async (options = {}) => {
    try {
      const response = await api.get(
        `/analytics/top-products?limit=5&${
          options.period ? `period=${options.period}` : ""
        }`
      );

      return response.data.data;
    } catch (error) {
      console.log(error);
      throw error;
    }
  },

  getTopWaitersData: async (options = {}) => {
    try {
      const response = await api.get(
        `/analytics/top-waiters?${
          options.period ? `period=${options.period}` : ""
        }`
      );

      return response.data.data;
    } catch (error) {
      console.log(error);
      throw error;
    }
  },

  getSalesBalance: async (startDate, endDate) => {
    try {
      const response = await api.get('/analytics/sales-balance', {
        params: { start_date: startDate, end_date: endDate },
      });

  
      return response.data.data;
    } catch (error) {
      console.error("Error al obtener el balance de ventas:", error);
      throw error;
    }
  },

  /**
   * 2. REPORTE: LISTADO DE FACTURAS
   * Obtiene una lista detallada de todas las facturas emitidas en un rango de fechas.
   * Útil para auditoría y consulta de transacciones específicas.
   * @param {string} startDate - Fecha de inicio en formato YYYY-MM-DD
   * @param {string} endDate - Fecha de fin en formato YYYY-MM-DD
   * @returns {Promise<Array<Object>>} Un array con los objetos de cada factura.
   * @example API Endpoint: GET /api/analytics/invoices-list?start_date=...&end_date=...
   */
  getInvoicesList: async (startDate, endDate) => {
    try {
      const response = await api.get("/analytics/invoices-list", {
        params: { start_date: startDate, end_date: endDate },
      });

     
      
      return response.data.data;
    } catch (error) {
      console.error("Error al obtener el listado de facturas:", error);
      throw error;
    }
  },

  // --- REPORTES OPERACIONALES ---

  /**
   * 3. REPORTE: DESEMPEÑO GENERAL DE EMPLEADOS
   * Obtiene métricas de rendimiento comparativas de todos los empleados en un rango de fechas.
   * @param {string} startDate - Fecha de inicio en formato YYYY-MM-DD
   * @param {string} endDate - Fecha de fin en formato YYYY-MM-DD
   * @returns {Promise<Array<Object>>} Un array con las métricas por cada empleado.
   * @example API Endpoint: GET /api/analytics/employees-performance?start_date=...&end_date=...
   */
  getEmployeesPerformance: async (startDate, endDate) => {
    try {
      const response = await api.get("/analytics/employees-performance", {
        params: { start_date: startDate, end_date: endDate },
      });

      
      return response.data.data;
    } catch (error) {
      console.error("Error al obtener el desempeño de empleados:", error);
      throw error;
    }
  },

  /**
   * 4. REPORTE: ESTADO ACTUAL DEL INVENTARIO
   * Obtiene una "fotografía" del estado actual de todos los productos (stock, etc.).
   * No requiere parámetros de fecha.
   * @returns {Promise<Array<Object>>} Un array con el estado de cada producto en inventario.
   * @example API Endpoint: GET /api/analytics/inventory-status
   */
  getInventoryStatus: async () => {
    try {
      const response = await api.get("/analytics/inventory-status");

     
      return response.data.data;
    } catch (error) {
      console.error("Error al obtener el estado del inventario:", error);
      throw error;
    }
  },
  // ... otros servicios de analytics
};
