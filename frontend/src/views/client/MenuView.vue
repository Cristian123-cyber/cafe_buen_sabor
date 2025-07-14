<template>


  <ClientLayout :showSearchInput="true" title="Productos">


    <!-- Search Bar (Mobile Only) -->

    <BaseSearchInput v-model="searchTerm" placeholder="Buscar productos..." />

    <div class="space-y-4 mb-4">
      <CategoryFilters v-model="selectedCategory" :categories="productStore.categories" />

      
    </div>

    <!-- Estado de carga -->
    <div v-if="productStore.isLoading" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
      <div v-for="n in 8" :key="n" class="product-card animate-pulse">
        <!-- Imagen falsa -->
        <div class="w-full rounded-xl bg-gray-300" style="aspect-ratio: 3 / 4;"></div>

        <!-- Info falsa -->
        <div class="px-1 mt-3 space-y-2">
          <div class="h-4 bg-gray-300 rounded w-3/4"></div> <!-- Nombre -->
          <div class="h-3 bg-gray-300 rounded w-1/3"></div> <!-- Precio -->
        </div>
      </div>
    </div>

    <!-- Estado vacío -->
    <div v-else-if="hasFetched && filteredProducts.length === 0"
      class="py-20 text-gray-500 flex flex-col items-center justify-center">
      <i-ph-package-bold class="empty-state-icon" />
      <p class="text-lg font-medium text-center">No hay productos que coincidan con tu búsqueda.</p>
    </div>

    <!-- Productos disponibles -->
    <div v-else
      class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
      <ProductCard v-for="product in filteredProducts" :key="product.id_product" :product="product"
        @view-details="handleViewProduct" />
    </div>


  </ClientLayout>
</template>

<script setup>


import { ref, computed, onMounted, watch } from 'vue';

import { useProductStore } from '../../stores/productS';

import { useRouter } from 'vue-router';

const router = useRouter();





const productStore = useProductStore();


const filteredProducts = computed(() => {

  const search = searchTerm.value.toLowerCase().trim();
  const categoryId = selectedCategory.value;

  return productStore.products.filter(product => {
    const matchesCategory =
      categoryId === null || product.product_category === categoryId;

    const matchesSearch =
      product.product_name.toLowerCase().includes(search);

    return matchesCategory && matchesSearch;
  });
  //return productStore.getProductsByCategory(selectedCategory.value);
});

const hasFetched = ref(false);




const searchTerm = ref('');


const selectedCategory = ref(null);


const handleViewProduct = (id_product) => {

  if (id_product !== 'undefined' && id_product !== null) {
    console.log('navegando a vista de detalle', id_product);

    router.push({ name: 'ProductDetail', params: { id: id_product } });
  }

}


// 3. Llamar a la acción para cargar los datos cuando el componente se monte
onMounted(async () => {
  await productStore.fetchAllProducts();
  await productStore.fetchAllCategories();
  hasFetched.value = true;
});





</script>

<style scoped>
@reference '../../style.css';

.empty-state-icon {
  @apply text-4xl mb-4 text-gray-400;
}
</style>