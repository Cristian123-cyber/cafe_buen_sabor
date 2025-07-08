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
  @apply w-screen h-screen flex items-center justify-center bg-primary-dark;
}

</style>