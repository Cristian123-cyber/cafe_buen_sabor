<script setup>
import { ref } from 'vue';
import { useReportGenerator } from '../../composables/usePDFGenerator.js';
import { useToasts } from '../../composables/useToast.js';



const { addToast } = useToasts();

const loadingFlags = ref({
    loadingSales: false,
    loadingInvoices: false,
    loadingPerf: false,
    loadingInventory: false,
  });

// 1. Importamos toda la lógica del composable
const {
  isLoading,
  generateSalesBalanceReport,
  generateInvoicesListReport,
  generateEmployeesPerformanceReport,
  generateInventoryStatusReport,
} = useReportGenerator();

// 2. Estado reactivo separado para cada reporte
const salesParams = ref({
  startDate: new Date().toISOString().split('T')[0],
  endDate: new Date().toISOString().split('T')[0],
});

const invoiceParams = ref({
  startDate: new Date().toISOString().split('T')[0],
  endDate: new Date().toISOString().split('T')[0],
});

const performanceParams = ref({
  startDate: new Date().toISOString().split('T')[0],
  endDate: new Date().toISOString().split('T')[0],
});

// Helper para validar fechas antes de generar (recibe los parámetros específicos)
const areDatesValid = (params) => {
  if (!params.startDate || !params.endDate) {
    addToast({
            message: `Por favor, selecciona ambas fechas.`,
            title: 'Error',
            type: 'error',
            duration: 4000
        });
    return false;
  }
  if (new Date(params.startDate) > new Date(params.endDate)) {

    addToast({
            message: `La fecha de inicio no puede ser posterior a la fecha de fin.`,
            title: 'Error',
            type: 'error',
            duration: 4000
        });

    return false;
  }
  return true;
};
</script>

<template>
  <AppLayout>
    <div class="reports-container">
      <HeaderSection title="Gestión de Reportes"
        descriptionMessage="Selecciona un tipo de reporte, define los parámetros y descarga el documento en formato PDF." />

      <div class="reports-grid">

        <!-- Tarjeta 1: Balance de Ventas -->
        <ReportCard title="Balance General de Ventas"
          description="Un resumen financiero detallado de ingresos, total de ventas y métodos de pago en un periodo.">
          <template #icon>
            <i-heroicons-chart-bar-20-solid />
          </template>
          <template #form-fields>
            <div class="form-fields-container">
              <div class="form-group">
                <label for="sales-start-date" class="form-label">Fecha de inicio</label>
                <input type="date" id="sales-start-date" v-model="salesParams.startDate" class="form-input" />
              </div>
              <div class="form-group">
                <label for="sales-end-date" class="form-label">Fecha de fin</label>
                <input type="date" id="sales-end-date" v-model="salesParams.endDate" class="form-input" />
              </div>
            </div>
          </template>
          <template #action-button>
            <BaseButton variant="gradient-terciary-2" size="lg" :loading="isLoading" :disabled="isLoading"
              @click="areDatesValid(salesParams) && generateSalesBalanceReport(salesParams.startDate, salesParams.endDate)"
              class="w-full">
              <template #icon-left>
                <i-heroicons-document-arrow-down-20-solid />
              </template>
              Generar Reporte
            </BaseButton>
          </template>
        </ReportCard>

        <!-- Tarjeta 2: Listado de Facturas -->
        <ReportCard title="Listado de Facturas"
          description="Genera una lista de todas las facturas emitidas en el rango de fechas seleccionado para auditoría.">
          <template #icon>
            <i-heroicons-document-text-20-solid />
          </template>
          <template #form-fields>
            <div class="form-fields-container">
              <div class="form-group">
                <label for="invoice-start-date" class="form-label">Fecha de inicio</label>
                <input type="date" id="invoice-start-date" v-model="invoiceParams.startDate" class="form-input" />
              </div>
              <div class="form-group">
                <label for="invoice-end-date" class="form-label">Fecha de fin</label>
                <input type="date" id="invoice-end-date" v-model="invoiceParams.endDate" class="form-input" />
              </div>
            </div>
          </template>
          <template #action-button>
            <BaseButton variant="gradient-secondary" size="lg" :loading="isLoading" :disabled="isLoading"
              @click="areDatesValid(invoiceParams) && generateInvoicesListReport(invoiceParams.startDate, invoiceParams.endDate)"
              class="w-full">
              <template #icon-left>
                <i-heroicons-document-arrow-down-20-solid />
              </template>
              Generar Reporte
            </BaseButton>
          </template>
        </ReportCard>

        <!-- Tarjeta 3: Desempeño de Empleados -->
        <ReportCard title="Desempeño de Empleados"
          description="Compara las métricas de rendimiento de meseros y cajeros durante un periodo específico.">
          <template #icon>
            <i-heroicons-users-20-solid />
          </template>
          <template #form-fields>
            <div class="form-fields-container">
              <div class="form-group">
                <label for="perf-start-date" class="form-label">Fecha de inicio</label>
                <input type="date" id="perf-start-date" v-model="performanceParams.startDate" class="form-input" />
              </div>
              <div class="form-group">
                <label for="perf-end-date" class="form-label">Fecha de fin</label>
                <input type="date" id="perf-end-date" v-model="performanceParams.endDate" class="form-input" />
              </div>
            </div>
          </template>
          <template #action-button>
            <BaseButton variant="gradient-terciary" size="lg" :loading="isLoading" :disabled="isLoading"
              @click="areDatesValid(performanceParams) && generateEmployeesPerformanceReport(performanceParams.startDate, performanceParams.endDate)"
              class="w-full">
              <template #icon-left>
                <i-heroicons-document-arrow-down-20-solid />
              </template>
              Generar Reporte
            </BaseButton>
          </template>
        </ReportCard>

        <!-- Tarjeta 4: Estado de Inventario -->
        <ReportCard title="Estado de Inventario"
          description="Obtiene una 'fotografía' actual del stock de todos los productos e ingredientes registrados.">
          <template #icon>
            <i-heroicons-cube-20-solid />
          </template>
          <!-- Este reporte no necesita formulario, por lo que el slot queda vacío -->
          <template #action-button>
            <BaseButton variant="success" size="lg" :loading="isLoading" :disabled="isLoading"
              @click="generateInventoryStatusReport" class="w-full">
              <template #icon-left>
                <i-heroicons-document-arrow-down-20-solid />
              </template>
              Generar Reporte
            </BaseButton>
          </template>
        </ReportCard>

      </div>
      <FooterDash>

      </FooterDash>
      
    </div>
  </AppLayout>
</template>

<style scoped>
@reference "../../style.css";

.reports-container {
  @apply flex flex-col gap-8 lg:gap-12;
  @apply max-w-7xl mx-auto;
  @apply px-4 sm:px-6 lg:px-8;
}

.reports-grid {
  @apply grid gap-6 lg:gap-8;

  /* Responsive grid que se adapta mejor */
  grid-template-columns: repeat(auto-fit, minmax(min(100%, 28rem), 1fr));

  /* En pantallas muy grandes, limitamos a 2 columnas para mejor UX */
  @media (min-width: 1400px) {
    grid-template-columns: repeat(2, 1fr);
    @apply max-w-6xl mx-auto;
  }

  /* En tablet, forzamos 2 columnas si hay espacio */
  @media (min-width: 768px) and (max-width: 1023px) {
    grid-template-columns: repeat(2, 1fr);
  }

  /* En móvil, una sola columna */
  @media (max-width: 767px) {
    grid-template-columns: 1fr;
    @apply gap-5;
  }
}

/* Contenedor para los campos del formulario */
.form-fields-container {
  @apply space-y-4;
  @apply p-4;
  @apply bg-transparent;
  @apply rounded-lg;
  @apply shadow-sm;
}

/* Grupo de campo individual */
.form-group {
  @apply flex flex-col gap-2;
}

/* Etiquetas de los campos */
.form-label {
  @apply text-sm font-semibold;
  @apply text-white;
  @apply tracking-wide;
}

/* Inputs de fecha */
.form-input {
  @apply w-full;
  @apply px-4 py-3;
  @apply bg-primary-dark;
  @apply rounded-lg;
  @apply shadow-sm;
  @apply text-white;
  @apply placeholder-gray-500;
  @apply transition-all duration-200;

  /* Estados de focus */
  @apply focus:ring-2 focus:ring-blue-500/10;
  @apply focus:border-blue-400;
  @apply focus:shadow-md;
  @apply focus:outline-none;

  /* Hover effect sutil */
  @apply hover:border-gray-300;
  @apply hover:shadow-md;
}

/* Mejorar la apariencia del input date en diferentes navegadores */
.form-input::-webkit-calendar-picker-indicator {
  @apply cursor-pointer;
  @apply opacity-60 hover:opacity-100;
  @apply transition-opacity duration-200;
}



/* Mejoras adicionales para pantallas pequeñas */
@media (max-width: 640px) {
  .reports-container {
    @apply gap-6;
    @apply px-3;
  }

  .form-fields-container {
    @apply p-3;
    @apply space-y-3;
    @apply bg-white;
  }

  .form-input {
    @apply py-2.5;
    @apply text-base;
    /* Evita zoom en iOS */
  }
}

/* Estados de loading para toda la grid */
.reports-grid[data-loading="true"] {
  @apply pointer-events-none;
  @apply opacity-75;
}
</style>