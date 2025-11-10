<script setup>

import { ref, onMounted, computed, watch, onUnmounted, onActivated } from 'vue';
import { storeToRefs } from 'pinia';


import { useTablesStore } from '../../stores/tablesStore';
import { useRoute } from 'vue-router';

import { useRouter } from 'vue-router';

const router = useRouter();
const route = useRoute();

const tableStore = useTablesStore();


const searchTerm = ref('');

const selectedState = ref(0);

watch([searchTerm, selectedState], ([newSearch, newState]) => {
  tableStore.setFilters({ term: newSearch, state: newState });
});


const stateOptions = computed(() => {

  return [{ value: 0, label: 'Todos' }, { value: 'OCCUPIED', label: 'Ocupada' }, { value: 'FREE', label: 'Libre' }, { value: 'INACTIVE', label: 'Inactiva' }];
});


const isLoading = computed(() => tableStore.isLoading);


const { tables } = storeToRefs(tableStore);







const handleViewOrders = (table) => {
  router.push({ name: 'CashierTableOrders', params: { id: table.id_table }, query: { table_number: table.table_number } });

}
















onMounted(() => {
  tableStore.startPolling();
  searchTerm.value = tableStore.filterTerm;
  selectedState.value = tableStore.filterState;
});



onUnmounted(() => {
  tableStore.stopPolling();
});

</script>

<template>
  <!-- 
    ¡Aquí está la magia de la composición!
    Envolvemos nuestro contenido (TestView) con nuestro layout (AppLayout).
  -->
  <AppLayout>


    <div class="dashboard-container">

      <template v-if="route.name === 'CashierTables'">




        <HeaderSection>

        </HeaderSection>

        <ToolsBar v-model:searchTerm="searchTerm" placeholderSearch="Numero de mesa..."
          v-model:selectedState="selectedState" :showCreate="false" :state-options="stateOptions" :loading="isLoading"
          searchLabel="Buscar mesas">


        </ToolsBar>

        <TableList :tables="tables" :loading="isLoading">
          <template #actions="{ table }">




            <BaseButton v-if="table.table_status === 'OCCUPIED'" size="icon" @click="handleViewOrders(table)"
              variant="secondary">
              <i-lets-icons-view-fill class="w-7 h-7" />
            </BaseButton>


          </template>


        </TableList>




        <FooterDash>

        </FooterDash>




      </template>


      <!-- 👇 Si estoy en la subruta, se muestra TableOrders -->
      <router-view v-else />


    </div>

  </AppLayout>
</template>


<style scoped>
/* IMPORTANTE: Referencia al archivo de tema central para que @apply funcione. */
@reference "../../style.css";

/* Contenedor principal de todo el dashboard */
.dashboard-container {
  @apply flex flex-col gap-6 lg:gap-8;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>