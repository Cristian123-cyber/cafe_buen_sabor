import { useOrderStore } from "../stores/orderS";
import { orderService } from "../services/ordersService";

import { useToasts } from "./useToast.js";

import { useAlert } from "./useAlert";

const alert = useAlert();

const { addToast } = useToasts();

export const useOrders = () => {
  const fetchAllOrders = async () => {
    const orderStore = useOrderStore();
    orderStore.isLoading = true;
    orderStore.errors.fetchOrders = null;

    try {
      const data = await orderService.getAllOrders();

      orderStore.setOrders(data.data);
      console.log("data seteada ordenes:: ", orderStore.orders);
    } catch (error) {
      orderStore.errors.fetchOrders =
        "Ha ocurrido un error al obtener las ordenes";
    } finally {
      orderStore.isLoading = false;
    }
  };

  const fetchOrdersCurrentSession = async (session_id) => {
    const orderStore = useOrderStore();
    orderStore.isLoading = true;
    orderStore.errors.fecthOrdersCurrentSession = null;

    try {
      const data = await orderService.getOrdersBySessionId(session_id);

      orderStore.setOrdersCurrentSession(data.data);
      console.log(
        "data seteada ordenes current:: ",
        orderStore.ordersCurrentSession
      );
    } catch (error) {
      orderStore.errors.fecthOrdersCurrentSession =
        "Ha ocurrido un error al obtener las ordenes";
    } finally {
      orderStore.isLoading = false;
    }
  };

  const fetchOrdersByTableId = async (tableId, showLoading = true) => {
    const orderStore = useOrderStore();
    orderStore.isLoading = showLoading;
    orderStore.errors.fetchOrders = null; // Puedes usar un error específico si quieres

    try {
      const data = await orderService.getOrdersByTableId(tableId);
      console.warn("PEIDOS DE LA MESAAAA:", data);
      orderStore.setOrdersForCurrentTable(data.data);
    } catch (error) {
      orderStore.errors.fetchOrders = "Error al cargar los pedidos de la mesa.";
      orderStore.setOrdersForCurrentTable([]); // Limpiar en caso de error
    } finally {
      orderStore.isLoading = false;
    }
  };

  const createOrder = async (data) => {
    const orderStore = useOrderStore();
    orderStore.isLoading = true;

    try {
      const result = await orderService.createOrder(data);
      console.log(result, "responxze");

      return result;
    } catch (error) {
      throw error;
    } finally {
      orderStore.isLoading = false;
    }
  };

  /**
   * Maneja la actualización de estado para un solo pedido.
   * @param {number} orderId - ID del pedido.
   * @param {'CONFIRMED' | 'CANCELLED'} status - Nuevo estado.
   * @returns {Promise<boolean>} - True si tuvo éxito, false si no.
   */
  const handleUpdateStatus = async (orderId, status, tableId) => {
    const orderStore = useOrderStore();

    try {
      await orderService.updateOrderStatus(orderId, status);

      if (tableId) {
        fetchOrdersByTableId(tableId, false);
      }

      addToast({
        title: "Éxito",
        message: `El pedido #${orderId} ha sido ${
          status === "confirm" ? "confirmado" : "cancelado"
        }.`,
        type: "success",
      });
      return true;
    } catch (error) {
      alert.show({
        title: "Ha ocurrido un error",
        message: "No se pudo actualizar el estado del pedido.",
        variant: "error",
      });

      return false;
    }
  };

  /**
   * Maneja la actualización masiva de estados de pedidos.
   * @param {number[]} orderIds - Array de IDs.
   * @param {'confirm' | 'cancel'} action - Nuevo estado.
   * @returns {Promise<boolean>}
   */
  const handleBulkUpdateStatus = async (orderIds, status, tableId) => {
    const orderStore = useOrderStore();

    if (orderIds.length === 0) return true;
    try {
      await orderService.bulkUpdateStatus(orderIds, status);

      if (tableId) {
        fetchOrdersByTableId(tableId, false);
      }

      addToast({
        title: "Operación Exitosa",
        message: `${orderIds.length} pedidos han sido ${
          status === "confirm-all" ? "confirmados" : "cancelados"
        }.`,
        type: "success",
      });
      return true;
    } catch (error) {
      alert.show({
        title: "Error al actualizar pedidos",
        message: "No se pudieron actualizar los estados de los pedidos.",
        variant: "error",
      });
      return false;
    }
  };

  return {
    fetchAllOrders,
    fetchOrdersCurrentSession,
    fetchOrdersByTableId,
    createOrder,
    handleUpdateStatus,
    handleBulkUpdateStatus,
  };
};
