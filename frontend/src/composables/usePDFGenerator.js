// src/composables/useReportGenerator.js

import { reactive, ref } from 'vue';
import { analyticsService } from '../services/analytics.js';
import { pdfGenerator } from '../utils/PDFGenerator.js';
import { useToasts  } from './useToast.js';



const { addToast } = useToasts();

/**
 * Un composable completo para la generación de reportes.
 * Encapsula la lógica de obtención de datos, generación de PDF,
 * y manejo de estados de carga y errores para cada tipo de reporte.
 */
export function useReportGenerator() {
  const isLoading = ref(false);
  
  const error = ref(null);
  

  /**
   * Función interna genérica para envolver las operaciones.
   * @param {Function} operation - La operación asíncrona a ejecutar.
   * @param {string} successMessage - Mensaje para mostrar en caso de éxito.
   */
  const _handleReportGeneration = async (operation, successMessage) => {
    if (isLoading.value) return; // Prevenir ejecuciones múltiples
    isLoading.value = true;
    error.value = null;

    

    try {
      const data = await operation();

      console.log('DATAA: ', data);
      
      // Validar si la operación devolvió datos vacíos para no generar un PDF en blanco.
      if (!data || (Array.isArray(data) && data.length === 0) || (data.sales && data.sales.length === 0)) {
        
        addToast({
            message:'No se encontraron datos para el periodo seleccionado.',
            title: 'Error',
            type: 'info',
            duration: 4000
        });
        return; // Detenemos la ejecución aquí
      }

      

      addToast({
            message: successMessage,
            title: 'Reporte generado con exito',
            type: 'success',
            duration: 4000
        });
    } catch (err) {
      const errorMessage = err.response?.data?.message || err.message || 'Ocurrió un error inesperado al generar el reporte.';
      error.value = errorMessage;
      console.error("Error al generar reporte:", err);
       addToast({
            message: errorMessage,
            title: 'Error',
            type: 'error',
            duration: 4000
        });
    } finally {
      isLoading.value = false;
    }
  };

  // --- WRAPPERS ESPECÍFICOS PARA CADA REPORTE ---

  /**
   * 1. Genera el Reporte de Balance de Ventas.
   * @param {string} startDate
   * @param {string} endDate
   */
  const generateSalesBalanceReport = async (startDate, endDate) => {
    await _handleReportGeneration(async () => {
      const data = await analyticsService.getSalesBalance(startDate, endDate);
      if (data) pdfGenerator.createSalesBalancePDF(data, startDate, endDate);
      return data;
    }, 'Reporte de Balance de Ventas generado con éxito.');
  };

  /**
   * 2. Genera el Reporte de Listado de Facturas.
   * @param {string} startDate
   * @param {string} endDate
   */
  const generateInvoicesListReport = async (startDate, endDate) => {
    await _handleReportGeneration(async () => {
      const data = await analyticsService.getInvoicesList(startDate, endDate);
      if (data) pdfGenerator.createInvoicesListPDF(data, startDate, endDate);
      return data;
    }, 'Reporte de Listado de Facturas generado con éxito.');
  };

  /**
   * 3. Genera el Reporte de Desempeño de Empleados.
   * @param {string} startDate
   * @param {string} endDate
   */
  const generateEmployeesPerformanceReport = async (startDate, endDate) => {
    await _handleReportGeneration(async () => {
      const data = await analyticsService.getEmployeesPerformance(startDate, endDate);
      if (data) pdfGenerator.createEmployeesPerformancePDF(data, startDate, endDate);
      return data;
    }, 'Reporte de Desempeño de Empleados generado con éxito.');
  };

  /**
   * 4. Genera el Reporte de Estado de Inventario.
   */
  const generateInventoryStatusReport = async () => {
    await _handleReportGeneration(async () => {
      const data = await analyticsService.getInventoryStatus();
      console.log('DATAA2: ', data);
      if (data) pdfGenerator.createInventoryStatusPDF(data);
      return data;
    }, 'Reporte de Estado de Inventario generado con éxito.');
  };


  return {
    isLoading,
    error,
    generateSalesBalanceReport,
    generateInvoicesListReport,
    generateEmployeesPerformanceReport,
    generateInventoryStatusReport,
  };
}