import { useSessionStore } from "../stores/clientSessionS";
import { clientSessionService } from "../services/clientSession";
import router from "../router/index.js";



export const useClientSession = () => {
  const store = useSessionStore();

  const startSession = async (qrToken, tableId) => {
    try {
      const data = await clientSessionService.validateQr(qrToken, tableId);
      console.log("data de session");
      console.log(data);

      if (
        !data.data.session_token ||
        !data.data.table_info?.id ||
        !data.data.session_info?.id
      ) {
        throw new Error("Sesión inválida: faltan datos clave.");
      }
      const session_token = data.data.session_token;
      const table_info = data.data.table_info;
      const session_info = data.data.session_info;
      store.setSessionData(session_token, table_info, session_info);

      return true;
    } catch (err) {
      console.error("Error validando QR pa:", err);
      store.sessionStatus = "error";
      throw err;
    }
  };

  const endSession = () => {
    store.clearSession();
    router.push({ name: "InvalidQR" });
  };

  const checkAuthClient = async () => {
    if (!store.sessionToken) {
      store.sessionStatus = "error";
      return;
    }

    store.sessionStatus = "loading";

    try {
      const sessionData = await clientSessionService.fetchClient(store.sessionInfo?.id);
      console.log(sessionData);

      if (sessionData.success && sessionData.data) {
        store.setSessionData(store.sessionToken, sessionData.data.table_info, sessionData.data.session_info)
      } else {
        throw new Error(
          sessionData.message || "Error al obtener datos del usuario"
        );
      }
    } catch (error) {
      console.error("Error en checkAuth:", error);
      store.authStatus = "error";
    }
  };

  return {
    startSession,
    endSession,
    checkAuthClient
  };
};
