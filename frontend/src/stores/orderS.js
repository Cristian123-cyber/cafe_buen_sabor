import { defineStore } from "pinia";
import { ref, computed, reactive } from "vue";
import { orderService } from "../services/ordersService";
import { ORDER_STATUSES } from "../utils/constants";

/**
 * Orders Store
 * Gestiona el estado global de los pedidos para todos los roles
 */
export const useOrderStore = defineStore("orders", () => {
  // ===== STATE =====

  // Todos los pedidos cargados (cache principal)
  const orders = ref([]);

  // Todos los pedidos de la session de mesa actual
  const ordersCurrentSession = ref([]);

  // State
  const ordersForCurrentTable = ref([]);
  // Action
  const setOrdersForCurrentTable = (data) => {
    ordersForCurrentTable.value = data || [];
  };

  // Cola de cocina (solo pedidos CONFIRMED) (Solo cocineros)
  const kitchenQueue = ref([]);

  // Pedidos listos para recoger (READY)
  const readyOrders = ref([]);

  const isLoading = ref(false);

  // Errores específicos por operación
  const errors = reactive({
    fetchOrders: null,
    fetchKitchenQueue: null,
    orderActions: null,
    fecthOrdersCurrentSession: null,
    fetchOrdersCurrentTable: null
  });

  // ===== COMPUTED GETTERS =====

  // Estadísticas rápidas
  const orderStats = computed(() => ({
    total: orders.value.length,
    ready: readyOrders.value.length,
    delivered: deliveredOrders.value.length,
  }));

  // ===== ACTIONS =====

  /**
   * Limpiar todos los datos del store
   */
  const clearStore = () => {
    orders.value = [];
    kitchenQueue.value = [];
    readyOrders.value = [];
    ordersCurrentSession.value = [];
    ordersForCurrentTable.value = [];

    // Reset errors
    Object.keys(errors).forEach((key) => {
      errors[key] = null;
    });
  };

  const setOrdersCurrentSession = (data) => {
    if (!data || data === null) return;

    ordersCurrentSession.value = data;
  };
  const setOrders = (data) => {
    if (!data || data === null) return;

    orders.value = data;
  };

  const setKitchenQueue = (data) => {
    if (!data || data === null) return;

    kitchenQueue.value = data;
  };

  // ===== RETURN STORE INTERFACE =====

  return {
    //State
    orders,
    kitchenQueue,
    ordersCurrentSession,
    ordersForCurrentTable,
    isLoading,
    errors,

    //actions
    clearStore,
    setOrdersCurrentSession,
    setOrders,
    setKitchenQueue,
    setOrdersForCurrentTable
  };
});
