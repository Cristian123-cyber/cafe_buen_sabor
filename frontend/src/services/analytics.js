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
      /* const response = await api.get('/analytics/sales-balance', {
        params: { start_date: startDate, end_date: endDate },
      }); */

      const response = {
        data: {
          summary: {
            total_revenue: 155000.5,
            total_sales_count: 45,
            average_sale_value: 3444.46,
            payment_methods: {
              CONTADO: 85000.0,
              TRANSFERENCIA: 70000.5,
            },
          },
          sales: [
            {
              sale_id: 1,
              sale_date: "2025-08-15T14:30:00",
              total_amount: 25000.0,
              payment_method: "CONTADO",
              cashier_name: "Cristian Chisavo",
            },
            {
              sale_id: 2,
              sale_date: "2025-08-15T15:10:00",
              total_amount: 40000.5,
              payment_method: "TRANSFERENCIA",
              cashier_name: "Daniel",
            },
            // ... más ventas
          ],
        },
      };
      return response.data;
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
      /* const response = await api.get("/api/analytics/invoices-list", {
        params: { start_date: startDate, end_date: endDate },
      }); */
      const response = {
        data: [
          {
            invoice_id: 1,
            invoice_date: "2025-08-15T14:30:00",
            total_amount: 25000.0,
            payment_method: "CONTADO",
            cashier_name: "Cristian Chisavo",
            included_orders: [
              {
                order_id: 10,
                table_number: 1,
                waiter_name: "Juan Pérez",
              },
              {
                order_id: 12,
                table_number: 1,
                waiter_name: "Juan Pérez",
              },
            ],
          },
          {
            invoice_id: 2,
            invoice_date: "2025-08-15T15:10:00",
            total_amount: 40000.5,
            payment_method: "TRANSFERENCIA",
            cashier_name: "Daniel",
            included_orders: [
              {
                order_id: 11,
                table_number: 3,
                waiter_name: "Angie",
              },
            ],
          },
          // ... más facturas
        ],
      };
      return response.data;
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
      /* const response = await api.get("/api/analytics/employees-performance", {
        params: { start_date: startDate, end_date: endDate },
      }); */

      const response = {
        data: [
          {
            employee_id: 14,
            employee_name: "Juan Pérez",
            role: "Mesero",
            metrics: {
              orders_confirmed: 150,
              total_value_managed: 1850000.0,
              average_order_value: 12333.33,
            },
          },
          {
            employee_id: 26,
            employee_name: "RANA",
            role: "Mesero",
            metrics: {
              orders_confirmed: 120,
              total_value_managed: 1650000.0,
              average_order_value: 13750.0,
            },
          },
          {
            employee_id: 12,
            employee_name: "Juan Pérez",
            role: "Cajero",
            metrics: {
              sales_processed: 95,
              total_revenue_processed: 2100000.0,
              average_sale_value: 22105.26,
            },
          },
          {
            employee_id: 11,
            employee_name: "Juan Pérez",
            role: "Cocinero",
            metrics: {
              orders_prepared: 270,
              average_preparation_time_seconds: 360, // Opcional, si mides tiempos
            },
          },
          // ... más empleados
        ],
      };
      return response.data;
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
      /* const response = await api.get("/api/analytics/inventory-status"); */

      const response = {
        data: 
          {
            products: [
              {
                product_id: 2,
                product_name: "Cocacola",
                category: "Gaseosas",
                stock: 25,
                stock_unit: "Unidad",
                low_stock_level: 30,
                status: "BAJO", // Calculado en el backend
              },
              {
                product_id: 21,
                product_name: "Empanadas",
                category: "Bebidas", // <-- Esto parece un error en tus datos, debería ser otra categoría
                stock: 100,
                stock_unit: "Unidad",
                low_stock_level: 30,
                status: "OPTIMO",
              },
            ],
          },
      };
      return response.data;
    } catch (error) {
      console.error("Error al obtener el estado del inventario:", error);
      throw error;
    }
  },
  // ... otros servicios de analytics
};
