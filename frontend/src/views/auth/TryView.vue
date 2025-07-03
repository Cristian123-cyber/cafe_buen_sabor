<template>

  

    <div class="p-4">
      <h1 class="text-3xl font-bold text-text">Mesa 5</h1>
      <!-- ... otros detalles de la mesa ... -->
      <button @click="confirmOrder"
      class="px-6 py-3 rounded-lg text-white font-bold bg-accent hover:bg-accent-dark transition-colors">
      Confirmar Pedido para Cocina
    </button>
    
    
    <!-- En cualquier vista, por ej: src/views/waiter/DashboardView.vue -->
    <div class="flex items-center gap-4 p-4">
      <BaseBadge color="success">Completado</BaseBadge>
      <BaseBadge color="warning">Pendiente</BaseBadge>
      <BaseBadge color="error">Cancelado</BaseBadge>
      <BaseBadge color="info">En cocina</BaseBadge>
      <BaseBadge color="accent">VIP</BaseBadge>
      <BaseBadge color="primary">Nuevo</BaseBadge>
      
      <span class="relative">
        <i-mdi-bell class="w-6 h-6" />
        <BaseBadge color="error" dot class="absolute -top-1 -right-1" />
      </span>
      <span class="flex items-center gap-2">
        Usuario Activo
        <BaseBadge color="success" dot />
      </span>
      
      <BaseBadge color="success">
        <template #icon><i-mdi-check-circle class="w-4 h-4" /></template>
        Pago Verificado
      </BaseBadge>
      
      <BaseBadge color="error" variant="outline">
        <template #icon><i-mdi-alert-circle class="w-4 h-4" /></template>
        Stock Bajo
      </BaseBadge>
      
      <BaseBadge color="info">
        <template #icon><i-mdi-fire class="w-4 h-4" /></template>
        En preparación
      </BaseBadge>
      
      
      <!-- Nuestro nuevo componente en acción! -->
      <NotificationBell
      :count="7"
      :maxCount="2"
      />
      
      
    </div>
    
    
    
    
  </div>
  


</template>

<script setup>
import { useAlert } from '../../composables/useAlert';
import { useToasts } from '../../composables/useToast'; // Importamos el composable

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

      const { addToast } = useToasts(); // Lo instanciamos aquí

      addToast({
        title: 'Pedido enviado pa',
        message: `El pedido está listo para ser recogido.`,
        type: 'error',
        duration: 5000, // Dura más para dar tiempo a verlo
      });
      addToast({
        title: 'Pedido enviado pa',
        message: `El pedido está listo para ser recogido.`,
        type: 'warning',
        duration: 5000, // Dura más para dar tiempo a verlo
      });
      addToast({
        title: 'Pedido enviado pa',
        message: `El pedido está listo para ser recogido.`,
        type: 'success',
        duration: 5000, // Dura más para dar tiempo a verlo
      });
      addToast({
        title: 'Pedido enviado pa',
        message: `El pedido está listo para ser recogido.`,
        type: 'info',
        duration: 5000, // Dura más para dar tiempo a verlo
      });
      /* await alert.show({
        variant: 'success',
        title: '¡Enviado!',
        message: 'El pedido ha sido enviado a la cocina con éxito.'
      }); */
    } catch (e) {
      await alert.show({
        variant: 'error',
        title: 'Error de Red ' + e ,
        message: 'No se pudo comunicar con la cocina. Por favor, intenta de nuevo.'
      });
    }
  } else {
    // El usuario presionó "Aún no" o hizo clic fuera.
    console.log("El mesero decidió no enviar el pedido todavía.");
  }
};
</script>


<style scoped>


</style>