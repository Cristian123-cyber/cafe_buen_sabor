<template>
  <header class="app-header">
    <!-- Gradiente sutil de fondo -->
    <div class="header-backdrop"></div>
    
    <!-- Efecto de brillo superior -->
    <div class="header-shine"></div>
    
    <div class="header-content">
      <!-- 
        Lado Izquierdo: Botón de menú y Logo.
        En pantallas grandes, el botón de menú se oculta.
      -->
      <div class="header-left">
        <button
          class="menu-button"
          aria-label="Alternar menú lateral"
          @click="emit('toggle-sidebar')"
        >
          <div class="menu-button-bg"></div>
          <i-mdi-menu class="w-6 h-6 relative z-10" />
        </button>
        
        <div class="logo-container">
          <div class="logo-icon-wrapper">
            <i-mdi-coffee-outline class="logo-icon" />
            <div class="logo-icon-glow"></div>
          </div>
          <div class="logo-text-container">
            <span class="logo-text">Café Buen Sabor</span>
            <div class="logo-underline"></div>
          </div>
        </div>
      </div>

      <!-- 
        Centro (Visible en Desktop): Título de la vista actual.
        Proporciona contexto al usuario sobre dónde se encuentra.
        Para obtener el título, usamos el meta de la ruta.
      -->
      <div class="header-center">
        <div class="view-title-container">
          <h1 class="view-title">{{ currentViewTitle }}</h1>
          <div class="view-title-accent"></div>
        </div>
      </div>

      <!-- 
        Lado Derecho: Controles y Acciones.
        Actualmente, solo el centro de notificaciones.
      -->
      <div class="header-right">
        <div class="notification-wrapper">
          <NotificationCenter />
        </div>
      </div>
    </div>
    
    <!-- Línea de acento inferior -->
    <div class="header-accent-line"></div>
  </header>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';

// Definimos el emit para comunicarnos con AppLayout.
const emit = defineEmits(['toggle-sidebar']);

// Usamos useRoute para acceder a la información de la ruta actual.
const route = useRoute();

/**
 * Obtiene dinámicamente el título de la vista actual desde los metadatos de la ruta.
 * Esto hace que el header sea contextual y reutilizable.
 * En tus rutas, deberás añadir: meta: { title: 'Nombre de la Vista' }
 * @returns {string} El título de la vista o un string por defecto.
 */
const currentViewTitle = computed(() => route.meta.title || 'Dashboard');
</script>

<style scoped>
@reference "../../style.css";

.app-header {
  @apply sticky top-0 z-30 relative;
  @apply h-16 bg-primary-light text-text-light border-b border-border-light;
  /* Sombra más dramática y moderna */
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15), 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Gradiente sutil de fondo para profundidad */
.header-backdrop {
  @apply absolute inset-0 z-0;
  background: linear-gradient(135deg, 
    rgba(66, 66, 66, 0.95) 0%, 
    rgba(33, 33, 33, 1) 50%, 
    rgba(13, 13, 13, 0.98) 100%
  );
}

/* Efecto de brillo superior para modernidad */
.header-shine {
  @apply absolute top-0 left-0 right-0 h-px z-10;
  background: linear-gradient(90deg, 
    transparent 0%, 
    rgba(203, 161, 53, 0.3) 20%, 
    rgba(203, 161, 53, 0.6) 50%, 
    rgba(203, 161, 53, 0.3) 80%, 
    transparent 100%
  );
}

.header-content {
  @apply h-full w-full max-w-screen-2xl mx-auto px-4 relative z-20;
  @apply flex items-center justify-between gap-4;
}

/* --- LADO IZQUIERDO --- */
.header-left {
  @apply flex items-center gap-3;
}

.menu-button {
  @apply p-2 rounded-xl text-text-muted relative overflow-hidden;
  @apply transition-all duration-300 ease-out;
  @apply hover:text-accent-light hover:scale-105;
  @apply lg:hidden;
  @apply border border-transparent hover:border-accent/20;
}

.menu-button-bg {
  @apply absolute inset-0 bg-accent/10 opacity-0 rounded-xl;
  @apply transition-all duration-300 ease-out;
  transform: scale(0.8);
}

.menu-button:hover .menu-button-bg {
  @apply opacity-100;
  transform: scale(1);
}

.logo-container {
  @apply flex items-center gap-3;
}

.logo-icon-wrapper {
  @apply relative;
}

.logo-icon {
  @apply w-8 h-8 text-accent relative z-10;
  @apply transition-all duration-300 ease-out;
  filter: drop-shadow(0 0 8px rgba(203, 161, 53, 0.3));
}

.logo-icon-glow {
  @apply absolute inset-0 w-8 h-8 bg-accent/20 rounded-full blur-sm;
  @apply transition-all duration-300 ease-out;
  transform: scale(0.8);
}

.logo-container:hover .logo-icon {
  transform: rotate(5deg) scale(1.05);
  filter: drop-shadow(0 0 12px rgba(203, 161, 53, 0.5));
}

.logo-container:hover .logo-icon-glow {
  transform: scale(1.2);
  @apply bg-accent/30;
}

.logo-text-container {
  @apply relative hidden sm:block;
}

.logo-text {
  @apply text-lg font-bold tracking-tight text-text-light relative z-10;
  @apply transition-all duration-300 ease-out;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.logo-underline {
  @apply absolute bottom-0 left-0 h-0.5 bg-accent;
  @apply transition-all duration-300 ease-out;
  width: 0%;
}

.logo-container:hover .logo-underline {
  width: 100%;
  @apply bg-accent-light;
  box-shadow: 0 0 8px rgba(203, 161, 53, 0.4);
}

/* --- CENTRO (Título de la Vista) --- */
.header-center {
  @apply hidden lg:flex flex-1 justify-center;
}

.view-title-container {
  @apply relative;
}

.view-title {
  @apply text-xl font-semibold text-text-light tracking-wider relative z-10;
  @apply transition-all duration-300 ease-out;
  text-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
  background: linear-gradient(135deg, 
    rgba(237, 237, 237, 1) 0%, 
    rgba(203, 161, 53, 0.8) 50%, 
    rgba(237, 237, 237, 1) 100%
  );
  background-clip: text;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-size: 200% 200%;
  animation: shimmer 3s ease-in-out infinite;
}

.view-title-accent {
  @apply absolute -bottom-1 left-1/2 w-12 h-0.5 bg-accent/60;
  @apply transition-all duration-300 ease-out;
  transform: translateX(-50%) scaleX(0);
}

.view-title-container:hover .view-title-accent {
  transform: translateX(-50%) scaleX(1);
  @apply bg-accent-light w-16;
  box-shadow: 0 0 6px rgba(203, 161, 53, 0.5);
}

@keyframes shimmer {
  0%, 100% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
}

/* --- LADO DERECHO --- */
.header-right {
  @apply flex items-center;
}

.notification-wrapper {
  @apply transition-all duration-300 ease-out;
}

/* Línea de acento inferior */
.header-accent-line {
  @apply absolute bottom-0 left-0 right-0 h-px z-10;
  background: linear-gradient(90deg,
    transparent 0%,
    rgba(203, 161, 53, 0.2) 10%,
    rgba(203, 161, 53, 0.6) 50%,
    rgba(203, 161, 53, 0.2) 90%,
    transparent 100%
  );
}

/* Mejoras de responsividad */
@media (max-width: 640px) {
  .logo-icon {
    @apply w-7 h-7;
  }
  
  .header-content {
    @apply px-3 gap-2;
  }
}
</style>