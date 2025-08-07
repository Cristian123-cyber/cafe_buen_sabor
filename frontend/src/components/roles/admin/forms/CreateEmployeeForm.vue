<template>
    <div>
        <BaseForm ref="formRef" :initial-values="employeeData" :validation-schema="employeSchema"
            :is-submitting="isLoading" @submit="onFormSubmit">

            <!-- El slot ya no necesita recibir 'errors'. Cada BaseInput lo gestiona internamente -->
            <template #default="{ values }">
                <BaseFormRow :cols="2">
                    <BaseInput name="employe_name" label="Nombre" placeholder="">
                        <template #prefix>
                            <i-mdi-person class="w-5 h-5 text-border-dark"></i-mdi-person>
                        </template>
                    </BaseInput>
                    <BaseInput name="employee_cc" type="number" label="Cedula" placeholder="">
                        <template #prefix>
                            <i-heroicons-identification-16-solid
                                class="w-5 h-5 text-border-dark"></i-heroicons-identification-16-solid>
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

                <BaseFormRow :cols="2">

                    <BaseSelect name="employees_rol_id_rol" label="Rol" :options="roles" option-label="rol_name"
                        option-value="id_rol" placeholder="Selecciona un rol">
                    </BaseSelect>
                    <BaseSelect name="employees_statuses_id_status" label="Estado Inicial" :options="states"
                        option-label="status_name" option-value="id_status" placeholder="Selecciona un estado">
                    </BaseSelect>


                </BaseFormRow>
                <!-- Input de imagen -->
                <BaseFormRow>
                    <BaseImageInput name="product_image" label="Imagen del Producto"
                        help-text="Sube una imagen en formato PNG o JPG (máximo 5MB)" />
                </BaseFormRow>


            </template>
        </BaseForm>

    </div>
</template>

<script setup>

import { ref, defineExpose, onMounted, computed } from 'vue';
import { toTypedSchema } from '@vee-validate/zod';
import { useEmployeStore } from '../../../../stores/employeesS';
import * as z from 'zod';
import { storeToRefs } from 'pinia';
import { useAlert } from '../../../../composables/useAlert';
import { useToasts } from '../../../../composables/useToast';
const alert = useAlert();
const isLoading = ref(false);
const employeStore = useEmployeStore();

const { addToast } = useToasts();

const { roles, states } = storeToRefs(employeStore);


const formRef = ref();

const emits = defineEmits(['completed']);


defineExpose({
    submit: () => {
        formRef.value.submit();

    },
    isLoading
}); // <-- Esto es clave para exponerlo al padre

const employeeData = ref({
    employe_name: '',
    employe_email: '',
    password: '',
    employees_statuses_id_status: 1,
    employees_rol_id_rol: '',
    employee_cc: '',
});


const employeSchema = toTypedSchema(
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
                .refine(val => val !== null, {
                    message: 'Debe seleccionar un rol',
                }),

            employee_cc: z
                .union([
                    z
                        .string()
                        .min(6, 'Debe tener al menos 6 caracteres')
                        .regex(/^\d+$/, 'Solo se permiten números'),
                    z
                        .number()
                        .refine(val => String(val).length >= 6, 'Debe tener al menos 6 dígitos')
                ])
                .transform(val => String(val)),
        })
);

const onFormSubmit = async (values) => {
    isLoading.value = true;

    try {
        await employeStore.addEmploye(values);
        emits('completed'); // Emitir el evento de completado
        addToast({
            message: 'Empleado creado exitosamente',
            title: 'Exito',
            type: 'info',
            duration: 2000
        });

    } catch (error) {
        console.error("Error al crear empleado:", error);

        alert.show({
            variant: 'error',
            title: 'Error al crear empleado',
            message: error?.message || 'Ocurrió un error al crear el empleado. Inténtalo de nuevo más tarde.',
        });
        // Si la API devuelve errores de campo, los puedes setear en el formulario:
        // por ejemplo: form.setErrors(error.response.data.errors)
    } finally {
        isLoading.value = false;
    }
};



onMounted(async () => {

    if (roles.value?.length === 0 || roles.value === null) {
        await employeStore.fetchRoles();
    }
    if (states.value?.length === 0 || states.value === null) {
        await employeStore.fetchStates();
    }

})



</script>

<style scoped></style>