<template>
  <DisplayLayout>
    <div v-if="isLoading" class="text-center text-text-muted">
      <i-svg-spinners-bars-scale-fade class="w-16 h-16 mx-auto" />
      <p class="mt-4 text-xl">Cargando información de la mesa...</p>
    </div>

    <div v-else-if="error" class="text-center text-error p-8">
      <i-mdi-alert-circle class="w-20 h-20 mx-auto" />
      <h2 class="mt-4 text-3xl font-bold">Error de Conexión</h2>
      <p class="mt-2 text-xl">{{ error }}</p>
    </div>

    <div v-else-if="qrToken" class="text-center text-text-muted">
      <h1 class="text-6xl font-bold mb-6">Mesa {{ tableId }}</h1>
      
      <QRComponent
      :qr_token="qrToken"
      :tableId="tableId"
      :qrSize="300"

      >


      </QRComponent>
      
      <p class="mt-6 text-2xl">Escanea para ver nuestro menú</p>
      <p class="text-lg text-text-muted">¡Buen provecho!</p>
    </div>
  </DisplayLayout>
</template>

<script setup>
import { useRoute } from 'vue-router';
import { useTableDisplay } from '../../composables/useTableQR.js';



// 1. Obtenemos la ruta actual para acceder a los parámetros.
const route = useRoute();

// 2. Extraemos el ID de la mesa de los parámetros de la URL.
const tableId = route.params.table_id;

// 3. Usamos el composable, que se encarga de toda la lógica.
// Le pasamos el ID de la mesa para que sepa qué QR solicitar.
const { qrToken, isLoading, error } = useTableDisplay(tableId);
</script>

<style scoped>
/* Si necesitas algún estilo específico para esta vista, puedes añadirlo aquí. */
</style>