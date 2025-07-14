<template>
  <div class="search-container">
    <i-ph-magnifying-glass-bold class="search-icon" />

    <input
      :value="modelValue"
      @input="handleInput"
      :placeholder="placeholder"
      type="search"
      class="search-input"
    />

    <button
      v-if="modelValue"
      @click="clearInput"
      class="clear-btn"
      aria-label="Limpiar búsqueda"
    >
      <i-ph-x-bold class="h-4 w-4" />
    </button>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  modelValue: {
    type: String,
    required: true,
  },
  placeholder: {
    type: String,
    default: 'Buscar...',
  },
  debounce: {
    type: Number,
    default: 300,
  },
});

const emit = defineEmits(['update:modelValue']);
const debounceTimeout = ref(null);

const handleInput = (event) => {
  clearTimeout(debounceTimeout.value);
  const inputValue = event.target.value;
  debounceTimeout.value = setTimeout(() => {
    emit('update:modelValue', inputValue);
  }, props.debounce);
};

const clearInput = () => {
  clearTimeout(debounceTimeout.value);
  emit('update:modelValue', '');
};
</script>

<style scoped>
@reference "../../style.css";

.search-container {
  @apply relative w-full flex items-center border-b border-border-light bg-gray-200 rounded-md px-2 py-2;
  @apply transition-colors duration-200;
}

.search-icon {
  @apply text-text-muted mr-2 w-5 h-5;
}

.search-input {
  @apply flex-1 bg-transparent text-sm font-medium text-text placeholder:text-text-muted outline-none;
  /* Remove default search icon */
}
.search-input::-webkit-search-cancel-button {
  -webkit-appearance: none;
}

.clear-btn {
  @apply ml-2 text-text-muted hover:text-accent transition-colors duration-200;
}
</style>
