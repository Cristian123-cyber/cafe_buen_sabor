// composables/useTableQR.js

import { ref, onMounted, onUnmounted, readonly } from "vue";
import { tablesService } from "../services/tables.js";

// NUEVO: Definimos el intervalo de polling como una constante para fácil configuración.
const POLLING_INTERVAL_MS = 60 * 1000; // 1 minuto

export function useTableDisplay(tableId) {
  const qrToken = ref(null);
  const isLoading = ref(true);
  const message = ref(null); // Para mostrar mensajes como "Pausado"
  const error = ref(null);

  const timeLeft = ref(0);
  const totalDuration = ref(0);

  let countdownInterval = null;
  let refreshTimeout = null; // Puede ser para expiración precisa O para polling

  const clearTimers = () => {
    if (countdownInterval) clearInterval(countdownInterval);
    if (refreshTimeout) clearTimeout(refreshTimeout);
  };

  const fetchQrToken = async () => {
    if (!qrToken.value) {
      isLoading.value = true;
    }
    
    try {
      const response = await tablesService.getQrForTable(tableId);
      
      qrToken.value = response.data.qr_token;
      error.value = null;

      // --- PUNTO DE DECISIÓN ---
      // Verificamos si la fecha de expiración existe y es válida.
      if (!response.data.token_expiration || response.data.token_expiration === null) {
        // Modo Pausado: Iniciar sondeo (polling)
        startPollingCheck();
        message.value = "Expiracion de codigo QR pausada.";
        
      } else {
        // Modo Activo: Iniciar contador preciso
        startExpirationCountdown(response.data.token_expiration);
        message.value = null; // Limpiamos cualquier mensaje de "pausado"
      }

    } catch (e) {
      error.value = "No se pudo obtener el código QR. Inténtalo de nuevo.";
      console.error("Error fetching QR token:", e);
      clearTimers();
    } finally {
      if (isLoading.value) {
        isLoading.value = false;
      }
    }
  };

  /**
   * Inicia el ciclo de vida para un token con fecha de expiración.
   * Calcula el tiempo restante y programa una llamada API precisa al expirar.
   */
  const startExpirationCountdown = (expirationTimestamp) => {
    clearTimers();

    const expirationDate = new Date(expirationTimestamp.replace(' ', 'T'));
    const now = new Date();
    const durationMs = Math.max(0, expirationDate.getTime() - now.getTime());
    
    totalDuration.value = durationMs;
    timeLeft.value = durationMs;

    // 1. Contador visual para la UI (cada segundo)
    countdownInterval = setInterval(() => {
      timeLeft.value = Math.max(0, timeLeft.value - 1000);
      if (timeLeft.value === 0) {
        clearInterval(countdownInterval);
      }
    }, 1000);

    // 2. Temporizador preciso para refrescar el token
    refreshTimeout = setTimeout(() => {
      console.log('Token expirado. Obteniendo uno nuevo...');
      fetchQrToken();
    }, durationMs);
  };

  /**
   * Inicia el ciclo de vida para un token sin fecha de expiración (pausado).
   * Programa un sondeo (polling) a un intervalo fijo.
   */
  const startPollingCheck = () => {
    clearTimers();
    
    // Reiniciamos los valores del temporizador para la UI
    timeLeft.value = 0;
    totalDuration.value = 0;

    console.warn(`Expiración pausada. Verificando de nuevo en ${POLLING_INTERVAL_MS / 1000} segundos.`);
    
    // Programamos la siguiente verificación a un intervalo fijo
    refreshTimeout = setTimeout(() => {
      console.log('Verificando estado de la mesa...');
      fetchQrToken();
    }, POLLING_INTERVAL_MS);
  };


  onMounted(() => {
    fetchQrToken();
  });

  onUnmounted(() => {
    clearTimers();
  });

  return {
    qrToken,
    isLoading,
    error,
    message,
    timeLeft: readonly(timeLeft),
    totalDuration: readonly(totalDuration),
  };
}