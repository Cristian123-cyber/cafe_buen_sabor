<script setup>
// No se necesita lógica de script, es un componente puramente presentacional.
</script>

<template>
  <div class="display-layout">
    <slot></slot>
  </div>
</template>

<style scoped>
@reference "../../style.css";

.display-layout {
  @apply w-screen min-h-screen flex items-center justify-center bg-surface-dark;
  padding: 2rem 1rem; /* margen para que no se corte arriba/abajo */
  overflow-y: auto;
}

</style>