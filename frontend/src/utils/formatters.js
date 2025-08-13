/**
 * src/utils/formatters.js
 * 
 * Composable reutilizable para formatear datos comunes en la aplicación.
 * Proporciona funciones para dar formato a monedas, fechas y otros tipos de datos,
 * asegurando consistencia visual en toda la UI.
 */


export const useFormatters = () => {

  /**
   * Formatea un valor numérico o un string numérico a una cadena de moneda.
   * Está preconfigurado para el Peso Colombiano (COP) pero puede ser adaptado.
   *
   * @param {number | string | null | undefined} value - El valor a formatear.
   * @param {string} locale - El código de localización (ej. 'es-CO').
   * @param {string} currency - El código de la moneda (ej. 'COP').
   * @returns {string} El valor formateado como moneda (ej. "$ 10.000").
   */
  const formatCurrency = (value, locale = 'es-CO', currency = 'COP') => {
    // 1. Validar la entrada. Si es nulo, indefinido o no es un número, devolver un valor por defecto.
    const numericValue = parseFloat(value);
    if (isNaN(numericValue)) {
      return new Intl.NumberFormat(locale, {
        style: 'currency',
        currency: currency,
        maximumFractionDigits: 0, // No mostrar centavos para COP
      }).format(0);
    }

    // 2. Usar la API Intl.NumberFormat para un formato correcto y localizado.
    try {
      return new Intl.NumberFormat(locale, {
        style: 'currency',
        currency: currency,
        maximumFractionDigits: 0, // En Colombia es común no mostrar centavos en los precios.
        minimumFractionDigits: 0,
      }).format(numericValue);
    } catch (error) {
      console.error("Error al formatear moneda:", error);
      return String(value); // Devolver el valor original si hay un error.
    }
  };

  /**
   * Formatea una cadena de fecha o un objeto Date a un formato legible.
   *
   * @param {string | Date} dateInput - La fecha a formatear.
   * @param {'short' | 'long' | 'datetime'} format - El formato deseado.
   *   - 'short': DD/MM/YYYY (ej. 19/06/2025)
   *   - 'long': 19 de junio de 2025
   *   - 'datetime': 19/06/2025, 14:30
   * @param {string} locale - El código de localización (ej. 'es-ES').
   * @returns {string} La fecha formateada.
   */
  const formatDate = (dateInput, format = 'short', locale = 'es-ES') => {
    if (!dateInput) return 'Fecha no disponible';
    
    try {
      const date = new Date(dateInput);
      // Validar si la fecha es válida después de la conversión
      if (isNaN(date.getTime())) {
          return 'Fecha inválida';
      }

      const options = {};
      switch (format) {
        case 'long':
          options.year = 'numeric';
          options.month = 'long';
          options.day = 'numeric';
          break;
        case 'datetime':
          options.year = 'numeric';
          options.month = '2-digit';
          options.day = '2-digit';
          options.hour = '2-digit';
          options.minute = '2-digit';
          break;
        case 'short':
        default:
          options.year = 'numeric';
          options.month = '2-digit';
          options.day = '2-digit';
          break;
      }
      return new Intl.DateTimeFormat(locale, options).format(date);
    } catch (error) {
      console.error("Error al formatear fecha:", error);
      return String(dateInput);
    }
  };

  // Puedes añadir más formatters aquí según necesites (ej. para capitalizar texto)
  
  /**
   * Capitaliza la primera letra de una cadena de texto.
   * @param {string} text - El texto a capitalizar.
   * @returns {string} El texto con la primera letra en mayúscula.
   */
  const capitalize = (text) => {
    if (typeof text !== 'string' || text.length === 0) return '';
    return text.charAt(0).toUpperCase() + text.slice(1);
  };


  // Devolvemos las funciones para que puedan ser destructuradas en los componentes.
  return {
    formatCurrency,
    formatDate,
    capitalize,
  };
};