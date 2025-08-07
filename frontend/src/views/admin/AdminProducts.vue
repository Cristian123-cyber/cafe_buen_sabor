<script setup>

import { ref, onMounted, computed, watch, onUnmounted } from 'vue';
import { storeToRefs } from 'pinia';

import { useAlert } from '../../composables/useAlert';
import { useProductStore } from '../../stores/productS';



const productStore = useProductStore();


const { products } = storeToRefs(productStore)


const alert = useAlert();

const searchTerm = ref('');

const selectedCategory = ref(0);

watch([searchTerm, selectedCategory], ([newSearch, newCategory]) => {
  productStore.setFilters({ term: newSearch, category: newCategory });
});


const isLoading = computed(() => productStore.isLoading);
const categoryOptions = computed(() => {
  const categories = productStore.categories?.map((category) => ({
    value: category.id_category,
    label: category.category_name,
  }));

  return categories ? categories : [];
});


//MODALS CONTROL

const showCreateModal = ref(false);
// Handlers actions
const handleCreate = () => {
  showCreateModal.value = true;

}
const handleViewDetails = (product) => {
  console.log('Ver detalles de:', product);
  // Aquí abrirías un modal o navegarías a una página de detalles
};

const handleEdit = (product) => {
  console.log('Editar:', product);
  // Aquí abrirías un modal/formulario para editar el producto
};

const handleDelete = (product) => {
  console.log('Eliminar:', product);
  // Aquí mostrarías un modal de confirmación antes de eliminar
};


//form references

const formCreateRef = ref(null); // ref para el formulario hijo
const formEditRef = ref(null); // ref para el formulario hijo


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
  productStore.fetchAllCategories();
  productStore.fetchProducts();

  searchTerm.value = productStore.filterTerm;
  selectedCategory.value = productStore.filterCategory;
});




</script>

<template>
  <!-- 
    ¡Aquí está la magia de la composición!
    Envolvemos nuestro contenido (TestView) con nuestro layout (AppLayout).
  -->
  <AppLayout>

    <div class="dashboard-container">


      <HeaderSection title="Gestión de productos"
        descriptionMessage="Administra la información y estado de los productos del local.">

      </HeaderSection>


      <ToolsBar v-model:searchTerm="searchTerm" placeholderSearch="Nombre, ID..."
        v-model:selectedRole="selectedCategory" :roleOptions="categoryOptions" titleRoleOptions="Filtrar por categoria" :loading="isLoading"
        buttonCreateText="Crear Producto" searchLabel="Buscar productos" @create="handleCreate">
      </ToolsBar>

      <ProductsTable :products="products" :loading="isLoading" @view-details="handleViewDetails" @edit="handleEdit"
        @delete="handleDelete" />

      <BasePagination :current-page="1" :per-page="5" :total="200" :per-page-options="[5, 10, 25, 50]" />


      <BaseModal @close="showCreateModal = false" v-model="showCreateModal" title="Crear Producto" max-width="2xl">

        <CreateProductForm ref="formCreateRef" @completed="showCreateModal = false">

        </CreateProductForm>

        


        <template #footer>
          <BaseButton variant="terciary" @click="showCreateModal = false">
            Cancelar
          </BaseButton>
          <BaseButton @click="triggerSubmit('create')" variant="accent" :loading="formCreateRef?.isLoading">
            Crear Producto
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