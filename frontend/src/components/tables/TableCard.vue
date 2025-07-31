<template>
    <div class="table-card-container">
        <span :class="statusClass" class="status-dot" :title="statusTooltip"></span>

        <div class="table-card-body">
            <div class="table-number-highlight">
                <span class="table-number-large">{{ table.table_number }}</span>
            </div>

            <div class="table-info">
                <h3 class="table-title">Mesa {{ table.table_number }}</h3>
                <p class="table-status-text">
                    {{ statusText }}
                </p>
                <p v-if="table.table_status === 'FREE' && table.token_expiration" class="table-expiration">
                    <i-mdi-clock-outline class="w-4 h-4 mr-1.5 text-green-500" />
                    <p class="text-green-500">
                        Token expira {{ formattedExpiration }}
                    </p>
                </p>
                <p v-else-if="table.table_status === 'OCCUPIED'" class="table-expiration">
                    <i-mdi-clock-outline class="w-4 h-4 mr-1.5 text-blue-500" />
                <p class="text-blue-500">
                    Expiración pausada
                </p>
                </p>
            </div>
        </div>

        <div class="table-card-actions">
            <slot name="actions"></slot>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
// 3. Importaciones de date-fns para el formato de fecha relativo (Requerimiento 3)
import { formatDistanceToNow } from 'date-fns';
import { es } from 'date-fns/locale';

// Definición de props (sin cambios)
const props = defineProps({
    table: {
        type: Object,
        required: true,
        validator: (value) => {
            return (
                'id_table' in value &&
                'table_number' in value &&
                'table_status' in value &&
                'token_expiration' in value
            );
        },
    },
});

// --- Computed Properties para Lógica de UI (sin cambios en la lógica) ---

const isOccupied = computed(() => props.table.table_status === 'OCCUPIED');
const isInactive = computed(() => props.table.table_status === 'INACTIVE');

const statusClass = computed(() =>
    isOccupied.value ? 'bg-success' : isInactive.value ? 'bg-primary' : 'bg-text-muted'
);

const statusTooltip = computed(() =>
    isOccupied.value ? 'Mesa Ocupada' : isInactive.value ? 'Mesa inactiva' : 'Mesa Libre'
);

const statusText = computed(() =>
    isOccupied.value ? 'Ocupada' : isInactive.value ? 'Inactiva' : 'Libre'
);

// 3. Formato de expiración actualizado con date-fns (Requerimiento 3)
const formattedExpiration = computed(() => {
    if (!props.table.token_expiration) return 'N/A';
    try {
        return formatDistanceToNow(new Date(props.table.token_expiration), {
            addSuffix: true,
            locale: es,
        });
    } catch (error) {
        console.error("Invalid date for token_expiration:", props.table.token_expiration);
        return 'fecha inválida';
    }
});
</script>

<style scoped>
/* IMPORTANTE: Esta línea es obligatoria para que @apply funcione con el tema global
  en Tailwind v4. Asegúrate de que la ruta sea correcta desde este archivo.
*/
@reference "../../style.css";

.table-card-container {
    @apply relative flex flex-col bg-surface border border-border-light rounded-lg shadow-sm transition-all duration-300 hover:shadow-lg hover:-translate-y-1 overflow-hidden;
}

.status-dot {
    @apply absolute top-3 right-3 w-3.5 h-3.5 rounded-full z-10;
    /* Borde mejorado para destacar sobre cualquier fondo */
    @apply ring-2 ring-surface;
}

/* El cuerpo ahora tiene el padding principal para unificar el espaciado */
.table-card-body {
    @apply p-4 flex-grow flex flex-col;
}

/* 1. Estilo para el recuadro del número (Requerimiento 1) */
.table-number-highlight {
    @apply flex items-center justify-center p-6 mb-4 bg-primary-light rounded-lg;
    /* Se quita el fondo de cabecera anterior para un look integrado */
}

.table-number-large {
    /* Usamos font-cafes definido en el theme */
    @apply font-cafes font-extrabold text-7xl text-text-light opacity-90 select-none;
}

/* Contenedor para la información textual debajo del highlight */
.table-info {
    @apply text-left;
    /* Asegura la alineación a la izquierda */
}

.table-title {
    @apply text-xl font-bold font-cafes text-text;
}

.table-status-text {
    @apply text-sm font-semibold text-text-muted mb-2;
}

.table-expiration {
    @apply flex items-center text-xs text-text-muted;
}

/* 2. Área de acciones mejorada para ser fluida y responsiva (Requerimiento 2) */
.table-card-actions {
    @apply p-3 bg-surface;
    /* Flex-wrap permite que los botones pasen a la siguiente línea si no caben */
    /* Gap-2 añade espaciado consistente en horizontal y vertical */
    @apply flex flex-wrap items-center justify-start gap-3;
}
</style>