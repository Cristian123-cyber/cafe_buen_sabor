<template>
  <div class="py-3">
    <div ref="containerRef" class="flex gap-3 overflow-x-auto scrollbar-hide px-2">
      <button
        v-for="cat in categories"
        :key="cat.id_category ?? 'all'"
        :ref="el => categoryRefs[cat.id_category ?? 'all'] = el"
        @click="selectCategory(cat.id_category)"
        class="flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-lg px-4 text-sm font-medium leading-normal whitespace-nowrap transition-colors duration-200"
        :class="{
          'bg-accent text-primary': selected === cat.id_category,
          'bg-white text-[#111418] hover:bg-gray-200': selected !== cat.id_category
        }"
      >
        {{ cat.category_name }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, defineEmits, defineProps, nextTick } from 'vue';

const props = defineProps({
  categories: {
    type: Array,
    required: true
  },
  modelValue: {
    type: [String, Number, null],
    default: null
  }
});

const emit = defineEmits(['update:modelValue', 'change']);

const selected = ref(props.modelValue);
const categoryRefs = ref({});
const containerRef = ref(null);

// Actualiza la categoría seleccionada y centra el botón
function selectCategory(id) {
  selected.value = id;
  emit('update:modelValue', id);
  emit('change', id);

  nextTick(() => {
    const el = categoryRefs.value[id ?? 'all'];
    if (el && typeof el.scrollIntoView === 'function') {
      el.scrollIntoView({ behavior: 'smooth', inline: 'start', block: 'nearest' });
    }
  });
}

// Mantiene sincronía si cambia desde el padre
watch(() => props.modelValue, (newVal) => {
  selected.value = newVal;
});
</script>
