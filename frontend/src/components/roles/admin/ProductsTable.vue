<script setup>
import { ref, computed } from 'vue';
import { useFormatters } from '../../../utils/formatters.js';

import MdiCheckCircle from '~icons/mdi/check-circle'; // Relleno
import MdiAlertCircle from '~icons/mdi/alert-circle'; // Relleno
import MdiAlertOctagon from '~icons/mdi/alert-octagon'; // Relleno
import MdiPackageVariantClosed from '~icons/mdi/package-variant-closed'; // Versión rellena/cerrada

// --- PROPS Y EMITS ---
const props = defineProps({
    products: {
        type: Array,
        required: true,
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['view-details', 'edit', 'delete']);

// --- HELPERS ---
const { formatCurrency } = useFormatters();

// --- CONFIGURACIÓN DE LA TABLA ---
// Columnas clave a mostrar en la tabla. Usamos llaves personalizadas ('product', 'stock')
// para tener más control en los slots y combinar información.
const columns = ref([
    { key: 'product', label: 'Producto' },
    { key: 'category_name', label: 'Categoría' },
    { key: 'product_price', label: 'Precio Venta' },
    { key: 'product_cost', label: 'Costo' },
    { key: 'stock', label: 'Stock' },
    { key: 'actions', label: 'Acciones' },
]);

// --- LÓGICA DE ESTILOS ---

const stockStatusConfig = {
  'Optimo':  { color: 'success', icon: MdiCheckCircle },
  'Bajo':    { color: 'warning', icon: MdiAlertCircle },
  'Critico': { color: 'error',  icon: MdiAlertOctagon },
  'default': { color: 'neutral', icon: MdiPackageVariantClosed }
};

// Función para obtener la configuración del estado de forma segura.
const getStockStatusConfig = (statusName) => {
    return stockStatusConfig[statusName] || stockStatusConfig.default;
};

// Asignamos colores a las categorías para una mejor distinción visual
const categoryClasses = {
    'Cafes': 'primary',
    'Gaseosas': 'info',
    'Postres': 'warning',
    'Sandwiches': 'secondary',
    'Bebidas Frias': 'accent',
};

// Función para obtener la clase de la categoría o un color por defecto.
const getCategoryClass = (categoryName) => {
    return categoryClasses[categoryName] || 'neutral';
};

</script>

<template>
    <BaseTable :columns="columns" :data="products" :loading="loading" track-by="id_product" size="md" hover>
        <!-- 
      Usamos los slots de BaseTable para personalizar el renderizado de celdas específicas,
      lo que nos da control total sobre la presentación sin modificar el componente base.
    -->

        <!-- Slot para la celda de Producto: combina imagen y texto -->
        <template #cell(product)="{ row }">
            <div class="product-info">
                <img :src="row.product_image_url" :alt="row.product_name" class="product-image" loading="lazy" />
                <div class="product-details">
                    <span class="product-name">{{ row.product_name }}</span>
                    <!-- Truncamos la descripción para no ocupar mucho espacio -->
                    <span class="product-desc">{{ row.product_desc }}</span>
                </div>
            </div>
        </template>

        <!-- Slot para la celda de Categoría: usa un Badge de color -->
        <template #cell(category_name)="{ value }">
            <BaseBadge :color="getCategoryClass(value)">
                <template #icon><i-mdi-tag class="w-4 h-4" /></template>
                {{ value }}
            </BaseBadge>
        </template>

        <!-- Slot para formatear el Precio de Venta como moneda -->
        <template #cell(product_price)="{ value }">
            <span class="font-semibold text-primary">{{ formatCurrency(value) }}</span>
        </template>

        <!-- Slot para formatear el Costo como moneda -->
        <template #cell(product_cost)="{ value }">
            <span class="font-medium text-text">{{ formatCurrency(value) }}</span>
        </template>
        <template #cell(stock)="{ row }">
            <!-- Condicional para productos que manejan stock -->
            <div v-if="row.product_types_id_type === 2 && row.estado_stock">
                <BaseBadge :color="getStockStatusConfig(row.estado_stock).color" size="sm">

                    <template #icon>
                        <component :is="getStockStatusConfig(row.estado_stock).icon" class="w-4 h-4 flex-shrink-0" />
                    </template>
                    <span >{{ row.estado_stock }}</span>
                    <span class="stock-count">{{ row.product_stock }}</span>
                </BaseBadge>
            </div>

            <!-- Para productos preparados que no tienen stock -->
            <div v-else class="on-demand-info">
                <i-mdi-chef-hat class="w-4 h-4" />
                <span>Bajo Demanda</span>
            </div>
        </template>

        <!-- Slot para la celda de Acciones: botones para interactuar con la fila -->
        <template #cell(actions)="{ row }">
            <div class="flex items-center justify-start gap-2">
                <BaseButton @click="emit('view-details', row)" variant="secondary" size="icon"
                    aria-label="Ver detalles del producto">
                    <i-mdi-eye class="w-5 h-5" />
                </BaseButton>
                <BaseButton @click="emit('edit', row)" variant="success" size="icon" aria-label="Editar producto">
                    <i-mdi-pencil class="w-5 h-5" />
                </BaseButton>
                <BaseButton @click="emit('delete', row)" variant="danger" size="icon" aria-label="Eliminar producto">
                    <i-mdi-trash class="w-5 h-5" />
                </BaseButton>
            </div>
        </template>
    </BaseTable>
</template>

<style scoped>
@reference "../../../style.css";


.product-info {
    @apply flex items-center gap-4;
}

.product-image {
    @apply w-12 h-12 object-cover rounded-lg flex-shrink-0;
    @apply border border-border-light;
}

.product-details {
    @apply flex flex-col;
}

.product-name {
    @apply font-semibold text-text;
}

.product-desc {
    @apply text-xs text-text-muted;
    /* Truncamiento de texto a 2 líneas para mantener la tabla compacta */
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.on-demand-info {
  @apply text-text-muted italic flex items-center gap-1.5;
}




.stock-count {
  /* Un círculo o "píldora" para el número, que contrasta ligeramente */
  @apply ml-1 bg-white/25 text-xs font-bold rounded-full px-2 py-0.5 leading-none;
}
</style>