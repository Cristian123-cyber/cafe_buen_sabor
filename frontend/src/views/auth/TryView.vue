<template>
  <div class="p-4">
    <h1 class="text-3xl font-bold text-text">Mesa 5</h1>
    <!-- ... otros detalles de la mesa ... -->
    <button @click="confirmOrder" class="px-6 py-3 rounded-lg text-white font-bold bg-accent hover:bg-accent-dark transition-colors">
      Confirmar Pedido para Cocina
    </button>
  </div>
</template>

<script setup>
import { useAlert } from '../../composables/useAlert';

const alert = useAlert();

// Simula una llamada a la API
const sendOrderToKitchenAPI = async () => {
  console.log("Enviando a cocina...");
  // Simular un retraso de red
  await new Promise(res => setTimeout(res, 1000));
  console.log("¡Pedido enviado!");
  return { success: true };
};

const confirmOrder = async () => {
  const isConfirmed = await alert.show({
    variant: 'warning',
    title: '¿Confirmar Pedido?',
    message: 'El pedido se enviará a la cocina y no podrá modificarse. ¿Deseas continuar?',
    confirmButtonText: 'Sí, enviar',
    cancelButtonText: 'Aún no',
  });

  if (isConfirmed) {
    // El usuario presionó "Sí, enviar"
    try {
      await sendOrderToKitchenAPI();
      // Muestra una notificación de éxito, que se autocierra.
      await alert.show({
        variant: 'success',
        title: '¡Enviado!',
        message: 'El pedido ha sido enviado a la cocina con éxito.'
      });
    } catch (e) {
      await alert.show({
        variant: 'error',
        title: 'Error de Red',
        message: 'No se pudo comunicar con la cocina. Por favor, intenta de nuevo.'
      });
    }
  } else {
    // El usuario presionó "Aún no" o hizo clic fuera.
    console.log("El mesero decidió no enviar el pedido todavía.");
  }
};
</script>