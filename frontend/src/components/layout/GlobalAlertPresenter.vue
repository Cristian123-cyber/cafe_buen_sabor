<template>
  <Teleport to="body">
    <!-- 
      AHORA: Este Transition envuelve TODO, pero solo controla la presencia
      de los elementos en el DOM con un v-if.
    -->
    <Transition name="presenter-fade">
      <div v-if="activeAlert" class="presenter-container">
        
        <!-- 
          SOLUCIÓN 1: El overlay tiene su PROPIA transición de opacidad.
          Esto hace que el color de fondo y el backdrop-blur se desvanezcan juntos.
        -->
        <Transition appear name="overlay-fade">
          <div class="overlay" @click="handleOverlayClick" />
        </Transition>

        <!-- 
          SOLUCIÓN 2: El modal mantiene su transición de escala y opacidad,
          pero ahora es un hermano del overlay, no un hijo.
        -->
        <Transition appear name="modal-bounce">
          <AlertModal
            v-if="activeAlert"
            :key="activeAlert.title"
            :title="activeAlert.title"
            :message="activeAlert.message"
            :variant="activeAlert.variant"
            :confirm-button-text="activeAlert.confirmButtonText"
            :cancel-button-text="activeAlert.cancelButtonText"
            @close="closeAlert"
          />
        </Transition>

      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { useAlert } from '../../composables/useAlert.js';

const { activeAlert, _close: closeAlert } = useAlert();

// Cerrar la alerta si el usuario hace clic en el overlay (equivale a cancelar)
const handleOverlayClick = () => {
  closeAlert(false);
};
</script>

<style scoped>

</style>