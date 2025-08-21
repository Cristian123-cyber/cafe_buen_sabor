<script setup>
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();

const mesaId = route.params.id;

const goBack = () => {
  router.push({ name: 'WaiterTables' });
};
</script>

<template>
  <div>
    <h2>Órdenes de la mesa {{ mesaId }}</h2>

    <!-- Aquí renderizas la lista de órdenes -->
    <p>Mostrando órdenes en tiempo real...</p>

    <BaseButton variant="secondary" @click="goBack">
      ← Volver a Mesas
    </BaseButton>
  </div>
</template>
