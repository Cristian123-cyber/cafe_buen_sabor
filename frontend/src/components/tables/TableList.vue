<template>
  <LoadingState v-if="loading" />

  <!-- Estado vacío -->
    <div v-else-if="!tables.length" class="empty-state">
      <EmptyState />
    </div>

  <div v-else class="table-list-grid">


    <TableCard v-for="table in tables" :key="table.id_table" :table="table">
      <!-- Aquí es donde ocurre la magia: inyectamos las acciones según el rol -->
      <template #actions>

        <slot name="actions" :table="table"></slot>



      </template>
    </TableCard>
  </div>
</template>

<script setup>
import { computed } from 'vue';


// --- Props ---
const props = defineProps({
  tables: {
    type: Array,
    required: true,
    default: () => [],
  },

  loading: {
    type: Boolean,
    default: false,
  },
});

</script>

<style scoped>
@reference "../../style.css";

.table-list-grid {
  @apply grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4;
}
</style>