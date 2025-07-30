<template>
    <div class="empty-state">

     <div class="empty-content">
          <div class="empty-icon-container">
            <i-mingcute-empty-box-fill class="empty-icon" />
          </div>
          <h3 class="empty-title">No hay datos disponibles</h3>
          <p class="empty-description">No hay registros que mostrar.</p>
    </div>
    </div>
</template>

<script setup>

</script>

<style scoped>

@reference "../../style.css";

/* === ESTADO VACÍO MEJORADO === */
.empty-state {
  @apply w-full rounded-xl border-2 border-dashed border-border p-12 text-center;
  @apply transition-all duration-500;
  @apply bg-surface-dark;
}

.empty-state:hover {
  @apply border-accent bg-hover;
}

.empty-content {
  @apply flex flex-col items-center gap-4 text-text-muted max-w-md mx-auto;
}

.empty-icon-container {
  @apply w-24 h-24 mx-auto rounded-full bg-surface-darker flex items-center justify-center;
  @apply transition-all duration-500;
}

.empty-state:hover .empty-icon-container {
  @apply bg-accent;
}

.empty-icon {
  @apply w-12 h-12 text-text-muted;
  @apply transition-colors duration-500;
}

.empty-state:hover .empty-icon {
  @apply text-text-light;
}

.empty-title {
  @apply text-xl font-bold text-text mt-2;
}

.empty-description {
  @apply text-sm text-text-muted leading-relaxed;
}

</style>