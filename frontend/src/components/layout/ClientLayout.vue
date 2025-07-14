<template>
  <div class="min-h-screen bg-secondary">

    <!-- Sidebar fijo en desktop -->
    <nav class="hidden md:flex fixed top-0 left-0 h-screen w-64 flex-col border-r border-primary-light bg-primary px-4 py-6 z-30">
      <div class="flex items-center gap-2 px-2 mb-10">
        <i-mdi-coffee class="h-8 w-8 text-accent" />
        <h2 class="text-xl font-bold text-text-light tracking-wider">Cafe Buen Sabor</h2>
      </div>

      <div class="flex flex-col gap-y-2">
        <template v-for="link in navigationItems" :key="link.to.name">
          <router-link 
            :to="link.to" 
            custom 
            v-slot="{ href, navigate, isActive }"
          >
            <a 
              :href="href" 
              @click="navigate" 
              class="nav-link-desktop"
              :class="{ 'nav-link-desktop--active': isActive }"
            >
              <component :is="link.icon" class="h-5 w-5 flex-shrink-0" />
              <span class="nav-label">{{ link.label }}</span>
            </a>
          </router-link>
        </template>
      </div>
    </nav>

    <!-- Contenido -->
    <main class="md:pl-64 bg-secondary flex flex-col overflow-y-auto min-h-screen">

      <!-- Header Mobile -->
      <div class="md:hidden flex items-center bg-surface p-4 pb-3 justify-between sticky top-0 z-10">
        <div class="w-10"></div>
        <h2 class="text-text text-lg font-bold leading-tight tracking-wide flex-1 text-center">
          {{ title }}
        </h2>
        <div class="flex w-10 items-center justify-end">
          <BaseButton :to="{ name: 'ClientCart'}" size="icon" variant="ghost" class="relative">
            <i-mynaui-cart-solid class="h-6 w-6 text-text"/>
            <span v-if="cartCounter > 0" class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-text-light">{{ cartCounterComputed }}</span>
          </BaseButton>
        </div>
      </div>

      <!-- Header Desktop -->
      <header class="hidden md:flex items-center justify-between border-b border-border-light bg-surface px-6 py-4">
        <h1 class="text-2xl font-bold text-text tracking-tight">{{ title }}</h1>
        <div class="flex items-center gap-4">
          <BaseButton :to="{ name: 'ClientCart'}" size="icon" variant="ghost" class="relative">
            <i-mynaui-cart-solid class="h-6 w-6 text-text"/>
            <span v-if="cartCounter > 0" class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-text-light">{{ cartCounterComputed }}</span>
          </BaseButton>
        </div>
      </header>

      <!-- Contenido dinámico -->
      <div class="p-4 sm:p-6">
        <slot></slot>
        <div class="h-24 md:h-8"></div>
      </div>

    </main>

    <!-- Bottom Navigation para Mobile -->
    <div class="fixed bottom-0 left-0 right-0 z-20 border-t border-primary-light bg-primary shadow-t-lg md:hidden">
      <div class="flex justify-around px-2 pb-2 pt-2">
        <template v-for="link in navigationItems" :key="link.to.name">
          <router-link 
            :to="link.to" 
            custom 
            v-slot="{ href, navigate, isActive }"
          >
            <a 
              :href="href" 
              @click="navigate" 
              class="nav-link-mobile"
              :class="{ 'nav-link-mobile--active': isActive }"
            >
              <component :is="link.icon" class="h-6 w-6" />
              <span class="nav-label text-xs">{{ link.label }}</span>
            </a>
          </router-link>
        </template>
      </div>
    </div>

  </div>
</template>


<script setup>
import { ref, computed } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import { getNavigationForRole } from '../../router/navigation';
import { useCartStore } from '../../stores/cartS';


const cartStore = useCartStore();

const props = defineProps({
    title: {
        type: String,
        default: 'Café Buen Sabor'
    }
});

const cartCounter = computed(() => cartStore.totalItems);

const cartCounterComputed = computed(() => {
    return cartStore.totalItems < 10 ? cartStore.totalItems : '9+'
});

const route = useRoute();

// No changes to logic, navigation items are computed as before
const navigationItems = computed(() => {
    return getNavigationForRole('Client');  
});

</script>

<style scoped>
/*
  IMPORTANTE: Tailwind v4 requiere que cada archivo CSS que use @apply
  referencie el archivo de tema central.
*/
@reference "../../style.css";

/* --- Desktop Sidebar Link --- */
/* Provides a clear visual hierarchy on the dark primary background */
.nav-link-desktop {
    @apply flex items-center gap-x-3 rounded-lg px-3 py-2.5 text-sm font-medium text-text-muted transition-colors duration-200;
    @apply hover:bg-primary-light hover:text-text-light;
}

/* Active state uses the accent color for a vibrant, clear indicator */
.nav-link-desktop--active {
    @apply bg-accent text-primary font-semibold;
}

/* --- Mobile Bottom Nav Link --- */
/* Designed for easy tapping and clear visual feedback */
.nav-link-mobile {
    @apply flex flex-1 flex-col items-center justify-center gap-1 rounded-lg py-1.5 text-text-muted transition-colors duration-200;
    @apply hover:bg-primary-light;
}

/* Active state on mobile uses the accent color for text and icon */
.nav-link-mobile--active {
    @apply text-accent font-medium;
}

/* Custom shadow for the top edge, more subtle than a standard shadow */
.shadow-t-lg {
  box-shadow: 0 -10px 15px -3px rgb(0 0 0 / 0.05), 0 -4px 6px -4px rgb(0 0 0 / 0.05);
}
</style>