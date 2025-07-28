import api from "./api";
/**
 * Servicio para gestionar empleados de la API.
 */
export const employeService = {
  /**
   * Obtiene la lista completa de empleados.
   */
  getAll: async (params) => {
    try {
      const response = await api.get("/employees", { params: params });

      return response.data;
    } catch (error) {
     
        console.error("Error al obtener empleados:", error);
        throw error;
      
    }
  },

  /**
   * Obtiene un empleado por ID.
   */
  getById: async (id) => {
    try {
      const response = await api.get(`/employees/${id}`);
      return response.data;
    } catch (error) {
      console.error(`Error al obtener el empleado ${id}:`, error);
      throw error;
    }
  },

  /**
   * Crea un nuevo empleado.
   */
  create: async (data) => {
    try {
      const response = await api.post("/employees", data);
      return response.data;
    } catch (error) {
      console.error("Error al crear empleado:", error);
      throw error;
    }
  },

  /**
   * Actualiza un empleado existente.
   */
  update: async (id, data) => {
    try {
      const response = await api.put(`/employees/${id}`, data);
      return response.data;
    } catch (error) {
      console.error(`Error al actualizar el empleado ${id}:`, error);
      throw error;
    }
  },

  /**
   * Elimina (desactiva) un empleado.
   */
  delete: async (id) => {
    try {
      const response = await api.delete(`/employees/${id}`);
      return response.data;
    } catch (error) {
      console.error(`Error al eliminar el empleado ${id}:`, error);
      throw error;
    }
  },

  /**
   * Filtra empleados por rol y/o estado.
   */
  filter: async (params) => {
    try {
      const response = await api.get("/employees/filter", { params });
      return response.data;
    } catch (error) {
      console.error("Error al filtrar empleados:", error);
      throw error;
    }
  },

  /**
   * Obtiene la cantidad de mesas atendidas por mesero.
   */
  getTablesServed: async () => {
    try {
      const response = await api.get("/employees/tables-served");
      return response.data;
    } catch (error) {
      console.error("Error al obtener mesas atendidas:", error);
      throw error;
    }
  },

  /**
   * Obtiene el resumen de ventas de un empleado.
   */
  getSalesSummary: async (id) => {
    try {
      const response = await api.get(`/employees/${id}/sales-summary`);
      return response.data;
    } catch (error) {
      console.error(
        `Error al obtener resumen de ventas del empleado ${id}:`,
        error
      );
      throw error;
    }
  },

  /**
   * Obtiene todos los roles de empleados.
   */
  getRoles: async () => {
    try {
      const response = await api.get("/employees/roles");
      console.log("roles obtenidos: ", response.data);

      return response.data;
    } catch (error) {
      console.error("Error al obtener roles de empleados:", error);
      throw error;
    }
  },
  getStates: async () => {
    try {
      const response = await api.get("/estados-empleados");
      console.log("estados obtenidos: ", response.data);

      return response.data;
    } catch (error) {
      console.error("Error al obtener roles de empleados:", error);
      throw error;
    }
  },
};
