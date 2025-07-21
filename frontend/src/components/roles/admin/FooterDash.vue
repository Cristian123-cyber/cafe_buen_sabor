<script setup>
import { computed } from 'vue'

const props = defineProps({
  version: {
    type: String,
    default: '1.0.0',
    validator: (value) => /^\d+\.\d+\.\d+$/.test(value)
  },
  companyName: {
    type: String,
    default: 'Café Buen Sabor'
  }
})

const currentYear = computed(() => new Date().getFullYear())
</script>

<template>
  <footer class="dashboard-footer">
  <div class="footer-content">
    
    <!-- Izquierda -->
    <div class="footer-info-left">
      <span class="copyright">
        &copy; {{ currentYear }} - <span class="brand">{{ companyName }}</span>
      </span>
    </div>
    
    <!-- Derecha -->
    <div class="footer-info-right">
      <span class="version">
        v{{ version }}
      </span>
    </div>

  </div>
</footer>
</template>

<style scoped>
@reference '../../../style.css';
.dashboard-footer {
  @apply flex items-center py-4 px-6 bg-transparent;
  min-height: 2.5rem;
}

.footer-content {
  @apply w-full mx-auto flex justify-between items-center;
  max-width: 1536px;
}

.footer-info-left,
.footer-info-right {
  @apply flex items-center gap-2;
}

.footer-info-left {
  @apply text-sm text-gray-600;
}

.footer-info-right {
  @apply text-xs font-mono text-gray-600 bg-gray-100 px-2 py-1 rounded;
}

/* Dark mode */
.dark .footer-info-left {
  @apply text-gray-400;
}

.dark .footer-info-right {
  @apply text-gray-400 bg-gray-800;
}
</style>