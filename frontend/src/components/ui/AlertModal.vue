<template>
  <div class="alert-modal" role="dialog" aria-modal="true" :aria-labelledby="titleId" :aria-describedby="messageId">
    
    <!-- Icono Protagonista (Ahora declarativo) -->
    <div :class="['icon-shroud', variantStyles.shroudClass]">
      <!-- 
        SOLUCIÓN: Usamos v-if para que unplugin-icons pueda detectar cada icono.
        Esto es más verboso en el template, pero es la forma correcta de asegurar
        que los iconos se incluyan en el build.
      -->
      <i-line-md-confirm-circle v-if="variant === 'success'" class="icon-main" />
      <i-mdi-alert-outline v-else-if="variant === 'warning'" class="icon-main" />
      <i-line-md-emoji-cry-filled v-else-if="variant === 'error'" class="icon-main" />
      <i-mdi-information-outline v-else class="icon-main" /> <!-- 'info' es el default -->
    </div>

    <!-- Contenido Textual -->
    <div class="content-wrapper">
      <h2 :id="titleId" class="title">{{ title }}</h2>
      <p :id="messageId" class="message">{{ message }}</p>
    </div>

    <!-- Botones de Acción -->
    <div class="actions-wrapper">
      <button @click="handleClose(false)" class="button-cancel button--cancel">
        {{ cancelButtonText }}
      </button>
      <button @click="handleClose(true)" :class="['button-confirm', variantStyles.buttonClass]">
        {{ confirmButtonText }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

// --- PROPS ---
const props = defineProps({
  title: { type: String, required: true },
  message: { type: String, required: true },
  variant: { type: String, default: 'info' },
  confirmButtonText: { type: String, default: 'Confirmar' },
  cancelButtonText: { type: String, default: 'Cancelar' },
});

// --- EMITS ---
const emit = defineEmits(['close']);

// --- LÓGICA DE ESTILO ---
const variantStyles = computed(() => {
  switch (props.variant) {
    case 'success':
      return {
        shroudClass: 'bg-success',
        buttonClass: 'button--accent', // Usaremos el acento para éxito
      };
    case 'warning':
      return {
        shroudClass: 'bg-warning',
        buttonClass: 'button--warning',
      };
    case 'error':
      return {
        shroudClass: 'bg-error',
        buttonClass: 'button--error',
      };
    default: // info
      return {
        shroudClass: 'bg-info',
        buttonClass: 'button--info',
      };
  }
});

// IDs únicos para accesibilidad (a11y)
const titleId = `alert-title-${Date.now()}`;
const messageId = `alert-message-${Date.now()}`;

// --- MANEJADORES ---
const handleClose = (result) => {
  emit('close', result);
};
</script>

<style scoped>

</style>