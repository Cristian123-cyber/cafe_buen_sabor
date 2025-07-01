import { ref, readonly } from 'vue';

/**
 * @typedef {'success' | 'error' | 'warning' | 'info'} ToastType
 */

/**
 * @typedef {object} Toast
 * @property {number} id
 * @property {string} message
 * @property {string} [title]
 * @property {ToastType} type
 * @property {number} duration
 */

// Estado reactivo global y privado para los toasts.
// Se define fuera de la función para que sea un singleton.
const toasts = ref([]);

let idCounter = 0;

/**
 * Añade una nueva notificación toast a la cola.
 * @param {object} options
 * @param {string} options.message - El mensaje principal del toast.
 * @param {string} [options.title] - Un título opcional para el toast.
 * @param {ToastType} [options.type='info'] - El tipo de toast (success, error, warning, info).
 * @param {number} [options.duration=5000] - Duración en milisegundos antes de que se cierre solo.
 */
const addToast = ({ message, title = '', type = 'info', duration = 5000 }) => {
  if (!message) {
    console.warn('Se intentó crear un toast sin mensaje.');
    return;
  }
  
  idCounter++;
  toasts.value.push({
    id: idCounter,
    message,
    title,
    type,
    duration,
  });
};

/**
 * Elimina un toast de la cola por su ID.
 * @param {number} id - El ID del toast a eliminar.
 */
const removeToast = (id) => {
  const index = toasts.value.findIndex((toast) => toast.id === id);
  if (index > -1) {
    toasts.value.splice(index, 1);
  }
};

/**
 * Composable para gestionar notificaciones toast en toda la aplicación.
 * @returns {{
 *  toasts: import('vue').Readonly<import('vue').Ref<Toast[]>>,
 *  addToast: (options: {message: string, title?: string, type?: ToastType, duration?: number}) => void,
 *  removeToast: (id: number) => void
 * }}
 */
export function useToasts() {
  return {
    // Exponemos una versión de solo lectura para evitar modificaciones externas directas.
    toasts: readonly(toasts),
    addToast,
    removeToast,
  };
}