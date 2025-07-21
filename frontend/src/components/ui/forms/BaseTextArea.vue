<template>
  <div class="form-group" :class="`variant-${variant}`">
    <!-- 1. Label (Etiqueta) -->
    <label v-if="label" :for="name" class="form-label">
      {{ label }}
    </label>

    <!-- 2. Contenedor, que recibirá la animación de sacudida -->
    <div class="form-input-wrapper" ref="wrapperRef">

      <!-- 3. El elemento <textarea> real -->
      <textarea :id="name" :name="name" v-model="value" :class="textareaClasses" v-bind="$attrs"
        :aria-invalid="!!errorMessage" :aria-describedby="describedByIds" ref="textareaRef"></textarea>

        <div v-if="errorMessage" class="form-input-state-decorator">
        <i-mdi-alert-circle class="w-5 h-5" />
      </div>

    </div>

    <!-- 4. Fila de "Footer" para ayuda, errores y el contador -->
    <div class="form-footer">
      <!-- Mensaje de Error o Ayuda -->
      <p v-if="errorMessage" :id="errorId" class="form-error-text">
        {{ errorMessage }}
      </p>
      <p v-else-if="helpText" :id="helpTextId" class="form-help-text">
        {{ helpText }}
      </p>
      <!-- Espaciador para empujar el contador a la derecha si no hay texto de ayuda -->
      <span v-else class="flex-grow"></span>

      <!-- Contador de Caracteres -->
      <div v-if="showCounter && maxLength" :class="counterClasses">
        {{ value.length }} / {{ maxLength }}
      </div>
    </div>
  </div>
</template>

<script setup>

import { useField } from 'vee-validate';
import { computed, watch, ref, onMounted } from 'vue';

// Desactivar herencia de atributos
defineOptions({
  inheritAttrs: false
});

// Referencias a los elementos del DOM
const textareaRef = ref(null);
const wrapperRef = ref(null); // Ref para el contenedor que se sacudirá

// --- PROPS ---
// Definidas para ser consistentes con tu BaseInput
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
  // --- PROPS ESPECÍFICAS DE TEXTAREA ---
  autoResize: {
    type: Boolean,
    default: false,
  },
  showCounter: {
    type: Boolean,
    default: false,
  },
  maxLength: {
    type: Number,
    default: null,
  },
  variant: {
    type: String,
    default: 'light',
    validator: (value) => ['light', 'dark'].includes(value)
  }
});



const { value, errorMessage, handleChange } = useField(() => props.name, undefined, {
  validateOnMount: false,
  validateOnValueUpdate: false
})


// --- LÓGICA DE IDS PARA ACCESIBILIDAD ---
const errorId = `error-${props.id}`;
const helpTextId = `help-${props.id}`;

const describedByIds = computed(() => {
  if (errorMessage.value) return errorId;
  if (props.helpText) return helpTextId;
  return null;
});

// --- CLASES COMPUTADAS ---
const textareaClasses = computed(() => [
  'form-input', `variant-${props.variant}`, // Reutilizamos la clase base de los inputs
  {
    'resize-none': props.autoResize, // Deshabilita el resize manual si el auto-resize está activo
  }
]);

const counterClasses = computed(() => [
  'form-counter',
  {
    'is-exceeded': props.maxLength && value.value.length > props.maxLength
  }
]);



// --- LÓGICA DE CARACTERÍSTICAS PREMIUM ---

const adjustHeight = () => {
  if (props.autoResize && textareaRef.value) {
    const el = textareaRef.value;
    el.style.height = 'auto';
    el.style.height = `${el.scrollHeight}px`;
  }
};

watch(value, () => {
  adjustHeight();
});

onMounted(() => {
  adjustHeight();
});



// 2. Animación de Sacudida por Error
// Lógica idéntica a tu BaseInput, aplicada al wrapper
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




</style>