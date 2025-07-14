<template>
  <div class="unauthorized-container">
    <div class="unauthorized-card">
      <div class="icon-wrapper">
        <i-ic-baseline-do-not-disturb class="unauthorized-icon" />
      </div>

      <h1 class="unauthorized-title">Acceso Denegado</h1>
      <p class="unauthorized-message">
        No tienes los permisos necesarios para acceder a esta página.
        <span v-if="authStore.isAuthenticated">
          Contacta a un administrador si crees que esto es un error.
        </span>
        <span v-else>
          Por favor, inicia sesión con una cuenta autorizada.
        </span>
      </p>

      <div class="unauthorized-actions">
        <BaseButton
        variant="primary"
        @click="goBack">

        <template #icon-left>
            <i-nrk-back></i-nrk-back>
        </template>

        Volver atras
        
        </BaseButton>
        
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/authS.js'; // Ajusta la ruta si es necesario

// --- COMPOSABLES ---
const router = useRouter();
const authStore = useAuthStore();

// --- MÉTODOS ---

/**
 * Navega a la página anterior en el historial.
 */
const goBack = () => {
  router.back();
};

</script>

<style scoped>
/*
 * (IMPORTANTE) Se referencia el archivo de estilos principal para poder usar @apply
 * y las variables CSS (--color-primary, etc.) definidas en el @theme.
 * La ruta es relativa desde este componente.
 */
@reference '../../style.css';

.unauthorized-container {
  @apply flex items-center justify-center min-h-screen w-full bg-surface-dark p-4;
}

.unauthorized-card {
  @apply bg-surface rounded-xl shadow-lg w-full max-w-md text-center p-8 transition-all;
}

.icon-wrapper {
  @apply mx-auto bg-error-light w-20 h-20 rounded-full flex items-center justify-center mb-6;
}

.unauthorized-icon {
  @apply text-error text-5xl;
}

.unauthorized-title {
  @apply text-3xl font-bold text-text mb-2;
}

.unauthorized-message {
  @apply text-base text-text-muted mb-8;
}

.unauthorized-actions {
  @apply flex flex-col sm:flex-row justify-center gap-4;
}

</style>