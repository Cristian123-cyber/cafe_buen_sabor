<template>
    <div>
        <!-- Usamos BaseForm para abstraer la lógica de VeeValidate -->
        <BaseForm
            ref="formRef"
            :initial-values="formData"
            :validation-schema="passwordSchema"
            :is-submitting="isLoading"
            @submit="onFormSubmit"
        >
            <template #header>
                
                <BaseBadge color="primary">
                    {{ employeName }}

                </BaseBadge>
            </template>
            <template #default>
                <BaseFormRow>
                    
                    <!-- Campo para la nueva contraseña -->
                    <BaseInput
                        name="password"
                        type="password"
                        label="Nueva Contraseña"
                        placeholder="••••••••"
                    >
                        <template #prefix>
                            <i-mdi-lock-outline class="w-5 h-5 text-border-dark" />
                        </template>
                    </BaseInput>
                </BaseFormRow>

                <BaseFormRow>
                    <!-- Campo para confirmar la nueva contraseña -->
                    <BaseInput
                        name="password_confirmation"
                        type="password"
                        label="Confirmar Contraseña"
                        placeholder="••••••••"
                    >
                        <template #prefix>
                            <i-mdi-lock-check-outline class="w-5 h-5 text-border-dark" />
                        </template>
                    </BaseInput>
                </BaseFormRow>
            </template>
        </BaseForm>
    </div>
</template>

<script setup>
import { ref, defineExpose, watch } from 'vue';
import { toTypedSchema } from '@vee-validate/zod';
import * as z from 'zod';
import { useAlert } from '../../../../composables/useAlert';
import { useToasts } from '../../../../composables/useToast';
// Asumimos que tienes un store para gestionar usuarios, si no, puedes adaptarlo a tu authStore.
import { useEmployeStore } from '../../../../stores/employeesS';

// --- PROPS Y EMITS ---
const props = defineProps({
    employeeId: {
        type: [Number, String], // Aceptamos Number o String para flexibilidad
        required: true,
    },
    employeName: {
        type: String
    }
});
const emits = defineEmits(['completed']);

// --- COMPOSABLES Y STORE ---
const alert = useAlert();
const { addToast } = useToasts();
const employeStore = useEmployeStore(); // Instancia del store de usuarios

// --- STATE ---
const isLoading = ref(false);
const formRef = ref(null);

// Datos iniciales del formulario. El ID se llenará desde la prop.
const formData = ref({
    id_user: null,
    password: '',
    password_confirmation: '',
});

// --- EXPOSE ---
// Exponemos el método submit para que el componente padre (ej. un modal) pueda invocarlo.
defineExpose({
    submit: () => {
        formRef.value.submit();
    },
    isLoading,
});

// --- SCHEMA DE VALIDACIÓN (ZOD) ---
const passwordSchema = toTypedSchema(
    z.object({
        // Incluimos el id para que esté en los 'values' del formulario, pero no se muestra
        id_user: z.number().positive('ID de usuario inválido'),

        password: z
            .string({ required_error: 'La contraseña es obligatoria' })
            .min(6, 'La contraseña debe tener al menos 6 caracteres'),
        
        password_confirmation: z
            .string({ required_error: 'Debe confirmar la contraseña' }),
    })
    // Usamos `refine` para la validación cruzada entre campos.
    // Es la forma recomendada por Zod para comparar dos campos.
    .refine((data) => data.password === data.password_confirmation, {
        message: "Las contraseñas no coinciden",
        // `path` le dice a VeeValidate en qué campo mostrar este error.
        path: ["password_confirmation"], 
    })
);

// --- LÓGICA DE ENVÍO ---
const onFormSubmit = async (values) => {
    isLoading.value = true;
    try {
        // Creamos un payload limpio para la API. No necesitamos enviar `password_confirmation`.
        const payload = {
            id_user: values.id_user,
            new_password: values.password,
            confirm_password: values.password_confirmation
        };

        // Llamamos a la acción del store correspondiente
        await employeStore.changePassword(payload.id_user, payload);

        addToast({
            title: 'Éxito',
            message: 'La contraseña se ha actualizado correctamente.',
            type: 'success',
            duration: 4000,
        });
        
        // Notificamos al padre que la operación se completó
        emits('completed');

    } catch (error) {
        alert.show({
            variant: 'error',
            title: 'Error al cambiar la contraseña',
            message: error.message || 'Ocurrió un error inesperado. Inténtalo de nuevo.',
        });
    } finally {
        isLoading.value = false;
    }
};

// --- WATCHER PARA SINCRONIZAR PROPS ---
// Este watcher se asegura de que el `id_user` en `formData` esté siempre
// sincronizado con la prop `employeeId` que recibe el componente.
watch(
    () => props.employeeId,
    (newId) => {
        if (newId) {
            formData.value.id_user = Number(newId); // Aseguramos que sea un número
        }
    },
    { immediate: true } // `immediate` hace que se ejecute en cuanto el componente se monta
);

</script>

<style scoped>
/* Si necesitas estilos específicos, puedes añadirlos aquí */
</style>