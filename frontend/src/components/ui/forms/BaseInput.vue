<!-- src/components/ui/BaseInput.vue -->
<template>
  <div class="form-group" :class="`variant-${variant}`">
    <label v-if="label" :for="name" class="form-label">
      {{ label }}
    </label>
    
    <div class="form-input-wrapper" ref="wrapperRef">
      <div v-if="$slots.prefix" class="form-input-adornment form-input-prefix">
        <slot name="prefix"></slot>
      </div>
      
      <input
        v-model="value"
        :id="name"
        :name="name"
        :class="inputClasses"
        v-bind="$attrs"
        :aria-invalid="!!errorMessage"
        :aria-describedby="describedByIds"
      />
      
      <div v-if="$slots.suffix" class="form-input-adornment form-input-suffix">
        <slot name="suffix"></slot>
      </div>
      
      <div v-if="errorMessage" class="form-input-state-decorator">
        <i-mdi-alert-circle class="w-5 h-5" />
      </div>
    </div>
    
    <p v-if="errorMessage" :id="errorId" class="form-error-text">
      {{ errorMessage }}
    </p>
    <p v-else-if="helpText" :id="helpTextId" class="form-help-text">
      {{ helpText }}
    </p>
  </div>
</template>

<script setup>
import { computed, useAttrs, watch, ref, useSlots } from 'vue';
import { useField } from 'vee-validate';

defineOptions({
  inheritAttrs: false
});

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
  variant: {
    type: String,
    default: 'light',
    validator: (value) => ['light', 'dark'].includes(value)
  }
});

const wrapperRef = ref(null);
const { value, errorMessage, handleChange } = useField(() => props.name, undefined, {
  validateOnMount: false,
  validateOnValueUpdate: false
});

const attrs = useAttrs();
const slots = useSlots();
const errorId = `error-${props.name}`;
const helpTextId = `help-${props.name}`;

const describedByIds = computed(() => {
  if (errorMessage.value) return errorId;
  if (props.helpText) return helpTextId;
  return null;
});

const inputClasses = computed(() => {
  const classes = ['form-input', `variant-${props.variant}`];
  
  if ('prefix' in slots) classes.push('has-prefix');
  if ('suffix' in slots) classes.push('has-suffix');
  if (attrs.type === 'number') classes.push('hide-number-spinners');
  
  return classes;
});

watch(errorMessage, (newError) => {
  if (newError && wrapperRef.value) {

    
    
   
    wrapperRef.value.classList.remove('animate-shake', 'is-invalid');
    void wrapperRef.value.offsetWidth;
    wrapperRef.value.classList.add('animate-shake', 'is-invalid');
 
  }else{

     wrapperRef.value.classList.remove('animate-shake', 'is-invalid');

  }
});
</script>

<style scoped>
/*
  IMPORTANTE: Esta ruta debe ser correcta desde /src/components/ui/
  hasta /src/assets/styles/.
*/
@reference "../../../style.css";




.form-group {
  @apply flex flex-col gap-2 w-full;
}

/* Variante light */
.form-group.variant-light .form-label {
  @apply text-gray-600;
}

.form-group.variant-light .form-input {
  @apply bg-surface text-text border-2 border-gray-200;
  @apply focus:border-accent focus:ring-2 focus:ring-accent/80;
  @apply placeholder-text-muted;
}

.form-group.variant-light .form-help-text {
  @apply text-text-muted;
}

/* Variante dark */
.form-group.variant-dark .form-label {
  @apply text-text-light;
}

.form-group.variant-dark .form-input {
  @apply bg-primary text-text-light border-2 border-border-dark;
  @apply focus:border-accent focus:ring-2 focus:ring-accent/80;
  @apply placeholder-text-muted;
}

.form-group.variant-dark .form-help-text {
  @apply text-text-muted;
}

/* Estilos comunes */
.form-label {
  @apply text-sm font-medium;
}

.form-input-wrapper {
  @apply relative flex items-center;
}

.form-input {
  @apply w-full py-2 px-3 rounded-lg outline-none transition-all duration-200;
}

/* Estado de error - AHORA FUNCIONARÁ */
.form-input-wrapper.is-invalid .form-input {
  @apply border-error focus:border-error focus:ring-error/30;
}

.form-group.variant-light .form-input-wrapper.is-invalid .form-input {
  @apply bg-error-light;
}

.form-group.variant-dark .form-input-wrapper.is-invalid .form-input {
  @apply bg-primary-light;
}

/* Adornos */
.form-input-adornment {
  @apply absolute flex items-center justify-center pointer-events-none;
}

.form-input-prefix {
  @apply left-3;
}

.form-input-suffix {
  @apply right-3;
}

.form-input.has-prefix {
  @apply pl-10;
}

.form-input.has-suffix {
  @apply pr-10;
}

/* Decorador de estado */
.form-input-state-decorator {
  @apply absolute right-3 flex items-center justify-center text-error;
}

/* Mensajes de texto */
.form-error-text {
  @apply text-sm text-error mt-1;
}



/* Ocultar spinners */
input[type="number"].hide-number-spinners::-webkit-outer-spin-button,
input[type="number"].hide-number-spinners::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type="number"].hide-number-spinners {
  -moz-appearance: textfield;
}
</style>