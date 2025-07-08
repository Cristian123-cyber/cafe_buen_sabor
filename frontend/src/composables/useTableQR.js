import { ref, onMounted, onUnmounted } from "vue";
import { tablesService } from "../services/tables.js";

// Polling cada 30 segundos (más eficiente que 10)
const POLLING_INTERVAL = 30000;

export function useTableDisplay(tableId) {
  const qrToken = ref(null);
  const isLoading = ref(true);
  const error = ref(null);
  let pollingTimer = null;

  const fetchQrToken = async () => {
    if (!qrToken.value) {
      isLoading.value = true;
    }
    
    try {
      const data = await tablesService.getQrForTable(tableId);
      qrToken.value = data.qr_token;
      error.value = null;
    } catch (e) {
      error.value = "Error al cargar QR";
      console.error(e);
    } finally {
      if (isLoading.value) {
        isLoading.value = false;
      }
    }
  };

  const startPolling = () => {

    if (pollingTimer) clearTimeout(pollingTimer);
    
    pollingTimer = setTimeout(async () => {
    console.log('obteniendo QRRR');

      await fetchQrToken();
      startPolling();
    }, POLLING_INTERVAL);
  };

  onMounted(async () => {
    await fetchQrToken();
    startPolling();
  });

  onUnmounted(() => {
    if (pollingTimer) {
      clearTimeout(pollingTimer);
    }
  });

  return {
    qrToken,
    isLoading,
    error
  };
}