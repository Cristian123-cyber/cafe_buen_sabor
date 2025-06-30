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
        <button @click="authStore.fakeLogin('Device')" class="bg-blue-500 text-white px-4 py-2 rounded">Entrar como
          Device</button>
        <button @click="authStore.fakeLogin('Mesero')" class="bg-blue-500 text-white px-4 py-2 rounded">Entrar como
          Mesero</button>
        <button @click="authStore.fakeLogin('Cocinero')" class="bg-green-500 text-white px-4 py-2 rounded">Entrar como
          Cocinero</button>
        <button @click="authStore.fakeLogin('Administrador')" class="bg-gray-800 text-white px-4 py-2 rounded">Entrar
          como Admin</button>
      </div>
    </div>

    <div class="p-8 max-w-lg mx-auto bg-surface space-y-8">

      <!-- 1. Uso Básico con v-model -->
      <div>
        <h3 class="font-bold mb-2">Uso Básico</h3>
        <BaseInput :error="errors.product_name" v-model="product.name" :id="dataInputs.productName.id"
          @refresh="refreshErrors" label="Nombre del Producto" placeholder="Ej: Café de Origen Único" />
      </div>

      <!-- 2. Con Texto de Ayuda -->
      <div>
        <h3 class="font-bold mb-2">Con Texto de Ayuda</h3>
        <BaseInput :error="errors.product_sku" v-model="product.sku" :id="dataInputs.productSku.id"
          @refresh="refreshErrors" label="SKU" help-text="El código único de producto no se puede cambiar después." />
      </div>

      <!-- 3. Con Estado de Error -->
      <div>
        <h3 class="font-bold mb-2">Con Estado de Error</h3>
        <BaseInput v-model="product.email" label="Email de Notificación" :id="dataInputs.productEmail.id"
          @refresh="refreshErrors" type="email" :error="errors.product_email">
          <template #prefix>
            <i-mdi-currency-usd class="h-5 w-5" />
          </template>
        </BaseInput>
      </div>

      <!-- 4. PREMIUM: Con Adornos (Prefix y Suffix) -->
      <div>
        <h3 class="font-bold mb-2">Con Adornos</h3>
        <BaseInput v-model="product.price" :id="dataInputs.productPrice.id" @refresh="refreshErrors"
          :error="errors.product_price" label="Precio" type="number" placeholder="0.00">
          <template #prefix>
            <i-mdi-currency-usd class="h-5 w-5" />
          </template>
          <template #suffix>
            <span class="text-sm font-semibold">USD</span>
          </template>
        </BaseInput>
      </div>


      <!-- EJEMPLO 1: Textarea básico para una descripción simple -->
      <div>
        <h3 class="font-semibold text-lg mb-2">Descripción Corta</h3>
        <BaseTextArea id="shortDescription" label="Resumen del producto" v-model="product.shortDescription"
          :error="errors.shortDescription" @refresh="refreshErrors"
          help-text="Un resumen breve para la vista de lista." />
      </div>

      <!-- EJEMPLO 2: Textarea con auto-redimensionamiento para notas -->
      <div>
        <h3 class="font-semibold text-lg mb-2">Notas de Preparación</h3>
        <BaseTextArea id="notes" label="Notas internas" v-model="product.notes" :auto-resize="true"
          :error="errors.notes" @refresh="refreshErrors"
          help-text="El campo crecerá automáticamente con el contenido." />
      </div>

      <!-- EJEMPLO 3: Textarea con contador de caracteres -->
      <div>
        <h3 class="font-semibold text-lg mb-2">Descripción para Redes Sociales</h3>
        <BaseTextArea id="socialDescription" label="Texto para publicación" v-model="product.socialDescription"
          :show-counter="true" :max-length="280" :error="errors.socialDescription" @refresh="refreshErrors"
          help-text="Optimizado para Twitter/X." />
      </div>

      <div>
        <h3 class="font-semibold text-lg mb-2">Asignar Rol (Básico)</h3>

        <BaseSelect id="roleSelect" v-model="product.role" :options="options" label="Selecciona un rol"
          placeholder="Elige un rol" :error="errors.role" @refresh="refreshErrors" optionValue="value"
          optionLabel="label" />


      </div>

     



      <BaseButton variant="gradient-terciary-3" @click="verify">
        <template #icon-left>
          <i-mdi-check class="h-5 w-5" />
        </template>

        Validar

      </BaseButton>

    </div>
    <div class="p-8 max-w-2xl mx-auto bg-surface rounded-lg shadow-lg">
      <h1 class="text-2xl font-bold mb-8">Configuración</h1>

      <div class="space-y-10">

        <!-- EJEMPLO DE USO DE BaseRadioGroup -->
        <BaseRadio name="delivery-method" label="Método de Entrega Preferido" v-model="settings.delivery"
          :options="deliveryOptions" direction="vertical" />

        <hr />

        <!-- EJEMPLO DE USO DE BaseCheckbox (con lógica corregida para 'Seleccionar Todo') -->
        <div>
          <h3 class="form-label mb-2 font-semibold">Notificaciones a Activar</h3>
          <div class="space-y-3">
            <BaseCheckBox v-model="selectAllNotifications" :indeterminate="isIndeterminate">
              <strong>Todas las Notificaciones</strong>
            </BaseCheckBox>
            <div class="pl-8 space-y-3 border-l-2 ml-2 border-border-light">
              <BaseCheckBox v-model="notifications.orderConfirmed">Pedido Confirmado</BaseCheckBox>
              <BaseCheckBox v-model="notifications.orderReady">Pedido Listo para Recoger</BaseCheckBox>
              <BaseCheckBox v-model="notifications.orderDelivered">Pedido Entregado</BaseCheckBox>
            </div>
          </div>
        </div>

      </div>
    </div>


  </div>


</template>

<script setup>
import { reactive, ref, watch, computed } from 'vue';
import { useAuthStore } from '../../stores/authS';


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

<style lang="scss" scoped></style>