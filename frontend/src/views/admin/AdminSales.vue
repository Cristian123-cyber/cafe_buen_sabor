

<script setup>
import { onMounted, ref, computed, watch } from 'vue';
import { useSalesStore } from '../../stores/salesS';
import api from '../../services/api';
import { useAlert } from '../../composables/useAlert';
import { useToasts } from '../../composables/useToast';

const salesStore = useSalesStore();
const { showAlert } = useAlert();
const { addToast } = useToasts();

// Filtros
const searchTerm = ref('');
const selectedStatus = ref('all');
const statusOptions = [
  { value: 'all', label: 'Todas' },
  { value: 'COMPLETED', label: 'Completadas' },
  { value: 'CANCELED', label: 'Canceladas' }
];

// Paginación local
const currentPage = ref(1);
const perPage = ref(10);
const showSale = ref(false);
const selectedSale = ref(null);

// Columnas
const columns = [
  { key: 'id_sale', label: 'ID VENTA' },
  { key: 'cashier_id', label: 'CAJERO' },
  { key: 'payment_method', label: 'MÉTODO DE PAGO' },
  { key: 'total_amount', label: 'TOTAL' },
  { key: 'sale_status', label: 'ESTADO' },
  { key: 'created_at', label: 'FECHA' },
  { key: 'acciones', label: 'ACCIONES' },
];

const filteredSales = computed(() => {
  let data = salesStore.sales || [];

  if (searchTerm.value) {
    const q = searchTerm.value.toLowerCase();
    data = data.filter(s =>
      s.id_sale?.toString().includes(q) ||
      s.cashier_id?.toString().includes(q) ||
      (s.payment_method || '').toLowerCase().includes(q)
    );
  }

  if (selectedStatus.value !== 'all') {
    data = data.filter(s => s.sale_status === selectedStatus.value);
  }

  return data;
});

const total = computed(() => filteredSales.value.length);
const paginatedSales = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  return filteredSales.value.slice(start, start + perPage.value);
});

function handlePageChange(page) { currentPage.value = page; }
function handlePerPageChange(n) { perPage.value = n; currentPage.value = 1; }

function formatCurrency(amount) {
  return new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(Number(amount || 0));
}

function formatDate(date) {
  if (!date) return '—';
  const d = new Date(date);
  if (isNaN(d.getTime())) return '—';
  return d.toLocaleString('es-AR', { year: 'numeric', month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit' });
}

function statusBadge(status) {
  switch (status) {
    case 'COMPLETED': return { label: 'Completada', cls: 'bg-green-100 text-green-800 border-green-200' };
    case 'CANCELED': return { label: 'Cancelada', cls: 'bg-red-100 text-red-800 border-red-200' };
    default: return { label: status || '—', cls: 'bg-gray-100 text-gray-700' };
  }
}

onMounted(async () => {
  try {
    // Cargar todas las ventas paginando de a 10 desde backend y acumulando
    const pageSize = 10;
    let page = 1;
    let all = [];
    while (true) {
      const { data } = await api.get(`/sales?page=${page}&limit=${pageSize}`);
      const items = data?.data ?? [];
      all = all.concat(items);
      if (!items.length || items.length < pageSize) break;
      page += 1;
    }
    salesStore.setSales(all);
  } catch (e) {
    showAlert('Error al cargar las ventas', 'error');
  }
});

watch([searchTerm, selectedStatus], () => { currentPage.value = 1; });

async function viewSale(row) {
  try {
    const { data } = await api.get(`/sales/${row.id_sale}`);
    selectedSale.value = data?.data || row;
  } catch {
    selectedSale.value = row;
  }
  showSale.value = true;
}
</script>

<template>
  <AppLayout>
    <div class="dashboard-container">
      <HeaderSection title="Gestión de Ventas" descriptionMessage="Administra la información de las ventas del local."></HeaderSection>

      <ToolsBar
        v-model:searchTerm="searchTerm"
        searchLabel="Buscar ventas"
        placeholderSearch="ID, Cajero, Método de pago..."
        v-model:selectedRole="selectedStatus"
        :roleOptions="statusOptions"
        titleRoleOptions="Estado"
        :loading="salesStore.loadingSales"
        buttonCreateText="Crear Venta"
        @create="() => {}"
      />

      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <MetricCard title="Total Ventas" :value="salesStore.salesStats.total" icon="i-mdi-chart-bar" />
        <MetricCard title="Total Recaudado" :value="formatCurrency(salesStore.salesStats.totalAmount)" icon="i-mdi-cash" />
        <MetricCard title="Completadas" :value="salesStore.salesStats.completed" icon="i-mdi-check" />
        <MetricCard title="Canceladas" :value="salesStore.salesStats.canceled" icon="i-mdi-close-circle" />
      </div>

      <BaseTable
        :columns="columns"
        :data="paginatedSales"
        trackBy="id_sale"
        :loading="salesStore.loadingSales"
        size="md"
        hover
        striped
      >
        <template #cell(payment_method)="{ value }">{{ (value === 'EFECTIVO' || value === 'CASH') ? 'Efectivo' : (value === 'TRANSFERENCIA' || value === 'TRANSFER' ? 'Transferencia' : value) }}</template>
        <template #cell(total_amount)="{ value }">{{ formatCurrency(value) }}</template>
        <template #cell(sale_status)="{ value }">
          <span class="inline-flex items-center px-2 py-1 rounded-full border text-xs font-semibold" :class="statusBadge(value).cls">
            {{ statusBadge(value).label }}
          </span>
        </template>
        <template #cell(created_at)="{ value }">{{ formatDate(value) }}</template>
        <template #cell(acciones)="{ row }">
          <div class="flex items-center gap-2">
            <BaseButton variant="success" size="icon" @click="viewSale(row)" aria-label="Ver detalles">
              <i-mdi-eye class="w-5 h-5" />
            </BaseButton>
          </div>
        </template>
      </BaseTable>

      <BasePagination
        :current-page="currentPage"
        :per-page="perPage"
        :total="total"
        :per-page-options="[5,10,25,50]"
        @page-changed="handlePageChange"
        @per-page-changed="handlePerPageChange"
      />

      <BaseModal v-model="showSale" title="Detalle de Venta" max-width="2xl">
        <div v-if="selectedSale" class="space-y-4">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold">Venta #{{ selectedSale.id_sale }}</h3>
            <span class="inline-flex items-center px-2 py-1 rounded-full border text-xs font-semibold" :class="statusBadge(selectedSale.sale_status).cls">
              {{ statusBadge(selectedSale.sale_status).label }}
            </span>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
              <div class="text-gray-500">Cajero</div>
              <div class="font-medium">{{ selectedSale.cashier_id }}</div>
            </div>
            <div>
              <div class="text-gray-500">Fecha</div>
              <div class="font-medium">{{ formatDate(selectedSale.created_at) }}</div>
            </div>
            <div>
              <div class="text-gray-500">Método de pago</div>
              <div class="font-medium">{{ (selectedSale.payment_method === 'EFECTIVO' || selectedSale.payment_method === 'CASH') ? 'Efectivo' : (selectedSale.payment_method === 'TRANSFERENCIA' || selectedSale.payment_method === 'TRANSFER' ? 'Transferencia' : selectedSale.payment_method) }}</div>
            </div>
            <div>
              <div class="text-gray-500">Total</div>
              <div class="font-medium">{{ formatCurrency(selectedSale.total_amount) }}</div>
            </div>
          </div>
        </div>
        <template #footer>
          <BaseButton variant="terciary" @click="showSale = false">Cerrar</BaseButton>
        </template>
      </BaseModal>

      <FooterDash></FooterDash>
    </div>
  </AppLayout>
</template>

<style scoped>
@reference "../../style.css";
.dashboard-container { @apply flex flex-col gap-6 lg:gap-8; }
</style>