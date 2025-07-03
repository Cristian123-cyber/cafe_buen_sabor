<script setup>

</script>

<template>
  <!-- 
    ¡Aquí está la magia de la composición!
    Envolvemos nuestro contenido (TestView) con nuestro layout (AppLayout).
  -->
  <AppLayout>
    <!-- El contenido de TestView se inyectará en el <slot> de AppLayout -->
    <TestView />
  </AppLayout>
</template>