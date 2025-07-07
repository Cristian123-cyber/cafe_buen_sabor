import api from './api.js';

export const clientSessionService = {
  /**
   * Valida el QR contra la mesa en el backend y retorna el JWT + info
   */
  validateQr: async (qrToken, tableId) => {
    const response = await api.post('/table-sessions/validate-qr', {
      qr_token: qrToken,
      id_table: tableId
    });
    return response.data;
  }
};