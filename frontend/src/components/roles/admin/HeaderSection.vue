<template>
    <header class="header-container">
        <!-- Mensaje de bienvenida -->
        <div class="welcome-wrapper">
            <div class="welcome-header">
                <i-mdi-human-hello-variant v-if="!title" class="welcome-icon" />
                <i-mdi-view-dashboard-outline v-else class="welcome-icon" />
                <div>
                    <h1 class="welcome-title">{{ title ? title :  welcomeMessage }}</h1>
                    <p class="welcome-subtitle">{{ descriptionMessage }}</p>
                </div>
            </div>
            <div class="welcome-extra">
                <span class="welcome-tip">✨ {{ motivationalPhrase }}</span>
            </div>
        </div>

        <!-- Widget de fecha mejorado -->
        <div class="date-widget">
            <div class="icon-wrapper">
                <i-fluent-calendar-clock-16-regular class="date-icon" />
            </div>
            <div class="date-info">
                <span class="day-number">{{ dayNumber }}</span>
                <div class="date-details">
                    <span class="month-year">{{ monthAndYear }}</span>
                    <span class="weekday">{{ weekday }}</span>
                </div>
            </div>
        </div>
    </header>
</template>


<script setup>
import { computed, defineProps } from 'vue';
import { useAuthStore } from '../../../stores/authS';
import { getRandomPhraseByRole } from '../../../utils/motivationalPhrases.js';

// Capitalizar primera letra
const capitalize = (s) => s ? s.charAt(0).toUpperCase() + s.slice(1) : '';

const props = defineProps({
    descriptionMessage: {
        type: String,
        default: 'Aquí tienes un resumen del rendimiento de Café Buen Sabor.'
    },
    title: {
        type: String,
        default: null
    }

});

const authStore = useAuthStore();
const adminName = computed(() => authStore.user?.name?.split(' ')[0] || 'Admin');
// Frase motivadora según el rol
const motivationalPhrase = computed(() => getRandomPhraseByRole(authStore.user?.role_id));

const welcomeMessage = computed(() => {
    const hour = new Date().getHours();
    if (hour < 12) return `¡Buenos días, ${adminName.value}!`;
    if (hour < 18) return `¡Buenas tardes, ${adminName.value}!`;
    return `¡Buenas noches, ${adminName.value}!`;
});

// Fecha
const currentDate = new Date();
const dayNumber = computed(() => currentDate.getDate());
const monthAndYear = computed(() =>
    capitalize(currentDate.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' }))
);
const weekday = computed(() =>
    capitalize(currentDate.toLocaleDateString('es-ES', { weekday: 'long' }))
);
</script>


<style scoped>
@reference "../../../style.css";

.header-container {
    @apply flex flex-col justify-between gap-6 pb-6 mb-6 md:flex-row md:items-center;
}

.welcome-wrapper {
  @apply flex flex-col gap-2;
}

.welcome-header {
  @apply flex items-start gap-3;
}



.welcome-icon {
  @apply text-4xl text-accent mt-1;
}

.welcome-title {
  @apply text-3xl lg:text-4xl font-extrabold text-text leading-tight;
}

.welcome-subtitle {
  @apply text-base text-text-muted font-medium mt-1;
}

.welcome-extra {
  @apply mt-1;
}

.welcome-tip {
  @apply text-sm text-accent font-medium bg-accent/10 px-3 py-1 rounded-full inline-block;
}


.date-widget {
    @apply flex items-center gap-4 p-4 rounded-xl border border-border-dark shadow-md transition-all duration-300 transform hover:scale-102;
    background: linear-gradient(145deg, #212121 0%, #1a1a1a 50%, #0d0d0d 100%);
}

.icon-wrapper {
    @apply bg-accent/20 rounded-full p-2 flex items-center justify-center;
}

.date-icon {
    @apply text-3xl text-accent;
}

.date-info {
    @apply flex items-center gap-4;
}

.day-number {
    @apply text-5xl font-extrabold text-text-light leading-none;
}

.date-details {
    @apply flex flex-col justify-center;
}

.month-year {
    @apply text-base font-semibold text-text-light capitalize;
}

.weekday {
    @apply text-sm text-text-muted capitalize;
}
</style>