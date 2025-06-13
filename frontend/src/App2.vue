<template>
  <div class="min-h-screen bg-gray-100 py-8">
    <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">
      Prueba de Tailwind CSS
    </h1>
    
    <!-- Componente de prueba -->
    <TestCafe />
    
    <!-- Prueba con props personalizados -->
    <TestCafe 
      nombre-cafe="Cappuccino Deluxe"
      descripcion="Cremoso cappuccino con espuma de leche perfecta"
      :precio="5.25"
      :rating="4.9"
      :es-organico="false"
    />
    <TestCafe 
      nombre-cafe="Cappuccino Deluxe"
      descripcion="Cremoso cappuccino con espuma de leche perfecta"
      :precio="5.25"
      :rating="4.9"
      :es-organico="false"
    />
  </div>
</template>

<script setup>
import TestCafe from './components/TestCafe.vue'
</script>