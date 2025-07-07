<template>
  <div class="p-8">
    <h1 class="text-2xl font-bold mb-4">Página de Login (Modo Desarrollo)</h1>

    <!-- Formulario de login real (lo dejas para cuando la API esté lista) -->
    <!-- <form @submit.prevent="handleLogin"> ... </form> -->

    <div class="mt-8 p-4 border-2 border-dashed border-red-400 rounded-lg">
      <h2 class="text-lg font-semibold text-red-600">Panel de Simulación</h2>
      <p class="text-sm text-gray-600 mb-4">Usa estos botones para entrar como diferentes roles sin necesidad del
        backend.</p>
      <div class="flex gap-4">
        <button @click="fakeLogin('Device')" class="bg-blue-500 text-white px-4 py-2 rounded">Entrar como
          Device</button>
        <button @click="fakeLogin('Mesero')" class="bg-blue-500 text-white px-4 py-2 rounded">Entrar como
          Mesero</button>
        <button @click="fakeLogin('Cocinero')" class="bg-green-500 text-white px-4 py-2 rounded">Entrar como
          Cocinero</button>
        <button @click="fakeLogin('Administrador')" class="bg-gray-800 text-white px-4 py-2 rounded">Entrar
          como Admin</button>
        <button @click="fakeLogin('Cajero')" class="bg-gray-800 text-white px-4 py-2 rounded">Entrar
          como Cajero</button>
      </div>
    </div>



  </div>


</template>

<script setup>
import { reactive, ref, watch, computed } from 'vue';
import { useAuthStore } from '../../stores/authS';

import { useAuth } from '../../composables/useAuth';

const { fakeLogin } = useAuth()


// --- Estado para RadioGroup ---
const settings = reactive({
  delivery: 'table',
});

const deliveryOptions = [
  { value: 'table', label: 'Servir en la mesa' },
  { value: 'pickup', label: 'Recoger en mostrador' },
];

// --- LÓGICA CORRECTA PARA CHECKBOX INDETERMINADO ---
const notifications = reactive({
  orderConfirmed: true,
  orderReady: false,
  orderDelivered: false,
});

// `isIndeterminate` es true si algunas (pero no todas) están seleccionadas.
const isIndeterminate = computed(() => {
  const selectedCount = Object.values(notifications).filter(Boolean).length;
  return selectedCount > 0 && selectedCount < Object.keys(notifications).length;
});

// `selectAllNotifications` es un computed con get/set, la forma más robusta.
const selectAllNotifications = computed({
  // GET: Determina si la caja "Todos" debe estar marcada.
  // Devuelve `true` solo si todas las notificaciones están marcadas.
  get() {
    return Object.values(notifications).every(Boolean);
  },
  // SET: Se ejecuta cuando el usuario hace clic en la caja "Todos".
  // `newValue` será true o false. Se aplica a todas las notificaciones hijas.
  set(newValue) {
    Object.keys(notifications).forEach(key => {
      notifications[key] = newValue;
    });
  }
});
const options = reactive([
  { value: 0, label: 'Device' },
  { value: 1, label: 'Mesero' },
  { value: 2, label: 'Cocinero' },
  { value: 3, label: 'Administrador' }
]);

const authStore = useAuthStore();
const product = reactive({
  role: 2,
  name: 'Café Buen Sabor - Grano Entero',
  sku: 'CBS-G-001',
  email: 'not-an-email',
  price: 15.50,
  shortDescription: 'Café de origen único, tostado a la perfección.',
  notes: 'Preparar con agua filtrada a 90°C.',
  socialDescription: 'Descubre el sabor único de nuestro café de origen único. Perfecto para cualquier momento del día.'
});

const errors = reactive({
  product_name: {
    message: null,
    version: 0 // Contador de versiones para forzar reactividad
  },
  product_sku: {
    message: null,
    version: 0 // Contador de versiones para forzar reactividad
  },
  product_email: {
    message: null,
    version: 0 // Contador de versiones para forzar reactividad
  },
  product_price: {
    message: null,
    version: 0 // Contador de versiones para forzar reactividad
  },
  shortDescription: {
    message: null,
    version: 0 // Contador de versiones para forzar reactividad
  },
  notes: {
    message: null,
    version: 0 // Contador de versiones para forzar reactividad
  },
  socialDescription: {
    message: null,
    version: 0 // Contador de versiones para forzar reactividad
  }
});


const verify = () => {
  console.log('Verificando datos del producto:', product);


  // Simulación de validación
  if (!product.name) {
    errors.product_name.message = 'El nombre del producto es obligatorio';
    errors.product_name.version++;
  } else {
    errors.product_name.message = null;
    errors.product_name.version++;
  }

  if (!product.sku) {
    errors.product_sku.message = 'El SKU es obligatorio';
    errors.product_sku.version++;
  } else {
    errors.product_sku.message = null;
    errors.product_sku.version++;
  }

  if (!product.email.includes('@')) {
    errors.product_email.message = 'El email debe contener un "@"';
    errors.product_email.version++;
  } else {
    errors.product_email.message = null;
    errors.product_email.version++;
  }

  if (isNaN(product.price) || product.price <= 0) {
    errors.product_price.message = 'El precio debe ser un número positivo';
    errors.product_price.version++;
  } else {
    errors.product_price.message = null;
    errors.product_price.version++;
  }

  if (!product.shortDescription) {
    errors.shortDescription.message = 'La descripción corta es obligatoria';
    errors.shortDescription.version++;
  } else {
    errors.shortDescription.message = null;
    errors.shortDescription.version++;
  }

  if (!product.notes) {
    errors.notes.message = 'Las notas son obligatorias';
    errors.notes.version++;
  } else {
    errors.notes.message = null;
    errors.notes.version++;
  }

  if (product.socialDescription.length > 280) {
    errors.socialDescription.message = 'La descripción para redes sociales no puede exceder los 280 caracteres';
    errors.socialDescription.version++;
  } else {
    errors.socialDescription.message = null;
    errors.socialDescription.version++;
  }
};
const refreshErrors = (id) => {



  if (errors[id]) {
    errors[id].message = null;
    errors[id].version++;
  } else {
    console.warn(`Error: '${id}' no existe en el objeto errors`);
  }



};

const dataInputs = reactive({
  productName: {
    id: 'product_name',
  },

  productSku: {
    id: 'product_sku',
  },
  productEmail: {
    id: 'product_email',
  },
  productPrice: {
    id: 'product_price',
  },
});

watch(() => product.name, (newValue) => {
  console.log('Nombre del producto actualizado:', newValue);
});
// En el componente padre
const errorState = reactive({
  message: null,
  version: 0 // Contador de versiones
});
const handleVerifyEmail = (value) => {
  console.log('Verificando email:', value);
  const hasError = !value.includes('@');
  errorState.message = hasError ? 'El email debe contener un "@"' : null;
  errorState.version++; // Incrementamos para forzar reactividad
};




</script>

<style scoped></style>