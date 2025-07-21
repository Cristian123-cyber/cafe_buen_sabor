<template>
    <div>
        <BaseForm ref="formRef" :initial-values="notificationData" :validation-schema="notificationSchema"
            :is-submitting="isLoading" @submit="onFormSubmit">

            <!-- El slot ya no necesita recibir 'errors'. Cada BaseInput lo gestiona internamente -->
            <template #default>
                <BaseFormRow>
                    <BaseInput name="notificationTitle" label="Titulo" placeholder="" />
                </BaseFormRow>
                <BaseFormRow>
                    <BaseSelect name="category_id" id="category_id" label="Categoría" :options="categoryOptions" />

                </BaseFormRow>

                <BaseFormRow>
                    <BaseTextArea name="notificationMsg" label="Mensaje" placeholder="Escribe tu mensaje..."
                        :autoResize="true" :showCounter="true" :maxLength="50" />
                </BaseFormRow>
            </template>
        </BaseForm>

    </div>
</template>

<script setup>

import { ref, defineExpose } from 'vue';
import { toTypedSchema } from '@vee-validate/zod';
import * as z from 'zod';

const isLoading = ref(false);


const formRef = ref();


defineExpose({
    submit: () => {
        formRef.value.submit();

    },
    isLoading
}); // <-- Esto es clave para exponerlo al padre

const notificationData = ref({
    notificationTitle: '',
    notificationMsg: '',
    category_id: null
});

const categoryOptions = ref([
    { value: 1, label: 'Bebidas Calientes' },
    { value: 2, label: 'Bebidas Frías' },
    { value: 3, label: 'Comidas' },
    { value: 4, label: 'Postres' }
]);

const notificationSchema = toTypedSchema(
    z.object({
        notificationTitle: z.string({ required_error: 'El nombre es obligatorio' }).min(3, 'Mínimo 3 caracteres'),
        notificationMsg: z.string().min(10, "META 10 caracteres HP"),
    })
);


const onFormSubmit = async (values) => {
    isLoading.value = true;

    await new Promise(res => setTimeout(res, 4000));
    try {
        // La data ya está validada y formateada por Zod
        // await productService.create(values);
        console.log("Datos listos para enviar:", values);
        // ... mostrar notificación de éxito, cerrar modal ...
    } catch (error) {
        console.error("Error al crear producto:", error);
        // Si la API devuelve errores de campo, los puedes setear en el formulario:
        // por ejemplo: form.setErrors(error.response.data.errors)
    } finally {
        isLoading.value = false;
    }
};



</script>

<style lang="scss" scoped></style>