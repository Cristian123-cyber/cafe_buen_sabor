<script setup>
import { computed } from 'vue';
import { useAuthStore } from '../../stores/authS';
import { getNavigationForRole } from '../../router/navigation';

import { useAuth } from '../../composables/useAuth';

const { logout } = useAuth();


const props = defineProps({
    isOpen: {
        type: Boolean,
        required: true,
    },
});

const emit = defineEmits(['close']);
const authStore = useAuthStore();

const navigationLinks = computed(() => {
    return getNavigationForRole(authStore.user?.role_id);
});

const handleLinkClick = (event, link) => {
    if (props.isOpen) {
        emit('close');
    }
};
</script>

<template>
    <aside :class="['sidebar-container', { 'is-open': isOpen }]">
        <!-- Backdrop con efecto de desenfoque -->
        <div class="sidebar-backdrop" @click="$emit('close')" aria-hidden="true" />
        
        <!-- Contenido principal del sidebar -->
        <div class="sidebar-content">
            <!-- Header con logo y botón de cierre -->
            <div class="sidebar-header">
                <div class="logo-container">
                    <i-mdi-coffee class="logo-icon" />
                    <span class="logo-text">Café Buen Sabor</span>
                </div>
                <button @click="$emit('close')" class="close-button" aria-label="Cerrar menú">
                    <i-mdi-close />
                </button>
            </div>

            <!-- Navegación principal -->
            <nav class="sidebar-nav">
                <ul>
                    <li v-for="link in navigationLinks" :key="link.to.name">
                        <router-link 
                            :to="link.to" 
                            class="nav-link" 
                            active-class="is-active"
                            @click="handleLinkClick"
                        >
                            <div class="nav-icon-container">
                                <component :is="link.icon" class="nav-icon" />
                            </div>
                            <span class="nav-label">{{ link.label }}</span>
                            <span class="nav-arrow">
                                <i-mdi-chevron-right />
                            </span>
                        </router-link>
                    </li>
                </ul>
            </nav>

            <!-- Footer con botón de logout -->
            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="avatar">
                        <i-mdi-account-circle />
                    </div>
                    <div class="user-info">
                        <span class="user-name">{{ authStore.userName || 'Usuario' }}</span>
                        <span class="user-role">{{ authStore.userRole || 'Rol' }}</span>
                    </div>
                </div>
                <button @click="logout()" class="logout-button">
                    <i-mdi-logout class="logout-icon" />
                    <span>Cerrar Sesión</span>
                </button>
            </div>
        </div>
    </aside>
</template>

<style scoped>
/* --- VARIABLES Y ESTILOS BASE --- */
@reference "../../style.css";

/* --- CONTENEDOR PRINCIPAL --- */
.sidebar-container {
    @apply fixed inset-y-0 left-0 z-40 w-72 transform -translate-x-full transition-all duration-500 ease-[cubic-bezier(0.33,1,0.68,1)] lg:sticky lg:translate-x-0;
    perspective: 1500px;
}

.sidebar-container.is-open {
    @apply translate-x-0;
}

/* Backdrop con efecto de blur */
.sidebar-backdrop {
    @apply fixed inset-0 z-30 bg-primary/80 backdrop-blur-sm transition-opacity duration-500 lg:hidden;
}

.sidebar-container:not(.is-open) .sidebar-backdrop {
    @apply opacity-0 pointer-events-none;
}

.sidebar-container.is-open .sidebar-backdrop {
    @apply opacity-100;
}

/* --- CONTENIDO DEL SIDEBAR --- */
.sidebar-content {
    @apply relative z-40 flex h-full w-full flex-col bg-gradient-to-b from-primary-dark to-primary shadow-xl;
    transform-style: preserve-3d;
}

/* --- HEADER --- */
.sidebar-header {
    @apply flex items-center justify-between h-20 px-6 border-b border-primary-light/20;
}

.logo-container {
    @apply flex items-center gap-3;
}

.logo-icon {
    @apply h-8 w-8 text-accent-light;
    filter: drop-shadow(0 0 4px rgba(var(--color-accent-light-rgb), 0.4));
}

.logo-text {
    @apply text-xl font-bold tracking-wider text-white;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.close-button {
    @apply p-2 rounded-full text-text-muted hover:text-white hover:bg-primary-light/20 transition-all duration-300 lg:hidden;
}

/* --- NAVEGACIÓN --- */
.sidebar-nav {
    @apply flex-1 overflow-y-auto px-4 py-6;
}

.sidebar-nav ul {
    @apply space-y-1;
}

.nav-link {
    @apply relative flex items-center w-full px-4 py-3 my-1 rounded-lg text-text-muted transition-all duration-300;
    @apply hover:bg-white/10 hover:text-white hover:pl-6;
}

.nav-icon-container {
    @apply flex items-center justify-center h-10 w-10 mr-3 rounded-lg bg-white/5 backdrop-blur-sm;
    transition: all 0.3s ease;
}

.nav-icon {
    @apply h-5 w-5;
}

.nav-label {
    @apply text-sm font-medium tracking-wide;
    transition: all 0.3s ease;
}

.nav-arrow {
    @apply ml-auto opacity-0 text-text-muted text-lg;
    transition: all 0.3s ease;
}

.nav-link:hover .nav-arrow {
    @apply opacity-100 translate-x-1;
}

.nav-link:hover .nav-icon-container {
    @apply bg-accent/30;
}

.nav-link:hover .nav-icon {
    @apply text-accent-light;
}

/* Estado activo */
.nav-link.is-active {
    @apply bg-gradient-to-r from-accent/30 to-accent/10 text-white pl-6;
    box-shadow: inset 4px 0 0 0 var(--color-accent-light);
}

.nav-link.is-active .nav-icon-container {
    @apply bg-accent/50 shadow-md;
}

.nav-link.is-active .nav-icon {
    @apply text-white;
}

.nav-link.is-active .nav-arrow {
    @apply opacity-100 text-white;
}

.nav-link.is-active::after {
    content: '';
    @apply absolute right-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-accent-light rounded-l-full;
}

/* --- FOOTER --- */
.sidebar-footer {
    @apply p-4 mt-auto border-t border-primary-light/20;
}

.user-profile {
    @apply flex items-center gap-3 mb-4 p-3 rounded-lg bg-white/5 backdrop-blur-sm;
}

.avatar {
    @apply h-10 w-10 rounded-full bg-accent/20 flex items-center justify-center text-white text-2xl;
}

.user-info {
    @apply flex flex-col;
}

.user-name {
    @apply text-sm font-medium text-white;
}

.user-role {
    @apply text-xs text-text-muted;
}

.logout-button {
    @apply flex items-center w-full px-4 py-3 rounded-lg text-text-muted transition-all duration-300;
    @apply hover:bg-error/20 hover:text-error;
}

.logout-icon {
    @apply h-5 w-5 mr-3;
}

/* --- EFECTOS DE SCROLL --- */
.sidebar-nav::-webkit-scrollbar {
    @apply w-1;
}

.sidebar-nav::-webkit-scrollbar-track {
    @apply bg-transparent;
}

.sidebar-nav::-webkit-scrollbar-thumb {
    @apply bg-white/10 rounded-full;
}

.sidebar-nav::-webkit-scrollbar-thumb:hover {
    @apply bg-white/20;
}

/* --- ANIMACIONES --- */
.sidebar-container {
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateX(-100%) rotateY(-10deg);
    }
    to {
        opacity: 1;
        transform: translateX(0) rotateY(0);
    }
}

.nav-link {
    animation: slideIn 0.4s ease-out forwards;
    opacity: 0;
    transform: translateX(-20px);
}

@keyframes slideIn {
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Retraso para cada elemento de navegación */
.nav-link:nth-child(1) { animation-delay: 0.1s; }
.nav-link:nth-child(2) { animation-delay: 0.2s; }
.nav-link:nth-child(3) { animation-delay: 0.3s; }
.nav-link:nth-child(4) { animation-delay: 0.4s; }
.nav-link:nth-child(5) { animation-delay: 0.5s; }
</style>