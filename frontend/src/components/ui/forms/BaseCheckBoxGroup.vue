<template>
  <div class="form-group">
    <fieldset>
      <legend v-if="label" class="form-label mb-3">{{ label }}</legend>

      <div class="checkbox-grid" :aria-invalid="!!errorMessage" :aria-describedby="describedByIds">
        <div v-for="option in options" :key="option[optionValue]" class="checkbox-item">
          <input 
            :id="`${name}-${option[optionValue]}`" 
            :name="name" 
            type="checkbox" 
            :value="option[optionValue]"
            v-model="value" 
            class="form-checkbox" 
          />
          <label :for="`${name}-${option[optionValue]}`" class="form-checkbox-label">
            <i-fluent-food-grains-20-filled class="icon-ing"></i-fluent-food-grains-20-filled>
            
            <span class="checkbox-text">{{ option[optionLabel] }}</span>
          </label>
        </div>
      </div>
    </fieldset>

    <div class="form-message-container">
      <p v-if="errorMessage" class="form-error-text">
        <svg class="message-icon" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        {{ errorMessage }}
      </p>
      <p v-else-if="helpText" :id="helpTextId" class="form-help-text">
        <svg class="message-icon" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
        </svg>
        {{ helpText }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useField } from 'vee-validate';

const props = defineProps({
  name: {
    type: String,
    required: true,
  },
  label: {
    type: String,
    default: '',
  },
  helpText: {
    type: String,
    default: '',
  },
  options: {
    type: Array,
    required: true,
  },
  optionLabel: {
    type: String,
    default: 'label',
  },
  optionValue: {
    type: String,
    default: 'value',
  },
});

// Usamos useField, especificando que es de tipo 'checkbox'.
// Esto hace que vee-validate maneje `value` como un array automáticamente.
const { value, errorMessage } = useField(() => props.name, undefined, {
  type: 'checkbox',
  validateOnMount: false,
});

const errorId = `error-${props.name}`;
const helpTextId = `help-${props.name}`;

const describedByIds = computed(() => {
  if (errorMessage.value) return errorId;
  if (props.helpText) return helpTextId;
  return null;
});
</script>

<style scoped>
@reference "../../../style.css";

.form-group {
  @apply space-y-3;
}

.form-label {
  @apply text-sm font-medium text-gray-700;
}

.checkbox-grid {
  @apply grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3;
}

.checkbox-item {
  @apply relative;
}

/* Ocultar completamente el checkbox nativo */
.form-checkbox {
  @apply hidden;
}

/* Estilo del label que actúa como contenedor clickeable */
.form-checkbox-label {
  @apply flex items-center gap-3 p-3 rounded-lg border-2 border-gray-200 bg-white cursor-pointer;
  @apply transition-all duration-200 ease-in-out;
  @apply hover:border-blue-300 hover:bg-blue-50 hover:shadow-sm;
}

/* Indicador visual personalizado del checkbox */
.checkbox-indicator {
  @apply flex items-center justify-center w-5 h-5 border-2 border-gray-300 rounded;
  @apply bg-white transition-all duration-200 ease-in-out;
  @apply shadow-sm;
}

/* Icono del check */
.checkbox-icon {
  @apply w-3 h-3 text-white opacity-0 transform scale-75;
  @apply transition-all duration-200 ease-in-out;
}

/* Texto del checkbox */
.checkbox-text {
  @apply text-sm font-medium text-gray-700 select-none;
  @apply transition-colors duration-200;
}

/* Estado checked */
.form-checkbox:checked + .form-checkbox-label .checkbox-indicator {
  @apply bg-blue-600 border-blue-600 shadow-md;
}

.form-checkbox:checked + .form-checkbox-label .checkbox-icon {
  @apply opacity-100 scale-100;
}

.form-checkbox:checked + .form-checkbox-label {
  @apply border-blue-500 bg-blue-50;
}

.form-checkbox:checked + .form-checkbox-label .checkbox-text .icon-ing {
  @apply text-blue-900;
}

/* Estado disabled */
.form-checkbox:disabled + .form-checkbox-label {
  @apply opacity-60 cursor-not-allowed bg-gray-50;
}

.form-checkbox:disabled + .form-checkbox-label:hover {
  @apply border-gray-200 bg-gray-50 shadow-none;
}

/* Estados de error */
.checkbox-grid[aria-invalid="true"] .form-checkbox-label {
  @apply border-red-300 hover:border-red-400;
}

.checkbox-grid[aria-invalid="true"] .form-checkbox:checked + .form-checkbox-label {
  @apply border-red-500 bg-red-50;
}

.checkbox-grid[aria-invalid="true"] .form-checkbox:checked + .form-checkbox-label .checkbox-indicator {
  @apply bg-red-600 border-red-600;
}

.checkbox-grid[aria-invalid="true"] .form-checkbox:checked + .form-checkbox-label .checkbox-text {
  @apply text-red-900;
}

/* Contenedor de mensajes */
.form-message-container {
  @apply mt-1;
}

/* Estilos para mensajes de error y ayuda */
.form-error-text {
  @apply text-xs text-red-600 mt-1;
}

.form-help-text {
  @apply text-xs text-gray-500 mt-1;
}

.message-icon {
  @apply hidden;
}

/* Mejoras para dispositivos táctiles */
@media (hover: none) and (pointer: coarse) {
  .form-checkbox-label {
    @apply p-4;
  }
  
  .checkbox-indicator {
    @apply w-6 h-6;
  }
  
  .checkbox-icon {
    @apply w-4 h-4;
  }
}

/* Animaciones mejoradas */
@keyframes checkScale {
  0% { transform: scale(0.8); }
  50% { transform: scale(1.1); }
  100% { transform: scale(1); }
}

.form-checkbox:checked + .form-checkbox-label .checkbox-indicator {
  animation: checkScale 0.2s ease-in-out;
}
</style>