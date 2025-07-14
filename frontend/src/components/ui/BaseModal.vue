<script setup>
import { ref, watch, nextTick, computed } from 'vue';

// --- PROPS Y EMITS ---

// Se utiliza defineModel para una sintaxis más limpia y directa con v-model en Vue 3.4+
// Si usas una versión anterior, puedes usar props y emits por separado.
const modelValue = defineModel({
  type: Boolean,
  default: false,
});

const props = defineProps({
  /**
   * Título que se muestra en la cabecera del modal.
   * También se usa para la accesibilidad (aria-labelledby).
   */
  title: {
    type: String,
    required: true,
  },
  /**
   * Permite cerrar el modal al hacer clic en el overlay.
   */
  closeable: {
    type: Boolean,
    default: true,
  },
  /**
   * Define el ancho máximo del modal.
   * Opciones: 'sm', 'md', 'lg', 'xl', '2xl'.
   */
  maxWidth: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg', 'xl', '2xl'].includes(value),
  }
});

const emit = defineEmits(['close']);

// --- REFERENCIAS DOM ---
const modalRef = ref(null); // Referencia al contenedor principal del modal

// --- LÓGICA DE CIERRE ---

/**
 * Cierra el modal y emite los eventos correspondientes.
 */
const close = () => {
  if (props.closeable) {
    modelValue.value = false;
    emit('close');
  }
};

/**
 * Maneja el evento de presionar una tecla, específicamente para 'Escape'.
 * @param {KeyboardEvent} event
 */
const handleKeydown = (event) => {
  if (event.key === 'Escape') {
    close();
  }
};

// --- TRAMPA DE FOCO (FOCUS TRAP) ---

/**
 * Lógica para mantener el foco dentro del modal.
 * Se activa cuando el modal se abre.
 */



// --- WATCHERS Y CICLO DE VIDA ---

/**
 * Observa el estado del modal para añadir/quitar listeners globales
 * y gestionar el foco.
 */
watch(modelValue, (isOpen) => {
  if (isOpen) {
    // Añadir listener para la tecla Escape
    window.addEventListener('keydown', handleKeydown);
    
  } else {
    // Limpiar listener al cerrar
    window.removeEventListener('keydown', handleKeydown);
  }
}, { immediate: true });

// --- CLASES COMPUTADAS ---

const maxWidthClass = computed(() => {
  return {
    sm: 'max-w-sm',
    md: 'max-w-md',
    lg: 'max-w-lg',
    xl: 'max-w-xl',
    '2xl': 'max-w-2xl',
  }[props.maxWidth];
});

</script>

<template>
  <Teleport to="body">
    <Transition name="modal-fade">
      <div
        v-if="modelValue"
        class="modal-wrapper"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
      >
        <div class="modal-overlay" @click="close"></div>

        <div
          ref="modalRef"
          :class="['modal-container', maxWidthClass]"
        >
          <header class="modal-header">
            <div class="modal-header-content">
              <h2 id="modal-title" class="modal-title">
                <slot name="header">
                  {{ title }}
                </slot>
              </h2>
              <button @click="close" class="modal-close-button" aria-label="Cerrar modal">
                <i-mdi-close class="w-5 h-5" />
              </button>
            </div>
          </header>

          <main class="modal-body">
            <slot />
          </main>

          <footer v-if="$slots.footer" class="modal-footer">
            <slot name="footer" />
          </footer>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
/*
 * Importa las variables y utilidades de Tailwind definidas en el tema global.
 * La ruta debe ser relativa desde este archivo hasta tu CSS principal.
 */
@reference '../../style.css';

/*
 * Clases semánticas para mantener el HTML limpio.
 * Se usan las utilidades de Tailwind, que a su vez usan las variables CSS
 * de nuestro archivo de tema.
 */

.modal-wrapper {
  @apply fixed inset-0 z-50 flex items-center justify-center p-4;
}

.modal-overlay {
  @apply fixed inset-0 bg-primary/60;
}

.modal-container {
  @apply relative z-10 flex w-full flex-col overflow-hidden rounded-xl bg-surface shadow-2xl ring-1 ring-border-light;
  max-height: 90vh;
}

.modal-header {
  @apply bg-gradient-to-r from-surface to-surface-dark;
}

.modal-header-content {
  @apply flex items-center justify-between px-6 py-4;
}

.modal-title {
  @apply text-xl font-semibold text-text tracking-tight;
}

.modal-close-button {
  @apply rounded-lg p-2 text-text-muted transition-all duration-200 hover:bg-accent-light hover:text-text active:scale-95;
}

.modal-body {
  @apply overflow-y-auto px-8 py-6;
}

.modal-footer {
  @apply flex flex-shrink-0 items-center justify-end gap-3 bg-surface-dark px-6 py-4;
}

/*
 * Transiciones de entrada y salida para el modal.
 * Crea un efecto de fade y un ligero zoom más suave.
 */
.modal-fade-enter-active,
.modal-fade-leave-active {
  @apply transition-all duration-300 ease-out;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  @apply opacity-0;
}

.modal-fade-enter-active .modal-container,
.modal-fade-leave-active .modal-container {
  @apply transition-all duration-300 ease-out;
}

.modal-fade-enter-from .modal-container,
.modal-fade-leave-to .modal-container {
  @apply -translate-y-8 scale-95 opacity-0;
}
</style>