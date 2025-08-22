<script setup>

import { ref, onMounted, computed, watch, onUnmounted } from 'vue';
import { storeToRefs } from 'pinia';

import { useAlert } from '../../composables/useAlert';
import { useProductStore } from '../../stores/productS';

import { useToasts } from '../../composables/useToast';


const productStore = useProductStore();


const { products } = storeToRefs(productStore)


const alert = useAlert();
const { addToast } = useToasts();

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
const showEditModal = ref(false);
const currentProduct = ref(null);
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
  currentProduct.value = product;
  showEditModal.value = true;
  // Aquí abrirías un modal/formulario para editar el producto
};

const handleDelete = async (product) => {
  
   const isConfirmed = await alert.show({
    variant: 'warning',
    title: '¿Desea eliminar este producto?',
    message: 'Este producto sera eliminado del sistema. ¿Deseas continuar?',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
  });
  

  if (isConfirmed) {

    try {
      await productStore.removeProduct(product.id_product);
      addToast({
            message: `El producto ${product.product_name} ha sido eliminado exitosamente`,
            title: 'Producto eliminado',
            type: 'info',
            duration: 2000
        });
    } catch (error) {
      alert.show({
        variant: 'error',
        title: 'Error al eliminar el producto',
        message: error?.message ? error?.message : `No se pudo eliminar el producto ${product.product_name}. Inténtalo de nuevo más tarde.`,
      });

    }
  }


}



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

      <BaseModal v-model="showEditModal" title="Editar Producto" @close="showEditModal = false" max-width="2xl" >
        <EditProductForm ref="formEditRef" :currentProduct="currentProduct" @completed="showEditModal = false" />

        <template #footer>
          <BaseButton variant="terciary" @click="showEditModal = false">
            Cancelar
          </BaseButton>
          <BaseButton @click="triggerSubmit('edit')" variant="accent" :loading="formEditRef?.isLoading">
            Guardar
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