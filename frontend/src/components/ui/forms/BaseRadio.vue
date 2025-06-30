<template>
  <fieldset class="form-group">
    <!-- 1. Label para todo el grupo, semánticamente correcto con <legend> -->
    <legend v-if="label" class="form-label mb-2">{{ label }}</legend>

    <!-- 2. Contenedor para las opciones, con layout dinámico -->
    <div :class="groupClasses">
      <!-- 3. Iteramos sobre las opciones para crear cada radio button -->
      <label
        v-for="option in options"
        :key="getOptionValue(option)"
        class="radio-wrapper"
      >
        <!-- Input nativo oculto -->
        <input
          type="radio"
          :name="name"
          :value="getOptionValue(option)"
          :checked="modelValue === getOptionValue(option)"
          @change="$emit('update:modelValue', getOptionValue(option))"
          class="radio-native-input"
          v-bind="$attrs"
        >
        <!-- Caja visual personalizada (círculo) -->
        <div class="radio-box">
          <span class="radio-indicator"></span>
        </div>
        <!-- Label para esta opción específica -->
        <span class="radio-label">
          {{ getOptionLabel(option) }}
        </span>
      </label>
    </div>

    <!-- 4. Texto de Error o Ayuda para el grupo -->
    <p v-if="error && error.message" class="form-error-text mt-2">{{ error.message }}</p>
    <p v-else-if="helpText" class="form-help-text mt-2">{{ helpText }}</p>
  </fieldset>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  // Para v-model
  modelValue: {
    type: [String, Number, null],
    default: null,
  },
  // Nombre del grupo, crucial para la funcionalidad de radio
  name: {
    type: String,
    required: true,
  },
  // Label para todo el grupo
  label: {
    type: String,
    default: '',
  },
  // Array de datos para las opciones
  options: {
    type: Array,
    default: () => [],
  },
  // Permite controlar la dirección del layout
  direction: {
    type: String,
    default: 'vertical', // 'vertical' o 'horizontal'
    validator: (value) => ['vertical', 'horizontal'].includes(value),
  },
  // Props para adaptabilidad de datos
  optionValue: {
    type: String,
    default: 'value',
  },
  optionLabel: {
    type: String,
    default: 'label',
  },
  // Manejo de errores para el grupo
  error: {
    type: Object,
    default: () => ({ message: null }),
  },
  helpText: {
    type: String,
    default: '',
  },
});

defineEmits(['update:modelValue']);

// Clases dinámicas para el layout del grupo
const groupClasses = computed(() => [
  'flex',
  {
    'flex-col space-y-3': props.direction === 'vertical',
    'flex-row space-x-6 flex-wrap': props.direction === 'horizontal',
  },
]);

// Funciones para leer datos de cualquier formato
const getOptionValue = (option) => option[props.optionValue];
const getOptionLabel = (option) => option[props.optionLabel];
</script>