<script setup>
import { ref, watch, onBeforeUnmount } from 'vue'


// Estado del sidebar en móvil
const isSidebarOpen = ref(false)

function toggleSidebar() {
  isSidebarOpen.value = !isSidebarOpen.value
}
function closeSidebar() {
  isSidebarOpen.value = false
}



// Bloquea scroll cuando el sidebar está abierto (solo en móvil)
watch(isSidebarOpen, (open) => {
  if (open) {
    document.body.classList.add('overflow-hidden');
  } else {
    document.body.classList.remove('overflow-hidden');
  }
});

// Limpieza por si acaso
onBeforeUnmount(() => {
  document.body.classList.remove('overflow-hidden');
});
</script>

<template>
  <div class="app-layout">
    <!-- El sidebar está perfecto aquí -->
    <AppSidebar :is-open="isSidebarOpen" @close="closeSidebar" />

    <!-- Zona principal (header + contenido) -->
    <div class="main-wrapper">
      <AppHeader @toggle-sidebar="toggleSidebar" />

      <!-- El slot para el contenido de la vista -->
      <main class="main-content">
        <slot></slot>
      </main>
    </div>
  </div>
</template>

<style scoped>
@reference "../../style.css";

.app-layout {
  /* [CLAVE 1] El contenedor principal ocupa toda la altura de la ventana */
  @apply h-screen;

  /* Por defecto (móvil) es un contenedor de bloque normal */
  @apply block;

  /* En desktop, usamos un Grid. La primera columna para el sidebar, la segunda para el resto. */
  /* [AJUSTE] Usamos un ancho fijo (288px = w-72) para el sidebar en lugar de 'auto'. */
  @apply lg:grid lg:grid-cols-[288px_1fr];
}

.main-wrapper {
  /* [CLAVE 2] Este wrapper debe ocupar toda la altura disponible en su celda del grid */
  /* y usar flexbox para organizar su propio contenido (Header y Main). */
  @apply h-full flex flex-col;

  /* [CLAVE 3] Esto es crucial para que el `main-content` no se desborde */
  /* y sepa que no puede ser más alto que este contenedor. */
  @apply overflow-y-hidden;
}

.main-content {
  /* [CLAVE 4] Le decimos que ocupe todo el espacio vertical sobrante. */
  @apply flex-1;
  /* Y que si su contenido es muy largo, este es el elemento que debe tener scroll. */
  @apply overflow-y-auto;
  
  /* Estilos de fondo y padding para el área de contenido. */
  @apply bg-surface-dark p-4 sm:p-6 lg:p-8;
}
</style>
