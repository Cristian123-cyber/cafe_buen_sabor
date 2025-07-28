<script setup>
import { computed, ref, watch } from 'vue';

// --- PROPS (Sin cambios en la lógica) ---
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

const firstRender = ref(true);



watch(() => props.data, () => {
  if (firstRender.value) {
    firstRender.value = false;
  }
});

// --- COMPUTED (Clases CSS actualizadas para el nuevo diseño) ---
const tableClasses = computed(() => [
  'premium-table',
  `premium-table--${props.size}`,
  {
    'premium-table--striped': props.striped,
    'premium-table--hover': props.hover,
    'premium-table--bordered': props.bordered,
  }
]);

</script>

<template>
  <div class="table-wrapper">
    <!-- Estado de carga -->
    <div v-if="loading && firstRender" class="state-wrapper">
        <i-svg-spinners-bars-scale-fade class="h-16 w-16 text-blue-500 opacity-80" />
    </div>

    <!-- Estado vacío -->
    <div v-else-if="!data.length" class="empty-state">
      <slot name="empty">
        <div class="empty-content">
          <div class="empty-icon-container">
            <svg class="empty-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
          </div>
          <h3 class="empty-title">No hay datos disponibles</h3>
          <p class="empty-description">No hay registros que mostrar.</p>
        </div>
      </slot>
    </div>

    <!-- Tabla principal -->
    <div v-else class="table-container">
      <table :class="tableClasses">
        <thead class="table-head">
          <tr>
            <th
              v-for="column in columns"
              :key="column.key"
              class="table-header"
            >
              <div class="header-content">
                <slot :name="`header(${column.key})`" :column="column">
                  {{ column.label }}
                </slot>
              </div>
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
              :data-label="column.label"
              class="table-cell"
              :class="{ 'cell-actions': column.key === 'actions' || column.key === 'acciones' }"
            >
              <div class="cell-content">
                <slot :name="`cell(${column.key})`" :value="row[column.key]" :row="row">
                  {{ row[column.key] }}
                </slot>
              </div>
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
  @apply w-full antialiased;
}

.table-container {
  @apply overflow-hidden bg-surface rounded-xl border border-border-light;
  box-shadow: 0 4px 12px rgba(33, 33, 33, 0.08);
  backdrop-filter: blur(10px);
}

/* === TABLA BASE === */
.premium-table {
  @apply w-full border-collapse;
  border-spacing: 0;
}

/* === CABECERA PREMIUM === */
.table-head {
  @apply hidden md:table-header-group;
}

.table-header {
  @apply text-xs font-bold text-text-light uppercase tracking-wider relative;
  @apply transition-all duration-300 ease-out;
  @apply bg-primary;
  position: relative;
  border: none;
}
.state-wrapper {
  @apply flex flex-1 items-center justify-center p-4;
  min-height: 320px;
  max-height: 100%; /* Nuevo: Control de altura */
}

.table-header::before {
  content: '';
  @apply absolute inset-0 opacity-0;
  @apply transition-opacity duration-300;
  @apply bg-accent;
  opacity: 0;
}

.table-header:hover::before {
  opacity: 0.1;
}

.header-content {
  @apply relative z-10 flex items-center justify-start gap-2;
}

/* === CUERPO DE LA TABLA === */
.table-body {
  @apply text-text;
}

.table-row {
  @apply transition-all duration-300 ease-out relative;
  @apply border-b border-border-light;
}

.table-row:last-child {
  @apply border-b-0;
}

.table-cell {
  @apply align-middle relative;
  @apply transition-all duration-300 ease-out;
}

.cell-content {
  @apply flex items-center;
}

/* Mejora específica para celdas de acciones */
.cell-actions .cell-content {
  @apply justify-end md:justify-center gap-2;
}

/* === TAMAÑOS CON MEJOR ESPACIADO === */
.premium-table--sm .table-header {
  @apply px-4 py-3;
}
.premium-table--sm .table-cell {
  @apply px-4 py-3 text-sm;
}

.premium-table--md .table-header {
  @apply px-6 py-4;
}
.premium-table--md .table-cell {
  @apply px-6 py-4 text-sm;
}

.premium-table--lg .table-header {
  @apply px-8 py-5;
}
.premium-table--lg .table-cell {
  @apply px-8 py-5 text-base;
}

/* === EFECTOS HOVER MEJORADOS === */
.premium-table--hover .table-row {
  @apply cursor-pointer;
}

.premium-table--hover .table-row:hover {
  @apply bg-blue-500/10;
  @apply shadow-md;
}

.premium-table--hover .table-row:hover .table-cell {
  @apply text-primary;
}

/* === VARIANTES DE ESTILO MEJORADAS === */
.premium-table--striped .table-row:nth-child(even) {
  @apply bg-surface-dark;
}

.premium-table--bordered {
  @apply border border-border;
}

.premium-table--bordered .table-cell {
  @apply border-r border-border-light last:border-r-0;
}

/* === ESTADO VACÍO MEJORADO === */
.empty-state {
  @apply w-full rounded-xl border-2 border-dashed border-border p-12 text-center;
  @apply transition-all duration-500;
  @apply bg-surface-dark;
}

.empty-state:hover {
  @apply border-accent bg-hover;
}

.empty-content {
  @apply flex flex-col items-center gap-4 text-text-muted max-w-md mx-auto;
}

.empty-icon-container {
  @apply w-24 h-24 mx-auto rounded-full bg-surface-darker flex items-center justify-center;
  @apply transition-all duration-500;
}

.empty-state:hover .empty-icon-container {
  @apply bg-accent;
}

.empty-icon {
  @apply w-12 h-12 text-text-muted;
  @apply transition-colors duration-500;
}

.empty-state:hover .empty-icon {
  @apply text-text-light;
}

.empty-title {
  @apply text-xl font-bold text-text mt-2;
}

.empty-description {
  @apply text-sm text-text-muted leading-relaxed;
}
/* === RESPONSIVE PREMIUM (CARDS EN MOBILE) === */
@media (max-width: 767px) {
  .table-container {
    @apply shadow-none bg-transparent border-0 overflow-visible;
  }

  .premium-table {
    @apply block;
  }

  .table-head {
    @apply hidden;
  }

  .table-body {
    @apply block space-y-3;
  }

  .table-row {
    @apply block rounded-lg border p-4 relative;
    @apply bg-white;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    @apply transition-all duration-300;
    border-color: #E5E5E5;
  }

  .table-row:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    border-color: #D0D0D0;
  }

  /* Removemos la línea superior de accent */
  .table-row::before {
    display: none;
  }

  .table-cell {
    @apply block py-2 border-b-0 px-0;
    @apply flex items-start justify-between;
    @apply min-h-0;
  }

  .table-cell:not(:last-child) {
    @apply pb-2 mb-2 border-b border-gray-100;
  }

  .table-cell:last-child {
    @apply pb-0 mb-0 border-b-0;
  }

  /* Labels más sutiles y elegantes */
  .table-cell::before {
    content: attr(data-label);
    @apply font-medium text-gray-600 text-xs;
    @apply flex-shrink-0 min-w-0 mr-4;
    @apply uppercase tracking-wide;
    min-width: 80px;
  }

  .cell-content {
    @apply flex-1 text-right justify-end;
    @apply text-gray-900 font-medium text-sm;
  }

  /* Acciones mejoradas y más limpias */
  .cell-actions {
    @apply pt-3 mt-3 border-t border-gray-100;
    @apply bg-gray-50;
    @apply -mx-4 px-4 pb-0 rounded-none;
    @apply border-l-0 border-r-0 border-b-0;
  }

  .cell-actions::before {
    content: '';
    display: none;
  }

  .cell-actions .cell-content {
    @apply justify-center gap-2 py-1;
  }

  /* Mejora visual para la última fila de acciones */
  .table-row .cell-actions:last-child {
    @apply -mb-4 pb-4;
    border-radius: 0 0 0.5rem 0.5rem;
  }

  /* Variante para datos importantes */
  .table-cell[data-label*="ID"]::before,
  .table-cell[data-label*="Nombre"]::before,
  .table-cell[data-label*="Email"]::before {
    @apply text-primary font-semibold;
  }

  .table-cell[data-label*="ID"] .cell-content,
  .table-cell[data-label*="Nombre"] .cell-content {
    @apply font-semibold text-gray-900;
  }

  /* Mejora para estados o badges */
  .table-cell[data-label*="Estado"] .cell-content,
  .table-cell[data-label*="Status"] .cell-content {
    @apply font-medium;
  }
}





/* === SCROLL PERSONALIZADO === */
.table-container::-webkit-scrollbar {
  height: 8px;
}

.table-container::-webkit-scrollbar-track {
  @apply bg-surface-dark rounded-lg;
}

.table-container::-webkit-scrollbar-thumb {
  @apply bg-accent rounded-lg;
}

.table-container::-webkit-scrollbar-thumb:hover {
  @apply bg-accent-dark;
}

/* === RESPONSIVE ENHANCEMENTS === */
@media (min-width: 768px) {
  .table-container {
    @apply overflow-x-auto;
  }
  
  .premium-table {
    @apply min-w-full;
  }
}
</style>