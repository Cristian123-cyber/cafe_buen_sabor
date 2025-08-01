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
// Handlers actions
const handleCreate = () => {
  //showCreateModal.value = true;

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
        v-model:selectedRole="selectedCategory" :roleOptions="categoryOptions" :loading="isLoading"
        buttonCreateText="Crear Producto" searchLabel="Buscar productos" @create="handleCreate">
      </ToolsBar>

      <ProductsTable :products="products" :loading="isLoading" @view-details="handleViewDetails" @edit="handleEdit"
        @delete="handleDelete" />

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