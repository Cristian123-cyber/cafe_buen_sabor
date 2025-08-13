<template>
    <!-- Estado de carga -->
    <div class="state-wrapper">
        <i-svg-spinners-bars-scale-fade class="h-16 w-16 text-blue-500 opacity-80" />
    </div>
</template>

<script setup>

</script>

<style scoped>

@reference '../../style.css';
.state-wrapper {
  @apply flex flex-1 items-center justify-center p-4;
  min-height: 320px;
  max-height: 100%; /* Nuevo: Control de altura */
}

</style>