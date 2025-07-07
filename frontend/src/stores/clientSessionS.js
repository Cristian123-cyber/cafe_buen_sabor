import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useSessionStore = defineStore('session', () => {
  const rawToken = localStorage.getItem("client_session_token");
  const rawTableInfo = localStorage.getItem("client_table_info");

  const isValidToken = rawToken && typeof rawToken === "string" && rawToken.length > 20; // mínimo
  let parsedTableInfo = null;

  try {
    parsedTableInfo = JSON.parse(rawTableInfo);
  } catch (e) {
    parsedTableInfo = null;
  }

  const isValidTableInfo =
    parsedTableInfo &&
    typeof parsedTableInfo === "object" &&
    typeof parsedTableInfo.id === "number" &&
    parsedTableInfo.id > 0;

  const sessionToken = ref(isValidToken ? rawToken : null);
  const tableInfo = ref(isValidTableInfo ? parsedTableInfo : null);

  const isSessionActive = computed(() => !!sessionToken.value && !!tableInfo.value);

  const setSessionData = (token, info) => {
    sessionToken.value = token;
    tableInfo.value = info;
    localStorage.setItem("client_session_token", token);
    localStorage.setItem("client_table_info", JSON.stringify(info));
  };

  const clearSession = () => {
    sessionToken.value = null;
    tableInfo.value = null;
    localStorage.removeItem("client_session_token");
    localStorage.removeItem("client_table_info");
  };

  return {
    sessionToken,
    tableInfo,
    isSessionActive,
    setSessionData,
    clearSession
  };
});
