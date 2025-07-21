<script setup>
import { computed } from 'vue';

const props = defineProps({
    // Recibe el array de objetos de meseros
    waitersData: {
        type: Array,
        required: true,
        default: () => []
    }
});


// Objeto para mapear el ranking a la clase de color de la medalla
const medalColors = {
    1: 'text-amber-500',       // Oro
    2: 'text-gray-400',        // Plata
    3: 'text-amber-700'        // Bronce
};




</script>

<template>
    <div class="top-waiters-container">
        <div v-if="waitersData.length === 0" class="empty-state">
            <div class="empty-icon-wrapper">
                <i-mdi-account-group-outline class="w-8 h-8 text-gray-400" />
            </div>
            <p class="empty-text">No hay datos de meseros disponibles</p>
            <p class="empty-subtext">Los datos aparecerán cuando haya actividad</p>
        </div>

        <div v-else class="waiters-list">
            <div v-for="waiter in waitersData" :key="waiter.rank" class="waiter-item">

                <!-- Ranking / Medalla -->
                <div class="rank-section">
                    <div v-if="[1, 2, 3].includes(waiter.rank)" class="medal-wrapper">
                        <i-fa6-solid-medal class="medal-icon" :class="medalColors[waiter.rank]" />
                    </div>

                    <div v-else>
                        <div class="rank-number h-10 w-10 mx-auto">
                            {{ waiter.rank }}
                        </div>

                    </div>

                </div>

                <!-- Avatar y Nombre -->
                <div class="waiter-info">

                    <div class="waiter-details">
                        <h4 class="waiter-name">{{ waiter.name }}</h4>
                        <p class="waiter-position">Mesero</p>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="stats-section">
                    <div class="stat-item">
                        <span class="stat-number">{{ waiter.tables_served }}</span>
                        <span class="stat-label">Mesas</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<style scoped>
@reference '../../../style.css';

.top-waiters-container {
    @apply w-full h-full overflow-y-auto px-1;
}

.empty-state {
    @apply flex flex-col items-center justify-center h-full text-center py-8 px-4;
}

.empty-icon-wrapper {
    @apply w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4;
}

.empty-text {
    @apply text-gray-700 font-medium text-base mb-1;
}

.empty-subtext {
    @apply text-gray-500 text-sm;
}

.waiters-list {
    @apply space-y-3 py-2;
}

.waiter-item {
    @apply flex items-center gap-4 p-4 bg-white rounded-xl border border-gray-100;
    @apply shadow-sm hover:shadow-md hover:border-gray-200 transition-all duration-200;
    @apply hover:-translate-y-0.5;
}

/* Ranking Section */
.rank-section {
    @apply flex-shrink-0 w-12 flex items-center justify-center;
}

.medal-wrapper {
    @apply relative flex items-center justify-center;
}

.medal-icon {
    @apply text-2xl drop-shadow-sm;
}


.rank-number {
    @apply bg-gradient-to-br from-slate-100 to-slate-200 text-slate-700 font-bold;
    @apply rounded-full flex items-center justify-center shadow-sm border border-slate-200;
    @apply hover:from-slate-200 hover:to-slate-300 transition-colors duration-200;
    aspect-ratio: 1/1;
    /* Garantiza relación cuadrada */
    flex-shrink: 0;
    /* Previene compresión en flexbox */
}

/* Waiter Info Section */
.waiter-info {
    @apply flex items-center gap-3 flex-1 min-w-0;
}

.avatar {
    @apply w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0;
    @apply shadow-sm ring-2 ring-white;
}

.avatar-initials {
    @apply text-sm font-bold tracking-wide;
}

.waiter-details {
    @apply flex flex-col min-w-0;
}

.waiter-name {
    @apply font-semibold text-gray-800 text-sm leading-tight truncate;
}

.waiter-position {
    @apply text-xs text-gray-500 leading-tight;
}

/* Stats Section */
.stats-section {
    @apply flex-shrink-0;
}

.stat-item {
    @apply flex flex-col items-center text-center bg-gray-50 rounded-lg px-3 py-2 min-w-[60px];
}

.stat-number {
    @apply text-lg font-bold text-gray-800 leading-none;
}

.stat-label {
    @apply text-xs text-gray-500 font-medium;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .waiter-item {
        @apply gap-3 p-3;
    }

    .rank-section {
        @apply w-10;
    }

    .medal-icon {
        @apply text-xl;
    }

    .avatar {
        @apply w-9 h-9;
    }

    .waiter-name {
        @apply text-xs;
    }

    .stat-item {
        @apply px-2 py-1.5 min-w-[50px];
    }

    .stat-number {
        @apply text-base;
    }

    /* Ajustes responsive para los rankings */
    .rank-number {
        @apply w-8 h-8 text-xs;
    }
}
</style>