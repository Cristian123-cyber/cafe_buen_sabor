<script setup>
import { computed } from 'vue';

// --- PROPS ---

const props = defineProps({
  /**
   * Array de objetos que configuran las columnas.
   * Formato esperado: [{ key: 'id', label: 'ID' }, { key: 'name', label: 'Nombre' }]
   */
  columns: {
    type: Array,
    required: true,
  },
  /**
   * Array de objetos con los datos a mostrar en la tabla.
   */
  data: {
    type: Array,
    required: true,
  },
  /**
   * Propiedad única en cada objeto de 'data' para usar como :key en el v-for.
   * Esencial para un renderizado eficiente en Vue. Ej: 'id'
   */
  trackBy: {
    type: String,
    required: true,
  },
  /**
   * Cuando es true, muestra un spinner de carga en lugar de la tabla.
   */
  loading: {
    type: Boolean,
    default: false,
  },
  /**
   * Activa el estilo de filas con colores alternados (cebreado).
   */
  striped: {
    type: Boolean,
    default: false,
  },
  /**
   * Activa el efecto visual al pasar el cursor sobre las filas.
   */
  hover: {
    type: Boolean,
    default: true,
  },
  /**
   * Tamaño del componente (sm, md, lg)
   */
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  /**
   * Aplica bordes a todas las celdas
   */
  bordered: {
    type: Boolean,
    default: false,
  }
});

// --- COMPUTED ---

/**
 * Calcula las clases CSS para el elemento <table> principal
 * basándose en las props de estilo.
 */
const tableClasses = computed(() => [
  'base-table',
  `base-table-${props.size}`,
  {
    'base-table-striped': props.striped,
    'base-table-hover': props.hover,
    'base-table-bordered': props.bordered,
  }
]);

</script>

<template>
  <div class="table-wrapper">
    <!-- Estado de Carga -->
    <div v-if="loading" class="loading-state">
      <i-mdi-loading class="spinner" />
      <span class="loading-text">Cargando datos...</span>
    </div>

    <!-- Estado Vacío -->
    <div v-else-if="!data.length" class="empty-state">
      <slot name="empty">
        <div class="empty-content">
          <i-mdi-database-off-outline class="empty-icon" />
          <span class="empty-title">No se encontraron datos</span>
          <p class="empty-description">Intenta ajustar los filtros o agregar nuevos registros.</p>
        </div>
      </slot>
    </div>

    <!-- Tabla con Datos -->
    <div v-else class="table-container">
      <table :class="tableClasses">
        <thead class="table-head">
          <tr>
            <th
              v-for="column in columns"
              :key="column.key"
              class="table-header-cell"
            >
              <slot :name="`header(${column.key})`" :column="column">
                {{ column.label }}
              </slot>
            </th>
          </tr>
        </thead>
        <tbody class="table-body">
          <tr 
            v-for="row in data" 
            :key="row[trackBy]"
            class="table-row"
          >
            <td
              v-for="column in columns"
              :key="`${row[trackBy]}-${column.key}`"
              class="table-body-cell"
            >
              <slot :name="`cell(${column.key})`" :value="row[column.key]" :row="row">
                {{ row[column.key] }}
              </slot>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped>
@reference '../../style.css';

/* === CONTENEDOR PRINCIPAL === */
.table-wrapper {
  @apply w-full;
}

.table-container {
  @apply overflow-x-auto rounded-lg border border-border shadow-sm;
}

/* === TABLA BASE === */
.base-table {
  @apply w-full border-collapse text-left bg-white;
}

/* === CABECERA === */
.table-head {
  @apply bg-accent border-b border-gray-200;
}

.table-header-cell {
  @apply font-semibold text-gray-700 tracking-wide uppercase text-xs;
  @apply transition-colors duration-200;
}

/* === CUERPO DE LA TABLA === */
.table-body {
  @apply divide-y divide-gray-100;
}

.table-row {
  @apply transition-all duration-200 ease-in-out;
}

.table-body-cell {
  @apply text-gray-800 align-middle;
  @apply transition-all duration-200;
}

/* === TAMAÑOS === */
.base-table-sm .table-header-cell {
  @apply px-3 py-2;
}

.base-table-sm .table-body-cell {
  @apply px-3 py-2 text-sm;
}

.base-table-md .table-header-cell {
  @apply px-4 py-3;
}

.base-table-md .table-body-cell {
  @apply px-4 py-3 text-sm;
}

.base-table-lg .table-header-cell {
  @apply px-6 py-4;
}

.base-table-lg .table-body-cell {
  @apply px-6 py-4 text-base;
}

/* === EFECTOS DE HOVER === */
.base-table-hover .table-row:hover {
  @apply bg-blue-50;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.base-table-hover .table-row:hover .table-body-cell {
  @apply text-blue-900;
}

/* === FILAS ALTERNADAS === */
.base-table-striped .table-row:nth-child(even) {
  @apply bg-gray-50;
}

/* === BORDES === */
.base-table-bordered {
  @apply border border-gray-200;
}

.base-table-bordered .table-header-cell,
.base-table-bordered .table-body-cell {
  @apply border-r border-gray-200;
}

.base-table-bordered .table-header-cell:last-child,
.base-table-bordered .table-body-cell:last-child {
  @apply border-r-0;
}

/* === ESTADO DE CARGA === */
.loading-state {
  @apply flex flex-col items-center justify-center py-16 space-y-4;
  @apply bg-white rounded-lg border border-gray-200 shadow-sm;
}

.spinner {
  @apply w-8 h-8 text-blue-600 animate-spin;
}

.loading-text {
  @apply text-gray-600 text-sm font-medium;
}

/* === ESTADO VACÍO === */
.empty-state {
  @apply w-full rounded-lg border-2 border-dashed border-gray-300 bg-gray-50;
  @apply p-12 text-center transition-colors duration-200;
}

.empty-content {
  @apply flex flex-col items-center gap-4 text-gray-500;
}

.empty-icon {
  @apply w-16 h-16 text-gray-400;
}

.empty-title {
  @apply text-lg font-semibold text-gray-700;
}

.empty-description {
  @apply text-sm text-gray-500 max-w-md;
}

/* === RESPONSIVE === */
@media (max-width: 768px) {
  .table-container {
    @apply rounded-none border-x-0;
  }
  
  .base-table-md .table-header-cell,
  .base-table-md .table-body-cell {
    @apply px-3 py-2 text-sm;
  }
  
  .base-table-lg .table-header-cell,
  .base-table-lg .table-body-cell {
    @apply px-4 py-3 text-sm;
  }
}

/* === MEJORAS ADICIONALES === */
.table-row:active {
  @apply bg-blue-100;
}

.table-header-cell:first-child {
  @apply rounded-tl-lg;
}

.table-header-cell:last-child {
  @apply rounded-tr-lg;
}

/* Focus para accesibilidad */
.table-row:focus-within {
  @apply outline-2 outline-blue-500 outline-offset-2;
}
</style>