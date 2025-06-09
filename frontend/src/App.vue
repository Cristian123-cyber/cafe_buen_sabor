<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const productos = ref([]);
const resTest = ref(null);
const isLoading = ref(true);
const errorMessage = ref(null);

const fetchData = async () => {
  isLoading.value = true;
  errorMessage.value = null;

  try {
    // Hacemos ambas peticiones en paralelo para más eficiencia
    const [productosResponse, testResponse] = await Promise.all([
      axios.get('/api/productos'),
      axios.get('/api/test')
    ]);

    // Procesamos la respuesta de productos
    if (productosResponse.data.success) {
      productos.value = productosResponse.data.data; // Accedemos al array dentro de 'data'
    } else {
      // Si la API devuelve success: false, lo tratamos como un error.
      throw new Error(productosResponse.data.message || 'Error al obtener los productos.');
    }

    // Procesamos la respuesta de test
    if (testResponse.data.success) {
      resTest.value = JSON.stringify(testResponse.data, null, 2);
    } else {
      throw new Error(testResponse.data.message || 'Error en el endpoint de prueba.');
    }

  } catch (error) {
    console.error('Ocurrió un error al obtener los datos de la API:', error);
    errorMessage.value = `No se pudieron cargar los datos de la API. (Error: ${error.message})`;
  
  } finally {
    isLoading.value = false;
  }
};

onMounted(fetchData);
</script>

<template>
  <div id="app-container">
    <header>
      <img alt="Vue logo" class="logo" src="./assets/vue.svg" width="125" height="125" />
      <div class="wrapper">
        <h1>Menú del Café Buen Sabor</h1>
      </div>
    </header>

    <main>
      <div class="card">

        <div v-if="isLoading">🔄 Cargando menú...</div>
        
        <div v-if="errorMessage" class="error">❌ {{ errorMessage }}</div>
        
        <table v-if="!isLoading && productos.length > 0">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Categoría</th>
              <th>Precio</th>
            </tr>
          </thead>
          <tbody>
            <!-- Usamos v-for para iterar sobre la lista de productos y crear una fila por cada uno -->
            <tr v-for="producto in productos" :key="producto.id">
              <td>{{ producto.nombre }}</td>
              <td>{{ producto.categoria }}</td>
              <td>${{ producto.precio.toFixed(2) }}</td>
            </tr>
          </tbody>
        </table>

        <div v-if="!isLoading && productos.length === 0 && !errorMessage">
          No hay productos en el menú en este momento.
        </div>

        <div v-if="!isLoading && resTest">
          <h2>Respuesta de la API test:</h2>
          <pre>{{ resTest }}</pre>
        </div>
      </div>
    </main>
  </div>
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
th, td {
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
