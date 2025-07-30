<script setup>
import { ref, computed, onMounted } from 'vue';
import { storeToRefs } from 'pinia';
import { useAnalyticsStore } from '../../stores/analyticsS';



const analyticStore = useAnalyticsStore();

// ESTADO Y LÓGICA DE AÑO, CENTRALIZADOS AQUÍ



// ----------------------------------------------------

//YEARLY RENEVUE VALUE
const selectedYear = ref(new Date().getFullYear());

//TOP WAITERS VALUEs

const optionsTopWaiter = [{label: 'Mes', value: 'monthly'}, {label: 'Semana', value: 'weekly'}, {label: 'Historico', value: 'all_time'}];
const selectedIndexTopWaiter = ref(0);
const currentValueTopWaiters = computed(() => {
  return optionsTopWaiter[selectedIndexTopWaiter.value].value;
});
const currentLabelTopWaiters = computed(() => {
  return optionsTopWaiter[selectedIndexTopWaiter.value].label;
});

//TOP PRODUCTS VALUEs

const optionsTopProducts = [{label: 'Mes', value: 'monthly'}, {label: 'Semana', value: 'weekly'}, {label: 'Historico', value: 'all_time'}];
const selectedIndexTopProducts = ref(0);
const currentValueTopProducts = computed(() => {
  return optionsTopProducts[selectedIndexTopProducts.value].value;
});
const currentLabelTopProducts = computed(() => {
  return optionsTopProducts[selectedIndexTopProducts.value].label;
});

//Yearly revenue actions
const prevYear = () => {

  if (selectedYear.value > 2000) {

    selectedYear.value--;
    analyticStore.fetchYearlyRevenueData({ year: selectedYear.value });

  }
};
const nextYear = () => {

  if (selectedYear.value < new Date().getFullYear()) {

    selectedYear.value++;
    analyticStore.fetchYearlyRevenueData({ year: selectedYear.value });


  }
};

//TOP WAITER actions
const prevOption = () => {

  if (selectedIndexTopWaiter.value > 0) {

    selectedIndexTopWaiter.value--;
    analyticStore.fetchTopWaiters({ period: currentValueTopWaiters });

  }
};
const nextOption = () => {

  if (selectedIndexTopWaiter.value < 2) {

    selectedIndexTopWaiter.value++;
    analyticStore.fetchTopWaiters({ period: currentValueTopWaiters });

  }
};

//TOP PRODUCTS actions
const prevOptionProducts = () => {

  if (selectedIndexTopProducts.value > 0) {

    selectedIndexTopProducts.value--;
    analyticStore.fetchTopProductsData({ period: currentValueTopProducts });

  }
};
const nextOptionProducts = () => {

  if (selectedIndexTopProducts.value < 2) {

    selectedIndexTopProducts.value++;
    analyticStore.fetchTopProductsData({ period: currentValueTopProducts });

  }
};


// Usamos storeToRefs para mantener la reactividad en las propiedades del store
const { summary, yearlyRevenue, topWaiters, productsTop, isLoading, loadingTopProducts, loadingTopWaiters, loadingYearlyRevenue, error, errorTopProducts, errorTopWaiters, errorYearlyRevenue } = storeToRefs(analyticStore);


// Lógica de placeholders (simulación de carga de datos)
const loading = ref(true);
onMounted(() => {
  // Simular una carga de datos de la API
  analyticStore.fetchDashboardData();
});

</script>

<template>
  <AppLayout>
    <div class="dashboard-container">

      <!-- =============================================== -->
      <!-- 1. HEADER DEL DASHBOARD                         -->
      <!-- =============================================== -->
      <HeaderSection>

      </HeaderSection>

      <!-- =============================================== -->
      <!-- 2. TARJETAS DE ESTADÍSTICAS RÁPIDAS             -->
      <!-- =============================================== -->


      <div v-if="isLoading" class="flex items-center justify-center h-[400px]">
        <i-svg-spinners-bars-scale-fade class="text-accent w-16 h-16 opacity-80" />
      </div>

      <div v-if="error">

        <div class="error-wrapper">
          <i-mdi-alert-circle-outline class="w-12 h-12 text-red-400" />
          <h4 class="error-title">Error al cargar datos</h4>
          <p class="error-text">No se pudieron cargar los datos del gráfico.</p>
          <p class="error-details">{{ error }}</p>
        </div>
      </div>

      <section v-if="summary" class="stats-grid">

        <MetricCard :title="summary.revenue.title" :value="summary.revenue.value"
          :trend-value="summary.revenue.trend.value" :trend-text="summary.revenue.trend.text">
          <template #icon>
            <i-mdi-wallet-outline class="w-7 h-7 text-accent" />
          </template>
        </MetricCard>

        <MetricCard :title="summary.totalOrders.title" :value="summary.totalOrders.value"
          :trend-value="summary.totalOrders.trend.value" :trend-text="summary.totalOrders.trend.text">
          <template #icon>
            <i-mdi-receipt-text-outline class="w-7 h-7 text-accent" />
          </template>
        </MetricCard>

        <MetricCard :title="summary.activeTables.title" :value="summary.activeTables.value"
          :progress="summary.activeTables.progress" :trend-text="summary.activeTables.text">
          <template #icon>
            <i-mdi-table-chair class="w-7 h-7 text-accent" />
          </template>
        </MetricCard>

        <MetricCard :title="summary.averageTicket.title" :value="summary.averageTicket.value"
          :trend-value="summary.averageTicket.trend.value" :trend-text="summary.averageTicket.trend.text">
          <template #icon>
            <i-mdi-cash-multiple class="w-7 h-7 text-accent" />
          </template>
        </MetricCard>

      </section>

      <!-- =============================================== -->
      <!-- 3. GRÁFICOS PRINCIPALES                         -->
      <!-- =============================================== -->


      <!-- =============================================== -->
      <!-- GRÁFICO PRINCIPAL DE ANCHO COMPLETO             -->
      <!-- =============================================== -->
      <section v-if="!isLoading && !error" class="main-chart-section">
        <ChartCard title="Recaudo Anual" :loading="loadingYearlyRevenue" :error="errorYearlyRevenue">
          <template #actions>
            <div class="year-selector">
              <button @click="prevYear" :disabled="selectedYear <= 2000 || loadingYearlyRevenue" class="year-button" aria-label="Año anterior">
                <i-mdi-chevron-left />
              </button>
              <span class="year-display">{{ selectedYear }}</span>
              <button @click="nextYear" :disabled="selectedYear >= new Date().getFullYear() || loadingYearlyRevenue" class="year-button"
                aria-label="Año siguiente">
                <i-mdi-chevron-right />
              </button>
            </div>
          </template>
          <!-- Pasamos los datos al componente del gráfico a través de props -->
          <MonthlyRevenueChart v-if="yearlyRevenue" :chart-data="yearlyRevenue"/>
        </ChartCard>
      </section>
      <!-- =============================================== -->
      <!-- GRID PARA GRÁFICOS SECUNDARIOS                  -->
      <!-- =============================================== -->
      <section v-if="!isLoading && !error" class="charts-grid">
        <!-- Tarjeta del Ranking de Meseros -->
        <ChartCard title="Top Meseros del Mes" :error="errorTopWaiters" :loading="loadingTopWaiters" >
          <template #actions>
            <div class="year-selector">
              <button @click="prevOption" :disabled="selectedIndexTopWaiter <= 0 || loadingTopWaiters" class="year-button" aria-label="Valor anterior">
                <i-mdi-chevron-left />
              </button>
              <span class="year-display">{{ currentLabelTopWaiters }}</span>
              <button @click="nextOption" :disabled="selectedIndexTopWaiter >= 2 || loadingTopWaiters" class="year-button"
                aria-label="Valor siguiente">
                <i-mdi-chevron-right />
              </button>
            </div>
          </template>
          <TopWaiterList v-if="topWaiters" :waiters-data="topWaiters"/>
        </ChartCard>
        <!-- Tarjeta de Top Productos -->
        <ChartCard title="Productos Más Vendidos" :error="errorTopProducts" :loading="loadingTopProducts" >
          <template #actions>
            <div class="year-selector">
              <button @click="prevOptionProducts" :disabled="selectedIndexTopProducts <= 0 || loadingTopProducts" class="year-button" aria-label="Valor anterior">
                <i-mdi-chevron-left />
              </button>
              <span class="year-display">{{ currentLabelTopProducts }}</span>
              <button @click="nextOptionProducts" :disabled="selectedIndexTopProducts >= 2 || loadingTopProducts" class="year-button"
                aria-label="Valor siguiente">
                <i-mdi-chevron-right />
              </button>
            </div>
          </template>
      
          <ProductTopBar v-if="productsTop" :products-data="productsTop"/>
        </ChartCard>
      </section>


      <ActionsPanel>

      </ActionsPanel>



      <!-- =============================================== -->
      <!-- 5. FOOTER MINIMALISTA                           -->
      <!-- =============================================== -->
      <FooterDash>

      </FooterDash>
    </div>
  </AppLayout>
</template>

<style scoped>
/* IMPORTANTE: Referencia al archivo de tema central para que @apply funcione. */
@reference "../../style.css";

/* Contenedor principal de todo el dashboard */
.dashboard-container {
  @apply flex flex-col gap-6 lg:gap-8;
}



/* 2. Grid para las tarjetas de estadísticas */
.stats-grid {
  @apply grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4;
}

.metric-card-placeholder {
  @apply bg-surface p-5 rounded-lg shadow hover:shadow-lg transition-shadow duration-300;
}



.chart-card-placeholder {
  @apply bg-surface p-6 rounded-lg shadow;
  @apply min-h-[300px] flex flex-col;
  /* Asegura altura mínima y layout de columna */
}

.chart-title {
  @apply text-lg font-bold text-text mb-4;
}

.chart-content-placeholder {
  @apply flex-1 flex flex-col items-center justify-center text-center text-text-muted;
  @apply border-2 border-dashed border-border rounded-lg;
}

.actions-panel {
  /* Usamos el tema oscuro, consistente con las MetricCards */
  @apply bg-primary p-6 rounded-xl border border-border-dark;
}

.panel-title {
  @apply text-lg font-bold text-text-light mb-4;
}

.buttons-grid {
  @apply grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3;
}

.action-button {
  @apply flex flex-col items-start text-left p-4 rounded-lg transition-all duration-300;
  @apply bg-primary-light border border-transparent;
  /* Efecto hover sutil */
  @apply hover:border-accent hover:bg-primary-dark hover:-translate-y-1;
  @apply focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-primary focus:ring-accent;
}

.action-icon {
  @apply w-8 h-8 text-accent mb-3;
}

.action-text {
  @apply font-semibold text-text-light;
}

.action-description {
  @apply text-sm text-text-muted mt-1;
}



.stats-grid {
  @apply grid gap-4 sm:grid-cols-2 lg:grid-cols-4;
}
/* Agrega esto a tus estilos (manteniendo todo lo demás igual) */
.charts-grid {
  @apply grid gap-6;
  
  /* Mobile: una columna */
  grid-template-columns: 1fr;
  
  /* Tablet: mantener una columna */
  @media (min-width: 640px) {
    grid-template-columns: 1fr;
  }
  
  /* Desktop: dos columnas con proporción 40% - 60% */
  @media (min-width: 1024px) {
    grid-template-columns: 4fr 6fr; /* Proporción 4:6 (40% - 60%) */
  }
  
  /* Pantallas extra grandes: proporción 1:2 (33% - 66%) */
  @media (min-width: 1536px) {
    grid-template-columns: 2fr 3fr;
  }
}

/* Opcional: Si necesitas que el segundo gráfico sea aún más ancho en ciertos breakpoints */
@media (min-width: 1280px) and (max-width: 1535px) {
  .charts-grid {
    grid-template-columns: 3fr 7fr; /* 30% - 70% */
  }
}
.main-chart-section {
  /* Esta sección puede ocupar el ancho completo si es necesario */
  @apply w-full;
}

.year-selector {
  @apply flex items-center bg-white border border-gray-200 rounded-lg shadow-sm px-1 py-1 gap-1;
}

.year-display {
  @apply font-semibold text-gray-700 text-sm px-3 py-1 min-w-[60px] text-center select-none;
}

.year-button {
  @apply p-2 rounded-md text-gray-500 transition-all duration-200 flex items-center justify-center w-8 h-8;
  @apply hover:bg-gray-300 hover:text-gray-700 hover:scale-105;
  @apply active:scale-95 active:bg-gray-200;
  @apply disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100;
}

.year-button:hover {
  @apply shadow-sm;
}

.error-wrapper {
  @apply flex flex-col items-center justify-center text-center w-full h-full flex-1 gap-4 p-4 max-sm:gap-3 max-sm:p-3;
}

.error-title {
  @apply text-lg font-semibold text-gray-800;
}

.error-text {
  @apply text-gray-600 font-medium;
}

.error-details {
  @apply text-sm text-text-light max-w-md bg-primary/80 px-3 py-2 rounded-md break-words;
}
</style>