<template>
    <div>
        <BaseForm ref="formRef" :initial-values="tableData" :validation-schema="tableSchema" :is-submitting="isLoading"
            @submit="onFormSubmit">

            <template #default>
                <BaseFormRow>
                    <BaseInput name="table_number" type="number" label="Numero de mesa" placeholder="Ej: 5">
                        <template #prefix>
                            <i-mdi-person class="w-5 h-5 text-border-dark"></i-mdi-person>
                        </template>
                    </BaseInput>
                </BaseFormRow>

                <BaseFormRow>
                    <BaseSelect name="table_status" label="Estado Inicial" :options="states"
                        option-label="status_label" option-value="status_name" placeholder="Selecciona un estado">
                    </BaseSelect>
                </BaseFormRow>

            </template>
        </BaseForm>
    </div>
</template>

<script setup>
import { ref, defineExpose, onMounted } from 'vue';
import { toTypedSchema } from '@vee-validate/zod';
import { useTablesStore } from '../../../../stores/tablesStore';
import * as z from 'zod';
import { storeToRefs } from 'pinia';
import { useAlert } from '../../../../composables/useAlert';

const alert = useAlert();
const isLoading = ref(false);
const tableStore = useTablesStore();
const { states } = tableStore;

const formRef = ref();
const emits = defineEmits(['completed']);

defineExpose({
    submit: () => {
        formRef.value.submit();
    },
    isLoading
});

// Datos iniciales para el formulario de mesas
const tableData = ref({
    table_name: '',
    table_status: '', // Se usa null para que el placeholder del select se muestre
});

const validStatuses = ['INACTIVE', 'OCCUPIED', 'FREE']; // Aquí tus estados válidos

// Schema de validación adaptado para las mesas
const tableSchema = toTypedSchema(
  z.object({
    table_number: z.coerce // Intenta convertir el valor a número
            .number({ required_error: 'El número de mesa es obligatorio' })
            .positive('El número de mesa debe ser un número positivo'),

    table_status: z
      .string({ required_error: 'Debe seleccionar un estado válido' })
      .refine((val) => val !== null && validStatuses.includes(val), {
        message: 'Debe seleccionar un estado válido',
      }),
  })
);

// Función de envío adaptada para crear una mesa
const onFormSubmit = async (values) => {
    console.log('Datos de la mesa a crear:', values);
    isLoading.value = true;
    try {
        await tableStore.addTable(values);
        emits('completed');
        alert.show({
            variant: 'success',
            title: 'Mesa Creada',
            message: `La mesa #${values.table_number} ha sido creada exitosamente.`,
        });
    } catch (error) {
        console.error("Error al crear la mesa:", error);
        alert.show({
            variant: 'error',
            title: 'Error al crear la mesa',
            message: error?.message || 'Ocurrió un error al crear la mesa. Inténtalo de nuevo más tarde.',
        });
    } finally {
        isLoading.value = false;
    }
};


   
</script>

<style lang="scss" scoped></style>