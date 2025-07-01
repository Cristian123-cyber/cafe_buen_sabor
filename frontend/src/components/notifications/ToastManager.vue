<script setup>
import { useToasts } from '../../composables/useToast';
import ToastNotification from './ToastNotification.vue';

const { toasts, removeToast } = useToasts();
</script>

<template>
  <div class="toast-manager">
    <TransitionGroup name="toast-transition" tag="div" class="toast-container">
      <ToastNotification
        v-for="toast in toasts"
        :key="toast.id"
        :id="toast.id"
        :message="toast.message"
        :title="toast.title"
        :type="toast.type"
        :duration="toast.duration"
        @close="removeToast"
      />
    </TransitionGroup>
  </div>
</template>

<style scoped>
/* No necesita referencia porque usa clases de utilidad globales */
@reference "../../style.css";

.toast-manager {
  /* Posicionamiento fijo en la esquina superior derecha */
  @apply fixed top-4 right-4 z-[9999] w-full max-w-sm;
  /* Necesario para que el contexto de apilamiento funcione */
  @apply pointer-events-none;
}

.toast-container {
    /* El contenedor interno sí debe aceptar clics */
    @apply pointer-events-auto flex flex-col items-end gap-3;
}

/* --- Animaciones de Entrada/Salida --- */
/* 1. Estado inicial de entrada y estado final de salida */
.toast-transition-enter-from,
.toast-transition-leave-to {
  opacity: 0;
  transform: translateX(100%);
}

/* 2. Estado activo de la transición */
.toast-transition-enter-active,
.toast-transition-leave-active {
  transition: all 0.4s cubic-bezier(0.21, 1.02, 0.73, 1); /* Una curva de easing elástica */
}

/* 3. Asegurar que los elementos que se mueven no afecten el layout */
.toast-transition-leave-active {
  position: absolute;
}
</style>