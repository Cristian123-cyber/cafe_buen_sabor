<script setup>

import { ref, onMounted, computed, watch, onUnmounted } from 'vue';
import { storeToRefs } from 'pinia';

import { useAlert } from '../../composables/useAlert';
import { useToasts } from '../../composables/useToast';
import { useTablesStore } from '../../stores/tablesStore';
import QrWrapper from '../../components/cafe/QrWrapper.vue';





const tableStore = useTablesStore();

const alert = useAlert();
const { addToast } = useToasts();
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



// Handlers actions
const handleCreate = () => {
  showCreateModal.value = true;
  
}

const handleEdit = (table) => {
 
  
  currentTable.value = table;
  showEditModal.value = true;
}

const handleDelete = async (table) => {
  
   const isConfirmed = await alert.show({
    variant: 'warning',
    title: '¿Desea eliminar esta mesa?',
    message: 'Este mesa sera eliminada del sistema. ¿Deseas continuar?',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
  });
  

  if (isConfirmed) {

    try {
      await tableStore.deleteTable(table.id_table);
      addToast({
            message: `La mesa ${table.table_number} ha sido eliminada exitosamente`,
            title: 'Mesa eliminada',
            type: 'info',
            duration: 2000
        });
    } catch (error) {
      alert.show({
        variant: 'error',
        title: 'Error al eliminar la mesa',
        message: error?.message ? error?.message : `No se pudo eliminar la mesa ${table.table_number}. Inténtalo de nuevo más tarde.`,
      });

    }
  }


}

const handleQrView = (table) => {

  showQrModal.value = true;
  currentTable.value = table;
}

const handleAddLogin = (table) => {

  showAddLoginModal.value = true;
  currentTable.value = table;
  // Aquí abrirías el modal o vista para agregar un login a la mesa
}


//MODAL CONTROL
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showQrModal = ref(false);
const showAddLoginModal = ref(false);
const currentTable = ref(null);


// form references
const formCreateRef = ref();
const formEditRef = ref();
const formAddLoginRef = ref();




const triggerSubmit = async (form) => {

  switch (form) {
    case 'create':
      formCreateRef.value?.submit();
      break;
    case 'edit':
      formEditRef.value?.submit();
      break;
    case 'addLogin':
      formAddLoginRef.value?.submit();
      break;
    default:
      console.warn('No se ha definido un formulario para enviar');
      break;
  };

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
      <HeaderSection title="Gestión de mesas"
        descriptionMessage="Administra la información y estado de las mesas del local.">

      </HeaderSection>


      <ToolsBar v-model:searchTerm="searchTerm" placeholderSearch="Numero de mesa..."
        v-model:selectedState="selectedState" :state-options="stateOptions" :loading="isLoading"
        buttonCreateText="Crear Mesa" searchLabel="Buscar mesas" @create="handleCreate">


      </ToolsBar>
      


      <TableList :tables="tables" :loading="isLoading">
        <template #actions="{ table }">
          
          <BaseButton size="icon" @click="handleEdit(table)" variant="secondary">
                <i-mdi-pencil class="w-5 h-5" />
          </BaseButton>
          <BaseButton size="icon" @click="handleDelete(table)" variant="danger">
             <i-mdi-trash class="w-5 h-5" />
          </BaseButton>
          <BaseButton size="icon" @click="handleAddLogin(table)" variant="terciary">
              <i-fa6-solid-user-plus class="w-5 h-5" />
          </BaseButton>
          
          <BaseButton size="icon" @click="handleQrView(table)" variant="primary">
            <i-mdi-qrcode-scan class="w-5 h-5" />
          </BaseButton>

        </template>


      </TableList>


      <BaseModal v-model="showCreateModal" title="Crear Mesa" @close="showCreateModal = false" >
        <CreateTableForm ref="formCreateRef" @completed="showCreateModal = false" />

        <template #footer>
          <BaseButton variant="terciary" @click="showCreateModal = false">
            Cancelar
          </BaseButton>
          <BaseButton @click="triggerSubmit('create')" variant="accent" :loading="formCreateRef?.isLoading">
            Crear Mesa
          </BaseButton>
        </template>
      
      </BaseModal>

      <BaseModal v-model="showEditModal" title="Editar Mesa" @close="showEditModal = false" >
        <EditTableForm ref="formEditRef" :currentTable="currentTable" @completed="showEditModal = false" />

        <template #footer>
          <BaseButton variant="terciary" @click="showEditModal = false">
            Cancelar
          </BaseButton>
          <BaseButton @click="triggerSubmit('edit')" variant="accent" :loading="formEditRef?.isLoading">
            Guardar
          </BaseButton>
        </template>
      
      </BaseModal>

      <BaseModal v-model="showQrModal" maxWidth="2xl" title="QR de la mesa" @close="showQrModal = false">

        <QrWrapper 
        :table-id="currentTable?.id_table"
        >
      </QrWrapper>
        
       

       <template #footer>
          <BaseButton variant="secondary" @click="showQrModal = false">
            Cerrar
          </BaseButton>
          
        </template>
      
      </BaseModal>


      <BaseModal v-model="showAddLoginModal" title="Crear Login" @close="showAddLoginModal = false">

        <CreateLoginTableForm ref="formAddLoginRef"  @completed="showAddLoginModal = false" :current-table="currentTable"></CreateLoginTableForm>

        <template #footer>
          <BaseButton variant="terciary" @click="showAddLoginModal = false">
            Cancelar
          </BaseButton>
          <BaseButton @click="triggerSubmit('addLogin')" variant="accent" :loading="formAddLoginRef?.isLoading">
            Crear Login
          </BaseButton>
        </template>
      
      </BaseModal>



      <FooterDash>

      </FooterDash>

    </div>






  </AppLayout>
</template>


<style scoped>
@reference "../../style.css";

/* Contenedor principal de todo el dashboard */
.dashboard-container {
  @apply flex flex-col gap-6 lg:gap-8;
}
</style>