import { useOrderStore } from "../stores/orderS";
import { orderService } from "../services/ordersService";




export const useOrders = () => {

    const fetchAllOrders = async () => {

        const orderStore = useOrderStore();
        orderStore.isLoading = true;
        orderStore.errors.fetchOrders = null;

        try {

            const data = await orderService.getAllOrders();

            orderStore.setOrders(data.data);
            console.log('data seteada ordenes:: ', orderStore.orders);

        } catch (error) {
            orderStore.errors.fetchOrders = "Ha ocurrido un error al obtener las ordenes";

            
        } finally {
            orderStore.isLoading = false;
        }


    }


    const fetchOrdersCurrentSession = async (session_id) => {

        const orderStore = useOrderStore();
        orderStore.isLoading = true;
        orderStore.errors.fecthOrdersCurrentSession = null;

        try {

            const data = await orderService.getOrdersBySessionId(session_id);

            orderStore.setOrdersCurrentSession(data.data);
            console.log('data seteada ordenes current:: ', orderStore.ordersCurrentSession);

        } catch (error) {
            orderStore.errors.fecthOrdersCurrentSession = "Ha ocurrido un error al obtener las ordenes";

            
        } finally {
            orderStore.isLoading = false;
        }

    }


    const createOrder = async (data) => {

        

        const orderStore = useOrderStore();
        orderStore.isLoading = true;

        try {

            const result = await orderService.createOrder(data);
            console.log(result, 'responxze');

            return result;
     

        } catch (error) {
            throw error;

            
        } finally {
            orderStore.isLoading = false;
        }

    }


    return {
        fetchAllOrders,
        fetchOrdersCurrentSession,
        createOrder

    }

}