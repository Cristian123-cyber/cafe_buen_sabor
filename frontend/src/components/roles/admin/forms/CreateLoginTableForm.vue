<template>
    <div>
        <BaseForm ref="formRef" :initial-values="loginData" :validation-schema="loginSchema" :is-submitting="isLoading"
            @submit="onFormSubmit">

            <!-- El slot ya no necesita recibir 'errors'. Cada BaseInput lo gestiona internamente -->
            <template #default="{ values }">
                <BaseFormRow>
                    <BaseInput name="employe_name" label="Nombre" placeholder="">
                        <template #prefix>
                            <i-mdi-person class="w-5 h-5 text-border-dark"></i-mdi-person>
                        </template>
                    </BaseInput>
                </BaseFormRow>
                <BaseFormRow>
                    <BaseInput name="employe_email" type="email" label="Email" placeholder="">

                        <template #prefix>

                            <i-mdi-email-outline class="w-5 h-5 text-border-dark" />
                        </template>
                    </BaseInput>

                </BaseFormRow>

                <BaseFormRow>
                    <BaseInput name="password" type="password" label="Contraseña" placeholder="">
                        <template #prefix>
                            <i-mdi-lock-outline class="w-5 h-5 text-border-dark" />
                        </template>

                    </BaseInput>
                </BaseFormRow>

                <BaseFormRow >
                    <BaseSelect name="employees_statuses_id_status" label="Estado Inicial" :options="states"
                        option-label="status_name" option-value="id_status" placeholder="Selecciona un estado">
                    </BaseSelect>


                </BaseFormRow>


            </template>
        </BaseForm>
    </div>
</template>

<script setup>
import { ref, defineExpose, onMounted, watch } from 'vue';
import { toTypedSchema } from '@vee-validate/zod';

import { useEmployeStore } from '../../../../stores/employeesS';
import * as z from 'zod';
import { storeToRefs } from 'pinia';
import { useAlert } from '../../../../composables/useAlert';

const alert = useAlert();
const isLoading = ref(false);
const employeStore = useEmployeStore();

const { states } = storeToRefs(employeStore);

const formRef = ref();
const emits = defineEmits(['completed']);

defineExpose({
    submit: () => {
        formRef.value.submit();
    },
    isLoading
});

const props = defineProps({
  currentTable: { type: Object, default: null },
});

// Datos iniciales para el formulario de mesas
const loginData = ref({
    employe_name: '',
    employe_email: '',
    password: '',
    employees_statuses_id_status: 1,
    employees_rol_id_rol: 4,
    employee_cc: null,
    table_id_device: null, // ID de la mesa si se está editando
    table_number: null, // Número de la mesa si se está editando
});


// Schema de validación adaptado para las mesas
const loginSchema = toTypedSchema(
    z
        .object({
            employe_name: z
                .string({ required_error: 'El nombre es obligatorio' })
                .min(3, 'El nombre debe tener al menos 3 caracteres'),

            employe_email: z
                .string({ required_error: 'El correo es obligatorio' })
                .email('Correo inválido'),

            password: z
                .string({ required_error: 'La contraseña es obligatoria' })
                .min(6, 'La contraseña debe tener al menos 6 caracteres'),

            employees_statuses_id_status: z
                .union([
                    z.string().transform(val => (val === '' ? null : Number(val))),
                    z.number()
                ])
                .nullable()
                .refine(val => val !== null, {
                    message: 'Debe seleccionar un estado',
                }),

            employees_rol_id_rol: z
                .union([
                    z.string().transform(val => (val === '' ? null : Number(val))),
                    z.number()
                ])
                .nullable()
                .refine(val => val !== null && val === 4, {
                    message: 'Debe seleccionar un rol y debe ser de tipo Login 4',
                }),
        })
);

// Función de envío adaptada para crear una mesa
const onFormSubmit = async (values) => {
    isLoading.value = true;
    try {
        await employeStore.addEmploye(values);
        emits('completed');
        alert.show({
            variant: 'success',
            title: 'Login Creado',
            message: `La mesa #${values.table_number} ahora puede accederse mediante un dispositivo de mesa.`,
        });
    } catch (error) {

        alert.show({
            variant: 'error',
            title: 'Error al crear el login',
            message: error?.message || 'Ocurrió un error al crear el login de la mesa. Inténtalo de nuevo más tarde.',
        });
    } finally {
        isLoading.value = false;
    }
};


watch(
  () => props.currentTable,
  (newVal) => {
    if (newVal && newVal !== null) {

        loginData.value.table_id_device = newVal.id_table || null;
        loginData.value.table_number = newVal.table_number || '';

      
    }
  },
  { immediate: true } // ← Para que corra también al montar si ya viene seteado
);

onMounted(() => {

    if (states.value.length === 0) {
        employeStore.fetchStates();
    }

});



</script>

<style scoped></style>