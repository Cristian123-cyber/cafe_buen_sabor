<template>
  <label class="checkbox-wrapper">
    <!-- 1. Input nativo, funcional pero invisible. Gestiona el estado. -->
    <input
      type="checkbox"
      :checked="modelValue"
      @change="$emit('update:modelValue', $event.target.checked)"
      class="checkbox-native-input"
      v-bind="$attrs"
      ref="inputRef"
    >
    
    <!-- 2. Nuestra caja visual personalizada -->
    <div class="checkbox-box">
      <!-- 3. El indicador (check o guion) -->
      <span class="checkbox-indicator">
        <i-mdi-minus v-if="indeterminate" class="h-4 w-4" />
        <i-mdi-check v-else class="h-4 w-4" />
      </span>
    </div>

    <!-- 4. El label, que acepta contenido enriquecido a través del slot -->
    <span v-if="$slots.default" class="checkbox-label">
      <slot></slot>
    </span>
  </label>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';

// Permite pasar atributos como 'disabled' al input nativo
defineOptions({
  inheritAttrs: false
});

const props = defineProps({
  // Para v-model
  modelValue: {
    type: Boolean,
    default: false,
  },
  // Para controlar el estado indeterminado
  indeterminate: {
    type: Boolean,
    default: false,
  },
});

defineEmits(['update:modelValue']);

const inputRef = ref(null);

// El estado `indeterminate` de un input debe manipularse con JS.
// Esta función sincroniza la prop con el estado real del DOM.
const syncIndeterminateState = () => {
  if (inputRef.value) {
    inputRef.value.indeterminate = props.indeterminate;
  }
};

// Sincroniza el estado al montar y cada vez que la prop cambia.
onMounted(syncIndeterminateState);
watch(() => props.indeterminate, syncIndeterminateState);
</script>