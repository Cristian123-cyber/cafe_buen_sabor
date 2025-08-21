<script setup>
import { defineProps, defineEmits, ref, computed } from 'vue';

let debounceTimer = null;
// --- PROPS ---
// Este componente es "controlado" por su padre. Recibe los valores
// actuales de los filtros a través de props.
const props = defineProps({
    /**
     * Valor actual del campo de búsqueda. Usado con v-model.
     */
    searchTerm: {
        type: String,
        required: true,
    },
    searchLabel: {
        type: String,
        default: 'Buscar'
    },

    placeholderSearch: {
        type: String,
        default: 'Nombre, email o información...'
    },


    /**
     * Valor actual del rol seleccionado. Usado con v-model.
     */
    selectedRole: {
        type: [String, Number],
        required: false,
    },
    selectedState: {
        type: [String, Number],
        required: false,
    },
    /**
     * Array de objetos para poblar las opciones del select.
     * Formato esperado: [{ value: 'admin', label: 'Administrador' }]
     */
    roleOptions: {
        type: Array,
        required: false,
        // Validador simple para asegurar que recibimos un array no vacío
        validator: (value) => Array.isArray(value)
    },

    titleRoleOptions: {
        type: String,
        default: 'Filtrar por rol'
    },
    stateOptions: {
        type: Array,
        required: false,
        // Validador simple para asegurar que recibimos un array no vacío
        validator: (value) => Array.isArray(value)
    },

    titleStateOptions: {
        type: String,
        default: 'Filtrar por estado'
    },
    buttonCreateText: {
        type: String,
        default: 'Crear'
    },
    showCreate: {
        type: Boolean,
        default: true
    },
    /**
     * Cuando es true, deshabilita los controles del toolbar.
     */
    loading: {
        type: Boolean,
        default: false,
    },
});

const isOpen = ref(false);
const isOpenState = ref(false);

const handleBlur = () => {
    setTimeout(() => {
        isOpen.value = false; // Cierra el select después de un breve retraso
    }, 150); // Ajusta el tiempo según sea necesario
};
const handleBlurState = () => {
    setTimeout(() => {
        isOpenState.value = false; // Cierra el select después de un breve retraso
    }, 150); // Ajusta el tiempo según sea necesario
};

// Computed para verificar si hay filtros activos
const hasActiveFilters = computed(() => {
    const role = props.selectedRole;
    const state = props.selectedState;

    const isRoleActive = role !== 0 && role !== -1 && role !== null && role !== undefined && role !== '' && role !== '0';
    const isStateActive = state !== 0 && state !== -1 && state !== null && state !== undefined && state !== '' && state !== '0';

    return props.searchTerm.trim() !== '' || isRoleActive || isStateActive;
});

// --- EMITS ---
// Definimos los eventos que este componente puede emitir.
// Esto permite al padre usar v-model:searchTerm y v-model:selectedRole.
const emit = defineEmits([
    'update:searchTerm',
    'update:selectedRole',
    'update:selectedState',
    'create'
]);

/**
 * Maneja el evento 'input' del campo de búsqueda y emite el nuevo valor.
 * @param {Event} event - El evento del DOM.
 */
function onSearchInput(event) {
    const value = event.target.value;

    if (debounceTimer) clearTimeout(debounceTimer);

    // Asignamos nuevo timer
    debounceTimer = setTimeout(() => {
        console.log('emitiendo cambio input');
        emit('update:searchTerm', value);
    }, 400); // <-- Puedes ajustar el delay a tu gusto
}

/**
 * Maneja el evento 'change' del select y emite el nuevo valor.
 * @param {Event} event - El evento del DOM.
 */
function onRoleChange(event) {
    console.log('emitiendo cambio select');
    emit('update:selectedRole', event.target.value);
}
function onStateChange(event) {
    console.log('emitiendo cambio state');
    emit('update:selectedState', event.target.value);
}

/**
 * Limpia todos los filtros activos
 */
function clearAllFilters() {
    emit('update:searchTerm', '');
    emit('update:selectedRole', 0); // Asumiendo que 0 es "Todos" o el valor por defecto
    emit('update:selectedState', 0); // Asumiendo que 0 es "Todos" o el valor por defecto
}
</script>

<template>
    <div class="toolbar-container">
        <!-- Header compacto con título y acción principal -->
        <div class="toolbar-header">
            <div class="toolbar-title">
                <i-mdi-filter-variant class="toolbar-title-icon" />
                <h3 class="toolbar-title-text">Filtros</h3>
            </div>

            <div class="toolbar-actions">
                <!-- Botón limpiar filtros -->
                <BaseButton v-if="hasActiveFilters && !loading" @click="clearAllFilters" variant="terciary"
                    :disabled="loading">
                    <template #icon-left>

                        <i-mdi-filter-remove class="clear-filters-icon" />
                    </template>
                    Limpiar
                </BaseButton>

                

                <BaseButton v-if="showCreate" variant="accent" size="md" @click="emit('create')" :disabled="loading">

                    <template #icon-left>
                        <slot name="create-btn-icon">
                            <i-gridicons-add></i-gridicons-add>
                        </slot>
                    </template>
                    <span class="hidden sm:block"> {{ buttonCreateText }} </span>
                </BaseButton>
            </div>
        </div>

        <!-- Filtros en una sección separada -->
        <div class="toolbar-filters">
            <!-- Loading state -->
            <div class="filters-grid">

                <!-- Campo de búsqueda mejorado -->
                <div class="filter-item search-filter">
                    <label class="filter-label">
                        <i-mdi-magnify class="filter-label-icon" />
                        {{ searchLabel }}
                    </label>
                    <div class="search-input-container">
                        <div class="search-input-wrapper">
                            <i-mdi-magnify class="search-input-icon" />
                            <input type="text" :value="searchTerm" @input="onSearchInput"
                                :placeholder="placeholderSearch" class="search-input" aria-label="Buscar usuario" />
                            <div v-if="searchTerm" class="search-clear" @click="emit('update:searchTerm', '')">
                                <i-mdi-close class="w-4 h-4" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Select de roles mejorado -->
                <div v-if="roleOptions" class="filter-item role-filter">
                    <label class="filter-label">
                        <i-mdi-account-group class="filter-label-icon" />
                        {{ titleRoleOptions }}
                    </label>

                    <!-- Mantenemos tu estructura form-group original pero con clases específicas -->
                    <div class="role-form-group variant-light">
                        <div class="role-select-wrapper">
                            <select :value="selectedRole" @change="onRoleChange" :disabled="loading"
                                class="role-form-input variant-light" @focus="isOpen = true" @blur="handleBlur">
                                <option v-if="roleOptions.length !== 0" v-for="option in roleOptions"
                                    :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>

                            <div class="role-select-chevron">
                                <i-mynaui-chevron-down-solid :class="['chevron-icon', { 'chevron-open': isOpen }]" />
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="stateOptions" class="filter-item role-filter">
                    <label class="filter-label">
                        <i-gg-check-o class="filter-label-icon" />
                        {{ titleStateOptions }}
                    </label>

                    <!-- Mantenemos tu estructura form-group original pero con clases específicas -->
                    <div class="role-form-group variant-light">
                        <div class="role-select-wrapper">
                            <select :value="selectedState" @change="onStateChange" :disabled="loading"
                                class="role-form-input variant-light" @focus="isOpenState = true"
                                @blur="handleBlurState">
                                <option v-if="stateOptions.length !== 0" v-for="option in stateOptions"
                                    :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>

                            <div class="role-select-chevron">
                                <i-mynaui-chevron-down-solid
                                    :class="['chevron-icon', { 'chevron-open': isOpenState }]" />
                            </div>
                        </div>
                    </div>
                </div>



            </div>


        </div>
    </div>
</template>

<style scoped>
@reference "../../style.css";

/* Contenedor principal */
.toolbar-container {
    @apply bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden;
}

/* Header del toolbar */
.toolbar-header {
    @apply flex items-center justify-between p-4 bg-primary border-b border-gray-100;
}

.toolbar-title {
    @apply flex items-center gap-2;
}

.toolbar-title-icon {
    @apply w-5 h-5 text-white/80;
}

.toolbar-title-text {
    @apply text-lg font-semibold text-white m-0;
}

.state-wrapper {
    @apply flex flex-1 items-center justify-center p-4;
    min-height: 320px;
    max-height: 100%;
    /* Nuevo: Control de altura */
}

.toolbar-actions {
    @apply flex items-center gap-3;
}

/* Sección de filtros */
.toolbar-filters {
    @apply relative p-5;
}

.filters-grid {
    @apply grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5;
}

/* Items de filtro */
.filter-item {
    @apply space-y-2;
}

.filter-label {
    @apply flex items-center gap-1.5 text-xs font-bold text-gray-600 mb-1;
}

.filter-label-icon {
    @apply w-3.5 h-3.5 text-gray-500;
}

/* Campo de búsqueda mejorado */
.search-filter {
    @apply md:col-span-2 xl:col-span-1;
}

.search-input-container {
    @apply relative;
}

.search-input-wrapper {
    @apply relative flex items-center;
}

.search-input-icon {
    @apply absolute left-3 w-4 h-4 text-gray-400 z-10;
}

.search-input {
    @apply w-full pl-9 pr-9 py-2.5 text-sm rounded-lg border border-gray-200 bg-white text-gray-900;
    @apply hover:border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent/20 focus:outline-none;
    @apply transition-all duration-200;
    @apply placeholder:text-gray-400;
}

.search-clear {
    @apply absolute right-2.5 p-1 rounded-md hover:bg-gray-100 cursor-pointer transition-colors duration-150;
}

/* Estilos específicos para el select de roles (mantiene tu estructura) */
.role-form-group {
    @apply flex flex-col gap-1 w-full;
}

.role-form-group.variant-light .role-form-input {
    @apply bg-white text-gray-900 border border-gray-200 rounded-lg;
    @apply transition-all duration-200;
    @apply hover:border-gray-300 focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20;
    @apply w-full py-2.5 px-3 pr-9 appearance-none text-sm;
}

.role-select-wrapper {
    @apply relative;
}

.role-select-chevron {
    @apply absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none;
}

.chevron-icon {
    @apply w-4 h-4 text-gray-400 transition-transform duration-200;
}

.chevron-open {
    @apply rotate-180;
}

/* Loading overlay */
.loading-overlay {
    @apply absolute inset-0 bg-white/80 backdrop-blur-sm flex items-center justify-center gap-3 rounded-xl;
}

.loading-spinner {
    @apply w-5 h-5 border-2 border-accent/30 border-t-accent rounded-full animate-spin;
}

.loading-text {
    @apply text-sm text-gray-500 font-medium;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .filters-grid {
        @apply grid-cols-1 gap-4;
    }

    .toolbar-filters {
        @apply p-4;
    }

    .search-input {
        @apply py-2.5;
    }

    .role-form-input {
        @apply py-2.5;
    }

    .btn-primary {
        @apply px-4 py-2;
    }
}

/* Estados de hover y focus mejorados */
@media (hover: hover) {
    .search-input:hover {
        @apply shadow-sm;
    }

    .role-form-input:hover {
        @apply shadow-sm;
    }
}

/* Animaciones suaves */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(8px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.filter-item {
    animation: fadeIn 0.3s ease-out;
}

.filter-item:nth-child(1) {
    animation-delay: 0.05s;
}

.filter-item:nth-child(2) {
    animation-delay: 0.1s;
}

/* Botón limpiar filtros en header */
.clear-filters-btn-header {
    @apply flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-white/80;
    @apply bg-white/10 border border-white/20 rounded-lg;
    @apply hover:bg-white/20 hover:border-white/30 hover:text-white;
    @apply focus:outline-none focus:ring-2 focus:ring-white/20;
    @apply transition-all duration-200;
    @apply disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-white/10;
}

.clear-filters-icon {
    @apply w-4 h-4;
}
</style>