<!-- src/components/ui/BaseInput.vue -->
<template>
  <div class="form-group">
    <label v-if="label" :for="name" class="form-label">
      {{ label }}
    </label>
    <div class="form-input-wrapper" ref="wrapperRef">
      <div v-if="$slots.prefix" class="form-input-adornment form-input-prefix">
        <slot name="prefix"></slot>
      </div>
      
      <!-- El input ahora está vinculado a VeeValidate a través de v-model="value" -->
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
    </div>
    
    <!-- El mensaje de error y el texto de ayuda ahora son más simples -->
    <p v-if="errorMessage" :id="errorId" class="form-error-text">
      {{ errorMessage }}
    </p>
    <p v-else-if="helpText" :id="helpTextId" class="form-help-text">
      {{ helpText }}
    </p>
  </div>
</template>

<script setup>
import { computed, useAttrs, watch, ref } from 'vue';
import { useField } from 'vee-validate';

defineOptions({
  inheritAttrs: false
});

const props = defineProps({
  // 'name' ahora es la prop más importante. Vincula este campo al estado del formulario.
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
  // ¡OJO! 'modelValue' y 'error' ya no son necesarios como props.
  // VeeValidate los manejará por nosotros.
});

const wrapperRef = ref(null);

// ✨ LA MAGIA DE VEE-VALIDATE ✨
// useField se conecta con el <BaseForm> padre y nos da todo lo que necesitamos.
const { value, errorMessage, handleChange } = useField(() => props.name, undefined, {
  validateOnMount: false,
  validateOnValueUpdate: false
});

const attrs = useAttrs();
const errorId = `error-${props.name}`;
const helpTextId = `help-${props.name}`;

const describedByIds = computed(() => {
  if (errorMessage.value) return errorId;
  if (props.helpText) return helpTextId;
  return null;
});

const inputClasses = computed(() => [
  'form-input',
  {
    'is-invalid': !!errorMessage.value,
    'pl-10': 'prefix' in attrs, // Un poco más robusto que $slots
    'pr-10': 'suffix' in attrs,
    'hide-number-spinners': attrs.type === 'number'
  }
]);

// La animación "shake" ahora reacciona directamente al `errorMessage` de VeeValidate. ¡Más simple!
watch(errorMessage, (newError) => {
  if (newError && wrapperRef.value) {
    wrapperRef.value.classList.remove('animate-shake');
    void wrapperRef.value.offsetWidth;
    wrapperRef.value.classList.add('animate-shake');
  }
});

</script>

<!-- Los estilos CSS no cambian -->
<style>

</style>