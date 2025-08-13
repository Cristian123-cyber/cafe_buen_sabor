

<script setup>



</script>

<template>
  <!-- 
    ¡Aquí está la magia de la composición!
    Envolvemos nuestro contenido (TestView) con nuestro layout (AppLayout).
  -->
  <AppLayout>

    

     <div class="dashboard-container">

      <HeaderSection title="Gestión de ordenes"
        descriptionMessage="Administra la información de las ordenes del local.">

      </HeaderSection>

<!-- 
      <ToolsBar v-model:searchTerm="searchTerm" placeholderSearch="Nombre, ID..."
        v-model:selectedRole="selectedCategory" :roleOptions="categoryOptions" :loading="isLoading"
        buttonCreateText="Crear Producto" searchLabel="Buscar productos" @create="handleCreate">
      </ToolsBar> -->


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