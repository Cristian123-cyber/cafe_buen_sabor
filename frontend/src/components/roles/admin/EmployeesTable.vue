<!-- src/components/role-specific/admin/EmployeesTable.vue -->
<script setup>
import { ref, computed } from 'vue';



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

// --- CONFIGURACIÓN DE LA TABLA ---

// Definimos las columnas que queremos mostrar. La propiedad 'key' debe coincidir
// con la clave en el objeto de datos del empleado.
const columns = ref([
   
    { key: 'employe_name', label: 'Nombre' },
    { key: 'employee_cc', label: 'Cedula' },
    { key: 'employe_email', label: 'Email' },
    { key: 'rol_name', label: 'Rol' },
    { key: 'created_date', label: 'Fecha registro' },
    { key: 'status_name', label: 'Estado' },
    { key: 'actions', label: 'Acciones' },
]);

// --- LÓGICA DE ESTILOS ---

// Mapeo de estados a clases de Tailwind para los badges.
// Esto centraliza la lógica de estilos y la hace fácil de mantener.
const statusClasses = computed(() => ({
    1: 'success',
    2: 'neutral',
    3: 'error',
    // Añade otros estados si los tienes
}));


const rolesClasses = computed(() => ({
    1: 'warning',
    2: 'primary',
    3: 'info',
    4: 'secondary',
    5: 'success',
}))

</script>

<template>
    <BaseTable :columns="columns" :data="employees" :loading="loading" track-by="id_employe" size="md" hover>
        <!-- 
      Aquí es donde ocurre la magia. Personalizamos CÓMO se ve cada celda
      usando los slots que nos proporciona BaseTable.
    -->



        <!-- Slot para la celda de estado (renderiza un badge de color) -->
        <template #cell(status_name)="{ value, row }">
            <span class="flex items-center gap-2">
                <BaseBadge :color="statusClasses[row.employees_statuses_id_status]" dot />
                {{ value }}
            </span>



        </template>

        <template #cell(rol_name)="{ row, value }">

            <BaseBadge :color="rolesClasses[row.employees_rol_id_rol]">
                <template #icon><i-mdi-account class="w-4 h-4" /></template>
                {{ value }}
            </BaseBadge>
        </template>

        <template #cell(created_date)="{ value }">
            <span class="text-text">
                {{ new Date(value).toLocaleDateString('es-ES', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                }) }}
            </span>
        </template>

        <!-- Slot para la celda de acciones (botones de editar y eliminar) -->
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

.user-avatar {
    @apply flex-shrink-0 w-10 h-10 rounded-full bg-accent flex items-center justify-center;
    @apply border-2 border-white ring-1 ring-border;
}



.action-button {
    @apply p-2 rounded-full text-text-muted transition-colors duration-200;
    @apply hover:bg-accent hover:text-primary;
}
</style>