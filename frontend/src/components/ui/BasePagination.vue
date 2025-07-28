<!-- components/ui/BasePagination.vue -->
<template>
    <!-- Wrapper principal que contiene todo -->
    <div class="pagination-container">
        <div class="pagination-wrapper">
            <!-- Información de elementos (móvil superior, desktop izquierda) -->
            <div class="pagination-info">
                <div class="info-content">
                    <div class="per-page-controls">
                        <span class="info-text">Elementos por página:</span>
                        <select :value="currentPerPage" @change="handlePerPageChange" class="per-page-select"
                            aria-label="Elementos por página">
                            <option v-for="size in perPageOptions" :key="size" :value="size">
                                {{ size }}
                            </option>
                        </select>
                    </div>
                    <div class="items-range">
                        <span class="info-text">{{ startItem }}-{{ endItem }} de {{ total }}</span>
                    </div>
                </div>
            </div>

            <!-- Controles de navegación -->
            <div class="navigation-section">
                <!-- Página actual en móvil -->
                <div class="mobile-page-info">
                    <span class="mobile-page-text">{{ currentPage }} / {{ totalPages }}</span>
                </div>

                <!-- Controles de navegación -->
                <div class="nav-controls">
                    <!-- Primera página -->
                    <button @click="goToPage(1)" :disabled="currentPage === 1" class="nav-btn nav-btn--first"
                        title="Primera página">
                        <i-mdi-chevron-double-left class="nav-icon" />
                    </button>

                    <!-- Página anterior -->
                    <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1" class="nav-btn"
                        title="Página anterior">
                        <i-mdi-chevron-left class="nav-icon" />
                    </button>

                    <!-- Páginas numéricas (solo desktop) -->
                    <div class="page-numbers">
                        <template v-for="page in visiblePages" :key="page">
                            <button v-if="typeof page === 'number'" @click="goToPage(page)" :class="[
                                'page-btn',
                                { 'page-btn--active': page === currentPage }
                            ]" :title="`Página ${page}`">
                                {{ page }}
                            </button>
                            <span v-else class="page-ellipsis">⋯</span>
                        </template>
                    </div>

                    <!-- Página siguiente -->
                    <button @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages" class="nav-btn"
                        title="Página siguiente">
                        <i-mdi-chevron-right class="nav-icon" />
                    </button>

                    <!-- Última página -->
                    <button @click="goToPage(totalPages)" :disabled="currentPage === totalPages"
                        class="nav-btn nav-btn--last" title="Última página">
                        <i-mdi-chevron-double-right class="nav-icon" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

// Props del componente
const props = defineProps({
    currentPage: {
        type: Number,
        default: 1,
        validator: (value) => value > 0
    },
    perPage: {
        type: Number,
        default: 10,
        validator: (value) => value > 0
    },
    total: {
        type: Number,
        default: 0,
        validator: (value) => value >= 0
    },
    perPageOptions: {
        type: Array,
        default: () => [5, 10, 25, 50, 100]
    },
    maxVisiblePages: {
        type: Number,
        default: 5,
        validator: (value) => value >= 3 && value % 2 === 1
    }
})

// Eventos emitidos
const emit = defineEmits(['page-changed', 'per-page-changed'])

// Computed properties
const currentPerPage = computed(() => props.perPage)

const totalPages = computed(() => {
     // Si no hay elementos en total, no hay páginas.
    if (props.total === 0) {
        return 0;
    }
    // De lo contrario, calcula las páginas como siempre.
    return Math.ceil(props.total / props.perPage);
})

const startItem = computed(() => {
    if (props.total === 0) return 0
    return (props.currentPage - 1) * props.perPage + 1
})

const endItem = computed(() => {
    const end = props.currentPage * props.perPage
    return Math.min(end, props.total)
})

// Lógica mejorada para páginas visibles
const visiblePages = computed(() => {
    const current = props.currentPage
    const total = totalPages.value
    const max = props.maxVisiblePages

    if (total <= max) {
        return Array.from({ length: total }, (_, i) => i + 1)
    }

    const half = Math.floor(max / 2)
    let start = Math.max(1, current - half)
    let end = Math.min(total, current + half)

    // Ajustar para mantener el número exacto de páginas visibles
    if (end - start + 1 < max) {
        if (start === 1) {
            end = Math.min(total, start + max - 1)
        } else {
            start = Math.max(1, end - max + 1)
        }
    }

    const pages = []

    // Primera página + elipsis
    if (start > 1) {
        pages.push(1)
        if (start > 2) pages.push('...')
    }

    // Páginas del rango
    for (let i = start; i <= end; i++) {
        pages.push(i)
    }

    // Elipsis + última página
    if (end < total) {
        if (end < total - 1) pages.push('...')
        pages.push(total)
    }

    return pages
})

// Métodos
const goToPage = (page) => {
    if (page >= 1 && page <= totalPages.value && page !== props.currentPage) {
        emit('page-changed', page)
    }
}

const handlePerPageChange = (event) => {
    const newPerPage = parseInt(event.target.value)
    emit('per-page-changed', newPerPage)
}
</script>

<style scoped>
@reference "../../style.css";

/* Contenedor principal - Wrapper que se adapta a cualquier contexto */
.pagination-container {
    @apply w-full;
    @apply bg-surface border border-border/50 rounded-xl;
    @apply shadow-sm backdrop-blur-sm;
    @apply overflow-hidden;
}

/* Wrapper interno del contenido */
.pagination-wrapper {
    @apply flex flex-col sm:flex-row sm:items-center sm:justify-between;
    @apply p-4 gap-4;
    @apply min-h-[3.5rem];
}

/* === INFORMACIÓN Y CONTROLES PER PAGE === */
.pagination-info {
    @apply w-full sm:w-auto;
    @apply flex items-start justify-center sm:justify-start;
    @apply order-2 sm:order-1;
}

.info-content {
    @apply flex flex-col sm:flex-row items-center gap-3;
    @apply w-full sm:w-auto;
}

.per-page-controls {
    @apply flex items-center gap-2;
    @apply w-full sm:w-auto justify-center sm:justify-start;
    @apply order-1;
}

.items-range {
    @apply flex items-center justify-center sm:justify-start;
    @apply w-full sm:w-auto;
    @apply order-2;
    @apply mt-2 sm:mt-0;
}

.info-text {
    @apply text-sm text-text-muted font-medium;
    @apply whitespace-nowrap;
}

.per-page-select {
    @apply px-3 py-1.5 min-w-[5rem];
    @apply bg-surface-dark border border-border/70 rounded-lg;
    @apply text-sm font-medium text-text;
    @apply transition-all duration-200 ease-out;
    @apply focus:outline-none focus:ring-2 focus:ring-primary/30;
    @apply hover:border-primary/30 hover:bg-surface;
    @apply cursor-pointer;
}

/* === SECCIÓN DE NAVEGACIÓN === */
.navigation-section {
    @apply flex flex-col items-center justify-center sm:justify-end;
    @apply gap-3 order-1 sm:order-2;
}

/* Información de página en móvil */
.mobile-page-info {
    @apply flex sm:hidden;
}

.mobile-page-text {
    @apply px-3 py-1.5 rounded-lg;
    @apply bg-surface-dark border border-border/50;
    @apply text-sm font-semibold text-text;
    @apply min-w-[4rem] text-center;
}

/* Controles de navegación */
.nav-controls {
    @apply flex items-center gap-1;
}

/* Botones de navegación */
.nav-btn {
    @apply flex items-center justify-center;
    @apply w-9 h-9 rounded-lg;
    @apply bg-transparent border border-border/50;
    @apply text-text-muted;
    @apply transition-all duration-200 ease-out;
    @apply focus:outline-none focus:ring-2 focus:ring-primary/30;
    @apply disabled:opacity-50 disabled:cursor-not-allowed;
    @apply hover:enabled:border-primary/50 hover:enabled:text-primary hover:enabled:bg-primary/5;
}

.nav-btn--first,
.nav-btn--last {
    @apply hidden sm:flex;
}

.nav-icon {
    @apply w-4 h-4;
}

/* Páginas numéricas */
.page-numbers {
    @apply hidden md:flex items-center gap-1 mx-2;
}

.page-btn {
    @apply flex items-center justify-center;
    @apply w-9 h-9 rounded-lg;
    @apply text-sm font-semibold;
    @apply bg-transparent border border-transparent;
    @apply text-text-muted;
    @apply transition-all duration-200 ease-out;
    @apply focus:outline-none;
    @apply hover:bg-primary/10 hover:text-primary;
}

.page-btn--active {
    @apply bg-primary border-primary text-white;
    @apply shadow-sm shadow-primary/20;
    @apply hover:bg-primary hover:text-white hover:border-primary;
}

.page-ellipsis {
    @apply flex items-center justify-center;
    @apply w-9 h-9 text-text-muted;
    @apply text-lg font-bold select-none;
}

/* === RESPONSIVE BREAKPOINTS === */

/* Móvil pequeño */
@media (max-width: 374px) {
    .pagination-wrapper {
        @apply p-3 gap-3;
        @apply min-h-[3rem];
    }

    .nav-btn,
    .page-btn {
        @apply w-8 h-8;
    }

    .nav-icon {
        @apply w-3.5 h-3.5;
    }

    .per-page-select {
        @apply px-2 py-1 text-xs min-w-[3rem];
    }

    .mobile-page-text {
        @apply px-2.5 py-1 text-xs min-w-[3.5rem];
    }

    .info-text {
        @apply text-xs;
    }
}

/* Tablet y desktop */
@media (min-width: 768px) {
    .pagination-wrapper {
        @apply px-6 py-4;
    }
}

/* Responsive ajustado */
@media (max-width: 640px) {
    .per-page-controls {
        @apply border-b border-border/10 pb-3;
        @apply w-full;
    }

    .items-range {
        @apply pt-2;
    }

    .per-page-select {
        @apply min-w-[4rem];
    }
}

/* === MEJORAS DE ACCESIBILIDAD === */
.pagination-container *:focus-visible {
    @apply outline-2 outline-offset-2 outline-primary;
}

/* Animaciones suaves (respeta prefers-reduced-motion) */
@media (prefers-reduced-motion: no-preference) {

    .nav-btn,
    .page-btn,
    .per-page-select {
        @apply transition-all duration-200 ease-out;
    }
}



/* === ESTADOS DE CARGA (OPCIONAL) === */
.pagination-container:has(.loading) {
    @apply opacity-60 pointer-events-none;
}

/* === BREAKPOINT PERSONALIZADO PARA PANTALLAS PEQUEÑAS === */
@media (min-width: 475px) {
    .navigation-section {
        @apply flex-row gap-4;
    }

    .nav-btn--first,
    .nav-btn--last {
        @apply flex;
    }
}
</style>