

<script setup>
import { onMounted, ref, computed, watch } from 'vue';
import { useOrderStore } from '../../stores/orderS';
import { useOrders } from '../../composables/useOrders';
import api from '../../services/api';
import { useAlert } from '../../composables/useAlert';
import { ORDER_STATUS_LABELS, ORDER_STATUS_COLORS } from '../../utils/constants';
import OrderDetails from '../../components/orders/OrderDetails.vue';
import { useToasts } from '../../composables/useToast';

const orderStore = useOrderStore();
const { fetchAllOrders } = useOrders();
const { showAlert } = useAlert();
const { addToast } = useToasts();

// Filtros
const searchTerm = ref('');
const selectedStatus = ref(0); // 0 = Todos

// Opciones de estado para el ToolsBar
const statusOptions = [
  { value: 0, label: 'Todas' },
  { value: 1, label: ORDER_STATUS_LABELS.PENDING },
  { value: 2, label: ORDER_STATUS_LABELS.CONFIRMED || 'Confirmada' },
  { value: 4, label: ORDER_STATUS_LABELS.READY },
  { value: 5, label: ORDER_STATUS_LABELS.DELIVERED },
];

// Paginación local
const currentPage = ref(1);
const perPage = ref(10);
const showDetails = ref(false);
const selectedOrder = ref(null);

// Columnas para BaseTable
const columns = [
  { key: 'id_order', label: 'ID ORDEN' },
  { key: 'table_sessions_id_session', label: 'MESA' },
  { key: 'waiter_id', label: 'MESERO' },
  { key: 'total_amount', label: 'TOTAL' },
  { key: 'order_statuses_id_status', label: 'ESTADO' },
  { key: 'created_date', label: 'FECHA' },
  { key: 'acciones', label: 'ACCIONES' },
];

// Filtro por término/estado
const filteredOrders = computed(() => {
  let data = orderStore.orders || [];

  if (searchTerm.value) {
    const q = searchTerm.value.toLowerCase();
    data = data.filter(o =>
      o.id_order?.toString().includes(q) ||
      o.table_sessions_id_session?.toString().includes(q) ||
      (o.waiter_id ?? '').toString().includes(q)
    );
  }

  if (Number(selectedStatus.value) !== 0) {
    data = data.filter(o => Number(o.order_statuses_id_status) === Number(selectedStatus.value));
  }

  return data;
});

const total = computed(() => filteredOrders.value.length);
const paginatedOrders = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  return filteredOrders.value.slice(start, start + perPage.value);
});

function handlePageChange(page) { currentPage.value = page; }
function handlePerPageChange(n) { perPage.value = n; currentPage.value = 1; }

function formatCurrency(amount) {
  return new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(Number(amount || 0));
}

function formatDate(dateString) {
  if (!dateString) return '—';
  const d = new Date(dateString);
  if (isNaN(d.getTime())) return '—';
  return d.toLocaleString('es-AR', { year: 'numeric', month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit' });
}

function statusInfo(idStatus) {
  const id = Number(idStatus);
  switch (id) {
    case 1: return { label: ORDER_STATUS_LABELS.PENDING, cls: ORDER_STATUS_COLORS.PENDING };
    case 2: return { label: ORDER_STATUS_LABELS.CONFIRMED || 'Confirmada', cls: ORDER_STATUS_COLORS.CONFIRMED };
    case 3: return { label: ORDER_STATUS_LABELS.CANCELLED || 'Cancelada', cls: ORDER_STATUS_COLORS.CANCELLED };
    case 4: return { label: ORDER_STATUS_LABELS.READY, cls: ORDER_STATUS_COLORS.READY };
    case 5: return { label: ORDER_STATUS_LABELS.DELIVERED, cls: ORDER_STATUS_COLORS.DELIVERED };
    default: return { label: '—', cls: 'bg-gray-100 text-gray-700' };
  }
}

async function openDetails(row) {
  try {
    const { data } = await api.get(`/orders/${row.id_order}`);
    const ord = data?.data || row;
    const statusName = (() => {
      switch (Number(ord.order_statuses_id_status)) {
        case 1: return 'PENDING';
        case 2: return 'CONFIRMED';
        case 3: return 'CANCELLED';
        case 4: return 'READY';
        case 5: return 'DELIVERED';
        default: return 'UNKNOWN';
      }
    })();
    selectedOrder.value = { ...ord, order_status_name: statusName, products: ord.products || [] };
  } catch (e) {
    const statusName = (() => {
      switch (Number(row.order_statuses_id_status)) {
        case 1: return 'PENDING';
        case 2: return 'CONFIRMED';
        case 3: return 'CANCELLED';
        case 4: return 'READY';
        case 5: return 'DELIVERED';
        default: return 'UNKNOWN';
      }
    })();
    selectedOrder.value = { ...row, order_status_name: statusName, products: row.products || [] };
  }
  showDetails.value = true;
}

// eliminar duplicados que causaban errores

onMounted(async () => {
  try {
    // Cargar todas las órdenes paginando de a 10 y acumulando para la UI
    const pageSize = 10;
    let page = 1;
    let all = [];
    // Bucle de carga por páginas hasta que devuelva menos de pageSize
    // para que la tabla pueda paginar localmente 10 por página
    // sin tocar servicios ni backend
    // Nota: backend por defecto devuelve 10 por página
    while (true) {
      const { data } = await api.get(`/orders?page=${page}&limit=${pageSize}`);
      const items = data?.data ?? [];
      all = all.concat(items);
      if (!items.length || items.length < pageSize) break;
      page += 1;
    }
    orderStore.setOrders(all);
  } catch (e) {
    // Fallback al composable original si falla la carga paginada
    try { await fetchAllOrders(); } catch {}
    showAlert('Error al cargar las órdenes', 'error');
  }
});

// Reiniciar a página 1 cuando cambian filtros
watch([searchTerm, selectedStatus], () => { currentPage.value = 1; });
</script>

<template>
  <AppLayout>
    <div class="dashboard-container">
      <HeaderSection title="Gestión de Órdenes" descriptionMessage="Administra la información y estado de las órdenes del local."></HeaderSection>

      <ToolsBar
        v-model:searchTerm="searchTerm"
        searchLabel="Buscar órdenes"
        placeholderSearch="ID, Mesa, Mesero..."
        v-model:selectedRole="selectedStatus"
        :roleOptions="statusOptions"
        titleRoleOptions="Estado"
        :loading="orderStore.isLoading"
        :buttonCreateText="''"
      />

      <BaseTable
        :columns="columns"
        :data="paginatedOrders"
        trackBy="id_order"
        :loading="orderStore.isLoading"
        size="md"
        hover
        striped
      >
        <template #cell(total_amount)="{ value }">{{ formatCurrency(value) }}</template>
        <template #cell(order_statuses_id_status)="{ value }">
          <span class="inline-flex items-center px-2 py-1 rounded-full border text-xs font-semibold" :class="statusInfo(value).cls">
            {{ statusInfo(value).label }}
          </span>
        </template>
        <template #cell(created_date)="{ value }">{{ formatDate(value) }}</template>
        <template #cell(acciones)="{ row }">
          <div class="flex items-center gap-2">
            <BaseButton variant="success" size="icon" @click="openDetails(row)" aria-label="Ver detalles">
              <i-mdi-eye class="w-5 h-5" />
            </BaseButton>
          </div>
        </template>
      </BaseTable>
      <BaseModal v-model="showDetails" title="Detalle de Orden" max-width="2xl">
        <OrderDetails v-if="selectedOrder" :order="selectedOrder" />
        <template #footer>
          <BaseButton variant="terciary" @click="showDetails = false">Cerrar</BaseButton>
        </template>
      </BaseModal>

      <BasePagination
        :current-page="currentPage"
        :per-page="perPage"
        :total="total"
        :per-page-options="[5,10,25,50]"
        @page-changed="handlePageChange"
        @per-page-changed="handlePerPageChange"
      />

      <FooterDash></FooterDash>
    </div>
  </AppLayout>
</template>

<style scoped>
@reference "../../style.css";
.dashboard-container { @apply flex flex-col gap-6 lg:gap-8; }
</style>