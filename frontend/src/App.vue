<script setup>
import { ref, onMounted } from 'vue';

import { useLoadingStore } from './stores/loadingS.js';
import api from './services/api.js';



const appStore = useLoadingStore();



</script>

<template>

  
  
    
  <RouterView v-slot="{ Component }">
    <transition name="fade" mode="out-in">
      <component :is="Component" />
    </transition>
  </RouterView>
   <ToastManager />
  <GlobalAlertPresenter />
</template>

<style scoped>
/* (Los estilos son los mismos que antes, pero adaptados a una tabla) */
#app-container {
  max-width: 800px;
  margin: 2rem auto;
  padding: 2rem;
  font-family: sans-serif;
}

header {
  text-align: center;
  margin-bottom: 2rem;
  border-bottom: 1px solid #eaeaea;
  padding-bottom: 1rem;
}

.logo {
  display: inline-block;
  vertical-align: middle;
  margin-right: 1rem;
}

.wrapper {
  display: inline-block;
  vertical-align: middle;
}

.card {
  border: 1px solid #ccc;
  border-radius: 8px;
  padding: 1.5rem;
  background-color: #f9f9f9;
  color: #000;
}

.error {
  color: #dc3545;
  font-weight: bold;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
  color: black;

}

th,
td {
  border: 1px solid #ddd;
  padding: 12px;
  text-align: left;
}

th {
  background-color: #f2f2f2;
  font-weight: bold;
}

tr:nth-child(even) {
  background-color: #f9f9f9;
}

tr:hover {
  background-color: #eef;
}
</style>
