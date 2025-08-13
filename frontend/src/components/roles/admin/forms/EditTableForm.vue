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
import { ref, defineExpose, onMounted, watch } from 'vue';
import { toTypedSchema } from '@vee-validate/zod';
import { useTablesStore } from '../../../../stores/tablesStore';
import * as z from 'zod';
import { storeToRefs } from 'pinia';
import { useAlert } from '../../../../composables/useAlert';
import { useToasts } from '../../../../composables/useToast';

const alert = useAlert();
const { addToast } = useToasts();
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

const props = defineProps({
    currentTable: { type: Object, default: null }, // Prop para recibir la mesa actual a editar
});
// Datos iniciales para el formulario de mesas
const tableData = ref({
    id_table: '', // ID de la mesa si se está editando
    table_number: '',
    table_status: '', // Se usa null para que el placeholder del select se muestre
});

const validStatuses = ['INACTIVE', 'OCCUPIED', 'FREE']; // Aquí tus estados válidos

// Schema de validación adaptado para las mesas
const tableSchema = toTypedSchema(
  z.object({
    id_table: z.number().optional(),
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
    console.log('Datos de la mesa a editar:', values);
    isLoading.value = true;
    try {
        await tableStore.editTable(values.id_table, values);
        emits('completed');
        addToast({
            message: 'Mesa editada exitosamente',
            title: 'Exito',
            type: 'info',
            duration: 2000
        });
    } catch (error) {
        console.error("Error al editar la mesa:", error);
        alert.show({
            variant: 'error',
            title: 'Error al editar la mesa',
            message: error?.message || 'Ocurrió un error al editar la mesa. Inténtalo de nuevo más tarde.',
        });
    } finally {
        isLoading.value = false;
    }
};

watch(
  () => props.currentTable,
  (newVal) => {
    if (newVal && newVal !== null) {
      tableData.value = {
        id_table: newVal.id_table || null,
        table_number: newVal.table_number || '',
        table_status: newVal.table_status || ''
      };
    }
  },
  { immediate: true } // ← Para que corra también al montar si ya viene seteado
);


   
</script>

<style lang="scss" scoped></style>