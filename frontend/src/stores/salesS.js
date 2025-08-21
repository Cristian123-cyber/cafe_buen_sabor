import { defineStore } from "pinia";
import { ref, computed, reactive } from "vue";
import { salesService } from "../services/salesService";

/**
 * Sales Store
 * Gestiona el estado global de las ventas para el rol de administrador
 */
export const useSalesStore = defineStore("sales", () => {
  // ===== STATE =====
  
  // Todas las ventas cargadas (cache principal)
  const sales = ref([]);
  
  // Datos de recaudo mensual
  const monthlyRevenue = ref(null);
  
  // Datos de ingresos por fecha
  const revenueByDate = ref(null);
  
  // Datos de ingresos por empleado
  const revenueByEmployee = ref(null);
  
  // Datos de ingresos por mesa
  const revenueByTable = ref(null);
  
  // Datos de mesas atendidas por mesero
  const tablesServedByWaiter = ref(null);
  
  // Estado de carga
  const isLoading = ref(false);
  const loadingSales = ref(false);
  const loadingMonthlyRevenue = ref(false);
  const loadingRevenueByDate = ref(false);
  const loadingRevenueByEmployee = ref(false);
  const loadingRevenueByTable = ref(false);
  const loadingTablesServed = ref(false);

  // Errores específicos por operación
  const errors = reactive({
    fetchSales: null,
    fetchMonthlyRevenue: null,
    fetchRevenueByDate: null,
    fetchRevenueByEmployee: null,
    fetchRevenueByTable: null,
    fetchTablesServed: null,
    createSale: null,
    updateSale: null,
    deleteSale: null
  });

  // ===== COMPUTED GETTERS =====
  
  // Estadísticas rápidas de ventas
  const salesStats = computed(() => ({
    total: sales.value.length,
    totalAmount: sales.value.reduce((sum, sale) => sum + (parseFloat(sale.total_amount) || 0), 0),
    completed: sales.value.filter(sale => sale.sale_status === 'COMPLETED').length,
    canceled: sales.value.filter(sale => sale.sale_status === 'CANCELED').length
  }));

  // Ventas completadas
  const completedSales = computed(() => 
    sales.value.filter(sale => sale.sale_status === 'COMPLETED')
  );

  // Ventas canceladas
  const canceledSales = computed(() => 
    sales.value.filter(sale => sale.sale_status === 'CANCELED')
  );

  // ===== ACTIONS =====

  /**
   * Limpiar todos los datos del store
   */
  const clearStore = () => {
    sales.value = [];
    monthlyRevenue.value = null;
    revenueByDate.value = null;
    revenueByEmployee.value = null;
    revenueByTable.value = null;
    tablesServedByWaiter.value = null;
    
    // Reset errors
    Object.keys(errors).forEach(key => {
      errors[key] = null;
    });
  };

  /**
   * Establecer las ventas en el store
   */
  const setSales = (data) => {
    if (!data || data === null) return;
    sales.value = data;
  };

  /**
   * Establecer el recaudo mensual en el store
   */
  const setMonthlyRevenue = (data) => {
    if (!data || data === null) return;
    monthlyRevenue.value = data;
  };

  /**
   * Establecer ingresos por fecha en el store
   */
  const setRevenueByDate = (data) => {
    if (!data || data === null) return;
    revenueByDate.value = data;
  };

  /**
   * Establecer ingresos por empleado en el store
   */
  const setRevenueByEmployee = (data) => {
    if (!data || data === null) return;
    revenueByEmployee.value = data;
  };

  /**
   * Establecer ingresos por mesa en el store
   */
  const setRevenueByTable = (data) => {
    if (!data || data === null) return;
    revenueByTable.value = data;
  };

  /**
   * Establecer mesas atendidas por mesero en el store
   */
  const setTablesServedByWaiter = (data) => {
    if (!data || data === null) return;
    tablesServedByWaiter.value = data;
  };

  /**
   * Obtener todas las ventas
   */
  const fetchSales = async (options = {}) => {
    loadingSales.value = true;
    errors.fetchSales = null;
    
    try {
      const response = await salesService.getAllSales(options);
      if (response.success) {
        setSales(response.data);
      }
      return response;
    } catch (error) {
      errors.fetchSales = error.message || 'Error al obtener las ventas';
      throw error;
    } finally {
      loadingSales.value = false;
    }
  };

  /**
   * Obtener recaudo mensual
   */
  const fetchMonthlyRevenue = async () => {
    loadingMonthlyRevenue.value = true;
    errors.fetchMonthlyRevenue = null;
    
    try {
      const response = await salesService.getMonthlyRevenue();
      if (response.success) {
        setMonthlyRevenue(response.data);
      }
      return response;
    } catch (error) {
      errors.fetchMonthlyRevenue = error.message || 'Error al obtener el recaudo mensual';
      throw error;
    } finally {
      loadingMonthlyRevenue.value = false;
    }
  };

  /**
   * Obtener ingresos por fecha
   */
  const fetchRevenueByDate = async (options = {}) => {
    loadingRevenueByDate.value = true;
    errors.fetchRevenueByDate = null;
    
    try {
      const response = await salesService.getRevenueByDate(options);
      if (response.success) {
        setRevenueByDate(response.data);
      }
      return response;
    } catch (error) {
      errors.fetchRevenueByDate = error.message || 'Error al obtener ingresos por fecha';
      throw error;
    } finally {
      loadingRevenueByDate.value = false;
    }
  };

  /**
   * Obtener ingresos por empleado
   */
  const fetchRevenueByEmployee = async () => {
    loadingRevenueByEmployee.value = true;
    errors.fetchRevenueByEmployee = null;
    
    try {
      const response = await salesService.getRevenueByEmployee();
      if (response.success) {
        setRevenueByEmployee(response.data);
      }
      return response;
    } catch (error) {
      errors.fetchRevenueByEmployee = error.message || 'Error al obtener ingresos por empleado';
      throw error;
    } finally {
      loadingRevenueByEmployee.value = false;
    }
  };

  /**
   * Obtener ingresos por mesa
   */
  const fetchRevenueByTable = async () => {
    loadingRevenueByTable.value = true;
    errors.fetchRevenueByTable = null;
    
    try {
      const response = await salesService.getRevenueByTable();
      if (response.success) {
        setRevenueByTable(response.data);
      }
      return response;
    } catch (error) {
      errors.fetchRevenueByTable = error.message || 'Error al obtener ingresos por mesa';
      throw error;
    } finally {
      loadingRevenueByTable.value = false;
    }
  };

  /**
   * Obtener mesas atendidas por mesero
   */
  const fetchTablesServedByWaiter = async () => {
    loadingTablesServed.value = true;
    errors.fetchTablesServed = null;
    
    try {
      const response = await salesService.getTablesServedByWaiter();
      if (response.success) {
        setTablesServedByWaiter(response.data);
      }
      return response;
    } catch (error) {
      errors.fetchTablesServed = error.message || 'Error al obtener mesas atendidas por mesero';
      throw error;
    } finally {
      loadingTablesServed.value = false;
    }
  };

  /**
   * Crear una nueva venta
   */
  const createSale = async (saleData) => {
    isLoading.value = true;
    errors.createSale = null;
    
    try {
      const response = await salesService.createSale(saleData);
      if (response.success) {
        // Recargar las ventas para incluir la nueva
        await fetchSales();
      }
      return response;
    } catch (error) {
      errors.createSale = error.message || 'Error al crear la venta';
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  /**
   * Actualizar una venta existente
   */
  const updateSale = async (id, saleData) => {
    isLoading.value = true;
    errors.updateSale = null;
    
    try {
      const response = await salesService.updateSale(id, saleData);
      if (response.success) {
        // Recargar las ventas para incluir los cambios
        await fetchSales();
      }
      return response;
    } catch (error) {
      errors.updateSale = error.message || 'Error al actualizar la venta';
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  /**
   * Eliminar una venta
   */
  const deleteSale = async (id) => {
    isLoading.value = true;
    errors.deleteSale = null;
    
    try {
      const response = await salesService.deleteSale(id);
      if (response.success) {
        // Recargar las ventas para excluir la eliminada
        await fetchSales();
      }
      return response;
    } catch (error) {
      errors.deleteSale = error.message || 'Error al eliminar la venta';
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  // ===== RETURN STORE INTERFACE =====
  
  return {
    // State
    sales,
    monthlyRevenue,
    revenueByDate,
    revenueByEmployee,
    revenueByTable,
    tablesServedByWaiter,
    isLoading,
    loadingSales,
    loadingMonthlyRevenue,
    loadingRevenueByDate,
    loadingRevenueByEmployee,
    loadingRevenueByTable,
    loadingTablesServed,
    errors,

    // Computed
    salesStats,
    completedSales,
    canceledSales,

    // Actions
    clearStore,
    setSales,
    setMonthlyRevenue,
    setRevenueByDate,
    setRevenueByEmployee,
    setRevenueByTable,
    setTablesServedByWaiter,
    fetchSales,
    fetchMonthlyRevenue,
    fetchRevenueByDate,
    fetchRevenueByEmployee,
    fetchRevenueByTable,
    fetchTablesServedByWaiter,
    createSale,
    updateSale,
    deleteSale
  };
});

