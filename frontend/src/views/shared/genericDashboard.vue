<template>
  <div class="p-4 sm:p-6 lg:p-8 bg-gray-50 min-h-full">
    <!-- 1. Encabezado del Dashboard -->
    <header class="mb-8">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">
            Bienvenido, {{ user?.name || 'Usuario' }}
          </h1>
          <p class="mt-1 text-md text-gray-500">
            Resumen de tu jornada como {{ user?.role_name }}.
          </p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-4">
          <!--
            Aquí irían botones de acción específicos del rol.
            Ej: <BaseButton>Crear Producto</BaseButton> para Admin
          -->
            <button @click="authStore.logout()">LOGOUT</button>
          
          
        </div>
      </div>
    </header>

    <!-- 2. Tarjetas de Métricas (Stats) -->
    <section v-if="stats.length > 0" class="mb-8">
      <h2 class="text-xl font-semibold text-gray-700 mb-4">Métricas Clave</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- 
          Iteramos sobre las estadísticas definidas dinámicamente.
          Aquí usarías tu componente `MetricCard` o `BaseCard`.
          Para este ejemplo, simulo una tarjeta directamente con Tailwind.
        -->
        <div 
          v-for="stat in stats" 
          :key="stat.title" 
          class="clean p-6 rounded-xl shadow-md border border-gray-200 flex items-center space-x-4 transform hover:-translate-y-1 transition-transform duration-200"
        >
          <div :class="stat.bgColor" class="p-3 rounded-full">
            <!-- Icono dinámico usando unplugin-icons -->
            <component :is="stat.icon" class="w-6 h-6" :class="stat.iconColor" />
          </div>
          <div>
            <p class="text-gray-500 text-sm">{{ stat.title }}</p>
            <p class="text-2xl font-bold text-gray-800">{{ stat.value }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- 3. Contenido Principal (Gráficos, Tablas, etc.) -->
    <section>
      <h2 class="text-xl font-semibold text-gray-700 mb-4">Actividad Reciente</h2>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna 1 y 2: Placeholder para un gráfico -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-md border border-gray-200">
          <h3 class="font-semibold text-gray-800 mb-4">Ventas de la Semana</h3>
          <div class="flex items-center justify-center h-64 bg-gray-100 rounded-lg">
            <p class="text-gray-400">Aquí iría un componente de Gráfico (Chart.js)</p>
          </div>
        </div>
        
        <!-- Columna 3: Placeholder para una lista de actividad -->
        <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200">
          <h3 class="font-semibold text-gray-800 mb-4">Últimos Pedidos</h3>
          <ul class="space-y-4">
            <li class="flex items-center justify-between text-sm">
              <span>Pedido #1024 (Mesa 5)</span>
              <span class="font-medium text-green-600">Entregado</span>
            </li>
            <li class="flex items-center justify-between text-sm">
              <span>Pedido #1023 (Mesa 2)</span>
              <span class="font-medium text-yellow-600">En Cocina</span>
            </li>
            <li class="flex items-center justify-between text-sm">
              <span>Pedido #1022 (Mesa 8)</span>
              <span class="font-medium text-blue-600">Listo para Recoger</span>
            </li>
            <li class="flex items-center justify-between text-sm">
              <span>Pedido #1021 (Mesa 5)</span>
              <span class="font-medium text-gray-500">Pagado</span>
            </li>
          </ul>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, markRaw } from 'vue';
import { useAuthStore } from '../../stores/authS'; // Asegúrate que la ruta sea correcta

// --- IMPORTACIONES DE COMPONENTES E ICONOS ---
// Gracias a `unplugin-vue-components` y `unplugin-icons`, no necesitas importar
// explícitamente componentes como `NotificationCenter` ni los iconos.
// Simplemente los usamos en el template.
// A continuación, los iconos que usaremos para que unplugin los detecte:
import IconCashMultiple from '~icons/mdi/cash-multiple';
import IconClipboardList from '~icons/mdi/clipboard-list-outline';
import IconAccountGroup from '~icons/mdi/account-group';
import IconTableChair from '~icons/mdi/table-chair';
import IconChefHat from '~icons/mdi/chef-hat';
import IconFoodCroissant from '~icons/mdi/food-croissant';
import IconBellRing from '~icons/mdi/bell-ring-outline';

// --- STORE DE AUTENTICACIÓN ---
// Usamos el store para obtener información del usuario actual y su rol.
const authStore = useAuthStore();
const user = computed(() => authStore.user);
const userRole = computed(() => authStore.userRole);

// --- ESTADO LOCAL DEL COMPONENTE ---
const stats = ref([]);

// --- LÓGICA DEL COMPONENTE ---

/**
 * Carga los datos y métricas del dashboard basándose en el rol del usuario.
 * En una aplicación real, esta función llamaría a acciones de stores
 * como `useAnalyticsStore.fetchDashboardMetrics()`.
 * Para pruebas, usamos datos simulados.
 */
const loadDashboardData = () => {
  switch (userRole.value) {
    case 'Administrador':
      stats.value = [
        { title: 'Ingresos de Hoy', value: '$1,250', icon: markRaw(IconCashMultiple), bgColor: 'bg-green-100', iconColor: 'text-green-600' },
        { title: 'Pedidos Totales', value: '78', icon: markRaw(IconClipboardList), bgColor: 'bg-blue-100', iconColor: 'text-blue-600' },
        { title: 'Mesas Activas', value: '12', icon: markRaw(IconTableChair), bgColor: 'bg-yellow-100', iconColor: 'text-yellow-600' },
        { title: 'Empleados Online', value: '8', icon: markRaw(IconAccountGroup), bgColor: 'bg-purple-100', iconColor: 'text-purple-600' },
      ];
      break;
    case 'Mesero':
      stats.value = [
        { title: 'Mesas Asignadas', value: '5', icon: markRaw(IconTableChair), bgColor: 'bg-blue-100', iconColor: 'text-blue-600' },
        { title: 'Pedidos por Confirmar', value: '3', icon: markRaw(IconClipboardList), bgColor: 'bg-yellow-100', iconColor: 'text-yellow-600' },
        { title: 'Listos para Recoger', value: '2', icon: markRaw(IconBellRing), bgColor: 'bg-red-100', iconColor: 'text-red-600' },
        { title: 'Pedidos Entregados', value: '15', icon: markRaw(IconFoodCroissant), bgColor: 'bg-green-100', iconColor: 'text-green-600' },
      ];
      break;
    case 'Cocinero':
      stats.value = [
        { title: 'Pedidos en Cola', value: '8', icon: markRaw(IconClipboardList), bgColor: 'bg-yellow-100', iconColor: 'text-yellow-600' },
        { title: 'En Preparación', value: '4', icon: markRaw(IconChefHat), bgColor: 'bg-blue-100', iconColor: 'text-blue-600' },
        { title: 'Pedidos Completados', value: '32', icon: markRaw(IconFoodCroissant), bgColor: 'bg-green-100', iconColor: 'text-green-600' },
      ];
      break;
    case 'Cajero':
       stats.value = [
        { title: 'Ventas del Turno', value: '$480', icon: markRaw(IconCashMultiple), bgColor: 'bg-green-100', iconColor: 'text-green-600' },
        { title: 'Mesas por Cobrar', value: '4', icon: markRaw(IconTableChair), bgColor: 'bg-yellow-100', iconColor: 'text-yellow-600' },
        { title: 'Transacciones', value: '21', icon: markRaw(IconClipboardList), bgColor: 'bg-blue-100', iconColor: 'text-blue-600' },
      ];
      break;
    default:
      stats.value = [];
  }
};

// --- LIFECYCLE HOOKS ---
onMounted(() => {
  // Cuando el componente se monta, cargamos los datos correspondientes al rol.
  loadDashboardData();
  
  // En el futuro, aquí podrías iniciar el polling de notificaciones o de datos del dashboard.
  // Ejemplo: useNotificationsStore.startPolling();
});

</script>

<style scoped>
/* Podemos añadir estilos específicos aquí si fuera necesario */
@reference "../../assets/styles/app.css";


</style>