<script setup>
import { ref, computed } from 'vue';
import { useFormatters } from '../../../utils/formatters.js';

import MdiShieldCrown from '~icons/mdi/shield-crown';
import MdiCashRegister from '~icons/mdi/cash-register';
import MdiRoomService from '~icons/mdi/room-service';
import MdiChefHat from '~icons/mdi/chef-hat';
import MdiTabletDashboard from '~icons/mdi/tablet-dashboard';
import MdiAccountCircle from '~icons/mdi/account-circle';

// --- PROPS Y EMITS ---
const props = defineProps({
    employees: {
        type: Array,
        required: true,
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['edit', 'delete', 'change-password']);

// --- HELPERS ---
const { formatDate } = useFormatters();

// --- CONFIGURACIÓN DE LA TABLA ---
// Se crea una columna 'employee' para agrupar nombre y email, mejorando la presentación.
const columns = ref([
    { key: 'employee', label: 'Empleado' },
    { key: 'employee_cc', label: 'Cédula' },
    { key: 'rol_name', label: 'Rol' },
    { key: 'created_date', label: 'Fecha de Registro' },
    { key: 'status_name', label: 'Estado' },
    { key: 'actions', label: 'Acciones' },
]);

// --- LÓGICA DE ESTILOS Y DATOS ---

// Mapeo de estados a colores de los badges. Es más legible usar el nombre del estado.
const statusClasses = {
    'Activo': 'success',
    'Inactivo': 'neutral',
    'Eliminado': 'error',
    // Añade otros estados si los tienes
};

// Mapeo de roles a colores y un icono representativo para cada uno.
// Usar el nombre del rol como clave hace el código más semántico y fácil de mantener.
const roleConfig = {
    'Administrador': { color: 'primary', icon: MdiShieldCrown },
    'Cajero': { color: 'info', icon: MdiCashRegister },
    'Mesero': { color: 'warning', icon: MdiRoomService },
    'Cocinero': { color: 'success', icon: MdiChefHat },
    'Dispositivo': { color: 'secondary', icon: MdiTabletDashboard },
    'default': { color: 'neutral', icon: MdiAccountCircle }
};

// Función para obtener la configuración del rol de manera segura.
const getRoleConfig = (roleName) => {
    return roleConfig[roleName] || roleConfig.default;
};

// Función para obtener el color del estado de forma segura.
const getStatusClass = (statusName) => {
    return statusClasses[statusName] || 'neutral';
};

</script>

<template>
    <BaseTable :columns="columns" :data="employees" :loading="loading" track-by="id_employe" size="md" hover>
        <!--
        Se refactorizan los slots para una presentación más limpia y profesional,
        aprovechando los nuevos mapeos y el formatter de fechas.
        -->

        <!-- Slot para la celda de Empleado: combina un avatar con nombre y email. -->
        <template #cell(employee)="{ row }">
            <div class="employee-info">
                <div class="user-avatar">
                    <!-- Icono dinámico según el rol para una rápida identificación visual -->
                    <component :is="getRoleConfig(row.rol_name).icon" class="w-6 h-6 text-white" />
                </div>
                <div class="employee-details">
                    <span class="employee-name">{{ row.employe_name }}</span>
                    <span class="employee-email">{{ row.employe_email }}</span>
                </div>
            </div>
        </template>

        <!-- Slot para la celda de Rol: usa BaseBadge con color e icono dinámico. -->
        <template #cell(rol_name)="{ value }">
            <BaseBadge :color="getRoleConfig(value).color">
                <template #icon>
                    <component :is="getRoleConfig(value).icon" class="w-4 h-4" />
                </template>
                {{ value }}
            </BaseBadge>
        </template>

        <!-- Slot para la celda de Fecha: utiliza el formatter para un formato consistente. -->
        <template #cell(created_date)="{ value }">
            <span v-if="value" class="text-text-muted">
                {{ formatDate(value, 'short') }}
            </span>
            <span v-else class="text-text-muted italic">—</span>
        </template>

        <!-- Slot para la celda de Estado: renderiza un badge con punto de color. -->
        <template #cell(status_name)="{ value }">
            <BaseBadge :color="getStatusClass(value)" size="sm" class="status-badge">
                {{ value }}
            </BaseBadge>
        </template>

        <!-- Slot para la celda de Acciones: sin cambios funcionales, solo semánticos. -->
        <template #cell(actions)="{ row }">
            <div class="flex items-center justify-start gap-2">
                <BaseButton @click="emit('edit', row)" variant="success" size="icon" aria-label="Editar empleado">
                    <i-mdi-pencil class="w-5 h-5" />
                </BaseButton>

                <BaseButton @click="emit('change-password', row)" variant="secondary" size="icon"
                    aria-label="Cambiar Contraseña">
                    <i-ri-key-fill class="w-5 h-5" />
                </BaseButton>

                <BaseButton @click="emit('delete', row)" variant="danger" size="icon" aria-label="Desactivar empleado">
                    <i-mdi-trash class="w-5 h-5" />
                </BaseButton>
            </div>
        </template>

    </BaseTable>
</template>

<style scoped>
@reference "../../../style.css";

.employee-info {
    @apply flex items-center gap-4;
}

.user-avatar {
    @apply flex-shrink-0 w-11 h-11 rounded-full flex items-center justify-center;
    @apply bg-primary;
    /* Un color base genérico, el icono da el contexto */
    @apply border-2 border-white ring-1 ring-border-light;
}

.employee-details {
    @apply flex flex-col;
}

.employee-name {
    @apply font-semibold text-text;
}

.employee-email {
    @apply text-sm text-text-muted;
}
</style>