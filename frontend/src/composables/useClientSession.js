import { useSessionStore } from "../stores/clientSessionS";
import { clientSessionService } from "../services/clientSession";
import { useRouter } from "vue-router";

export const useClientSession = () => {
  const store = useSessionStore();
  const router = useRouter();

  const startSession = async (qrToken, tableId) => {
    try {
      const { session_token, table_info } =
        await clientSessionService.validateQr(qrToken, tableId);

      if (!session_token || !table_info?.id) {
        throw new Error("Sesión inválida: faltan datos clave.");
      }
      store.setSessionData(session_token, table_info);
      router.replace({ name: "ClientMenu" }); // Redirige al menú si todo fue bien
    } catch (err) {
      console.error("Error validando QR:", err);
      router.replace({ name: "InvalidQR" }); // Vista de error
    }
  };

  const endSession = () => {
    store.clearSession();
    router.push({ name: "InvalidQR" });
  };

  return {
    startSession,
    endSession
  };
};
