import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useSessionStore = defineStore('session', () => {
  const rawToken = localStorage.getItem("client_session_token");
  const rawTableInfo = localStorage.getItem("client_table_info");
  const rawSessionInfo = localStorage.getItem("client_session_info");

  const isValidToken = rawToken && typeof rawToken === "string" && rawToken.length > 20; // mínimo
  let parsedTableInfo = null;
  let parsedSessionInfo = null;

  try {
    parsedTableInfo = JSON.parse(rawTableInfo);
    parsedSessionInfo = JSON.parse(rawSessionInfo);
    
  } catch (e) {
    parsedTableInfo = null;
    parsedSessionInfo = null;
  }

  const isValidTableInfo =
    parsedTableInfo &&
    typeof parsedTableInfo === "object" &&
    typeof parsedTableInfo.id === "number" &&
    parsedTableInfo.id > 0;
  const isValidSessionInfo =
    parsedSessionInfo &&
    typeof parsedSessionInfo === "object" &&
    typeof parsedSessionInfo.id === "number" &&
    parsedSessionInfo.id > 0;

  const sessionToken = ref(isValidToken ? rawToken : null);
  const tableInfo = ref(isValidTableInfo ? parsedTableInfo : null);
  const sessionInfo = ref(isValidSessionInfo ? parsedSessionInfo : null);
  const sessionStatus = ref('idle');

  const isSessionActive = computed(() => sessionStatus.value === 'success' && !!sessionToken.value && !!tableInfo.value && !!sessionInfo.value);

  const setSessionData = (token, table_info, session_info) => {
    sessionToken.value = token;
    tableInfo.value = table_info;
    sessionInfo.value = session_info;
    localStorage.setItem("client_session_token", token);
    localStorage.setItem("client_table_info", JSON.stringify(table_info));
    localStorage.setItem("client_session_info", JSON.stringify(session_info));
    sessionStatus.value = 'success';
  };

  const clearSession = () => {
    sessionToken.value = null;
    tableInfo.value = null;
    sessionInfo.value = null;
    localStorage.removeItem("client_session_token");
    localStorage.removeItem("client_table_info");
    localStorage.removeItem("client_session_info");
    sessionStatus.value = 'idle';
  };
  

  return {
    sessionToken,
    tableInfo,
    sessionInfo,
    isSessionActive,
    setSessionData,
    clearSession,
    sessionStatus
  };
});
