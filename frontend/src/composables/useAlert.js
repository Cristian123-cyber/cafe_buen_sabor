import { ref, readonly } from 'vue';

/**
 * Estado reactivo global y privado para almacenar las alertas activas.
 * Al estar fuera de la función, actúa como un singleton.
 */
const activeAlert = ref(null);

/**
 * Nuestro composable "minilibrería" para mostrar alertas modales profesionales.
 */
export function useAlert() {
  /**
   * Muestra una alerta modal y devuelve una Promesa que se resuelve con la
   * decisión del usuario (true para confirmar, false para cancelar/cerrar).
   *
   * @param {object} options - Opciones para personalizar la alerta.
   * @param {string} options.title - El título principal de la alerta.
   * @param {string} options.message - El texto descriptivo.
   * @param {'success'|'error'|'warning'|'info'} [options.variant='info'] - El tono visual.
   * @param {string} [options.confirmButtonText='Confirmar'] - Texto del botón de confirmación.
   * @param {string} [options.cancelButtonText='Cancelar'] - Texto del botón de cancelación.
   * @returns {Promise<boolean>} - Resuelve `true` si se confirma, `false` en caso contrario.
   */
  const show = (options) => {
    // Solo permitimos una alerta modal a la vez para una UX limpia.
    if (activeAlert.value) {
      console.warn("Se intentó mostrar una alerta mientras otra ya estaba activa. Petición ignorada.");
      // Rechazamos la promesa si ya hay una alerta
      return Promise.reject("Alert conflict");
    }

    return new Promise((resolve) => {
      activeAlert.value = {
        // Valores por defecto fusionados con las opciones proporcionadas
        variant: 'info',
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar',
        ...options,
        // Almacenamos la función `resolve` para llamarla desde la UI
        _resolve: resolve,
      };
    });
  };

  /**
   * Cierra la alerta activa y resuelve la promesa asociada.
   * Esta función es para uso interno del Presenter.
   * @param {boolean} result - El valor con el que se resolverá la promesa.
   */
  const _close = (result) => {
    if (activeAlert.value && typeof activeAlert.value._resolve === 'function') {
      activeAlert.value._resolve(result);
    }
    activeAlert.value = null;
  };

  return {
    // Exponemos una versión de solo lectura del estado para el Presenter
    activeAlert: readonly(activeAlert),
    show,
    _close, // Exponemos la función de cierre para el Presenter
  };
}