<script setup>

import { ref, onMounted, computed, watch } from 'vue';
import { useEmployeStore } from '../../stores/employeesS';
import { useAlert } from '../../composables/useAlert';
import { useToasts } from '../../composables/useToast';



const alert = useAlert();
const { addToast } = useToasts();



const employeStore = useEmployeStore();


const searchTerm = ref('');

const selectedRole = ref(0);
const selectedState = ref(0);

const roleOptions = computed(() => {
  const roles = employeStore.roles?.map((rol) => ({
    value: rol.id_rol,
    label: rol.rol_name,
  }));

  roles?.unshift({ value: 0, label: 'Todos' });

  return roles ? roles : [];
});

const stateOptions = computed(() => {
  const estados = employeStore.states?.map((state) => ({
    value: state.id_status,
    label: state.status_name,
  }));

  estados?.unshift({ value: 0, label: 'Todos' });

  return estados ? estados : [];
});




// Getter computado del store para pasar a la tabla
const filteredEmployees = computed(() => employeStore.employees);
const isLoading = computed(() => employeStore.isLoading);



//MODALS STATES

const showCreateModal = ref(false);
const showEditModal = ref(false);
const showChangePasswordModal = ref(false);
const currentEmployee = ref(null);

//FORMS REFS

const formCreateRef = ref(null); // ref para el formulario hijo
const formEditRef = ref(null); // ref para el formulario hijo


watch([searchTerm, selectedRole, selectedState], ([newSearch, newRole, newState]) => {
  employeStore.setFilters({ term: newSearch, role: newRole, state: newState });
});


// --- MANEJADORES DE EVENTOS DE LA TABLA ---
// Estas funciones se activarán cuando EmployeesTable emita los eventos.
const handleEdit = (employee) => {
  console.log('Editar empleado:', employee);
  currentEmployee.value = employee;
  showEditModal.value = true;
  // Aquí abrirías el modal de edición pasándole el objeto 'employee'
}

const handleDelete = async (employee) => {
  console.log('Desactivar empleado:', employee);



  const isConfirmed = await alert.show({
    variant: 'warning',
    title: '¿Desea eliminar este registro?',
    message: 'Este empleado sera desactivado del sistema. ¿Deseas continuar?',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
  });


  if (isConfirmed) {

    try {
      await employeStore.deleteEmploye(employee.id_employe);
      addToast({
            message: 'Empleado desactivado exitosamente',
            title: 'Exito',
            type: 'info',
            duration: 2000
        });
    } catch (error) {
      alert.show({
        variant: 'error',
        title: 'Error al desactivar empleado',
        message: error?.message ? error?.message : `No se pudo desactivar al empleado ${employee.employe_name}. Inténtalo de nuevo más tarde.`,
      });

    }
  }

}

const handleChangePassword = (employee) => {
  console.log('Cambiar contraseña para:', employee);
  // Aquí abrirías el modal de cambio de contraseña
}

const handleCreate = () => {
  console.log('Crear nuevo empleado');
  showCreateModal.value = true;

}

const handlePageChange = async (page) => {
  await employeStore.setPage(page)
}

const handlePerPageChange = async (perPage) => {
  await employeStore.setPerPage(perPage)
}

const triggerSubmit = async (form) => {

  switch (form) {
    case 'create':
      formCreateRef.value?.submit();
      break;
    case 'edit':
      formEditRef.value?.submit();
      break;
    default:
      console.warn('No se ha definido un formulario para enviar');
      break;
  };

}
onMounted(() => {
 
  employeStore.fetchEmployees();
  employeStore.fetchRoles();
  employeStore.fetchStates();


  searchTerm.value = employeStore.filterTerm;
  selectedRole.value = employeStore.filterRol;
  selectedState.value = employeStore.filterState;
  console.log(employeStore.filterState);

});

</script>

<template>
  <!-- 
    ¡Aquí está la magia de la composición!
    Envolvemos nuestro contenido (TestView) con nuestro layout (AppLayout).
  -->
  <AppLayout>


    <div class="dashboard-container">
      <HeaderSection title="Gestión de usuarios"
        descriptionMessage="Administra la información y estado de los empleados del sistema.">

      </HeaderSection>

      <ToolsBar v-model:searchTerm="searchTerm" v-model:selectedRole="selectedRole"
        v-model:selectedState="selectedState" :role-options="roleOptions" :state-options="stateOptions"
        :loading="isLoading" buttonCreateText="Crear Empleado" @create="handleCreate">

        <template #create-btn-icon>
          <i-mdi-account-plus />
        </template>
      </ToolsBar>


      <EmployeesTable :employees="filteredEmployees" :loading="isLoading" @edit="handleEdit" @delete="handleDelete"
        @change-password="handleChangePassword">
        

      </EmployeesTable>
      <!-- Componente de Paginación -->
      <BasePagination :current-page="employeStore.currentPage" :per-page="employeStore.perPage"
        :total="employeStore.total" :per-page-options="[5, 10, 25, 50]" @page-changed="handlePageChange"
        @per-page-changed="handlePerPageChange" />

      <FooterDash>

      </FooterDash>


      <BaseModal v-model="showCreateModal" title="Crear Empleado" max-width="2xl">
        <CreateEmployeeForm ref="formCreateRef" @completed="showCreateModal = false">

        </CreateEmployeeForm>


        <template #footer>
          <BaseButton variant="terciary" @click="showCreateModal = false">
            Cancelar
          </BaseButton>
          <BaseButton @click="triggerSubmit('create')" variant="accent" :loading="formCreateRef?.isLoading">
            Crear Empleado
          </BaseButton>
        </template>
      </BaseModal>


      <BaseModal v-model="showEditModal" title="Editar Empleado" max-width="2xl">
        <EditEmployeeForm :currentEmployee="currentEmployee" ref="formEditRef" @completed="showEditModal = false">

        </EditEmployeeForm>


        <template #footer>
          <BaseButton variant="terciary" @click="showEditModal = false">
            Cancelar
          </BaseButton>
          <BaseButton @click="triggerSubmit('edit')" variant="accent" :loading="formEditRef?.isLoading">
            Guardar
          </BaseButton>
        </template>
      </BaseModal>


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