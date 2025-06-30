<!-- src/components/ui/BaseFormRow.vue -->
<template>
  <div class="form-row" :style="gridStyle">
    <slot></slot>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  cols: {
    type: [Number, String],
    default: 1,
  },
});

const gridStyle = computed(() => ({
  '--grid-cols': props.cols,
}));
</script>

<style scoped>

</style>