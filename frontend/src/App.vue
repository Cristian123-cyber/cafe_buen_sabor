<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useLoadingStore } from './stores/loadingS.js';
import api from './services/api.js';


const route = useRoute();
const appStore = useLoadingStore();



</script>

<template>

  
  
    
  <router-view v-slot="{ Component }" class="bg-primary">
    
  </router-view>
   <ToastManager />
  <GlobalAlertPresenter />
</template>

<style scoped>


</style>
