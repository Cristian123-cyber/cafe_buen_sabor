<template>
    <div>
        <BaseForm ref="formRef" :initial-values="productData" :validation-schema="productSchema"
            :is-submitting="isLoading" @submit="onFormSubmit">

            <template #default="{ values }">
                <BaseFormRow :cols="2">
                    <BaseInput name="product_name" label="Nombre del Producto" placeholder="">
                        <template #prefix>
                            <i-mdi-package-variant class="w-5 h-5 text-border-dark"></i-mdi-package-variant>
                        </template>
                    </BaseInput>
                    <BaseSelect name="product_types_id_type" label="Tipo de Producto" :options="productTypes"
                        option-label="type_name" option-value="id_type" placeholder="Selecciona un tipo">
                    </BaseSelect>
                </BaseFormRow>


 

                <BaseFormRow :cols="2">
                    <BaseInput name="product_price" type="number" step="0.01" label="Precio" placeholder="">
                        <template #prefix>
                            <i-mdi-currency-usd class="w-5 h-5 text-border-dark" />
                        </template>
                    </BaseInput>
                    <BaseInput name="product_cost" type="number" step="0.01" label="Costo" placeholder="">
                        <template #prefix>
                            <i-mdi-calculator class="w-5 h-5 text-border-dark" />
                        </template>
                    </BaseInput>

                </BaseFormRow>

                <BaseFormRow>
                    <BaseSelect name="product_categories_id_category" label="Categoría" :options="categories"
                        option-label="category_name" option-value="id_category" placeholder="Selecciona una categoría">
                    </BaseSelect>
                </BaseFormRow>

                <!-- Campos solo para productos NO preparados (tipo 2) -->
                <div v-if="values.product_types_id_type == 2">
                    <BaseFormRow :cols="3">
                        <BaseInput name="product_stock" type="number" label="Stock Inicial" placeholder="">
                            <template #prefix>
                                <i-mdi-package class="w-5 h-5 text-border-dark" />
                            </template>
                        </BaseInput>
                        <BaseInput name="low_stock_level" type="number" label="Nivel Stock Bajo" placeholder="">
                            <template #prefix>
                                <i-mdi-alert-circle-outline class="w-5 h-5 text-border-dark" />
                            </template>
                        </BaseInput>
                        <BaseInput name="critical_stock_level" type="number" label="Nivel Stock Crítico" placeholder="">
                            <template #prefix>
                                <i-mdi-alert class="w-5 h-5 text-border-dark" />
                            </template>
                        </BaseInput>
                    </BaseFormRow>


                </div>

                <BaseFormRow>


                    <BaseTextArea name="product_desc" label="Descripcion" placeholder="Escribe aqui..."
                        :autoResize="true" :showCounter="true" :maxLength="50"></BaseTextArea>
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
import { ref, defineExpose, onMounted, computed, watch } from 'vue';
import { toTypedSchema } from '@vee-validate/zod';
import { useProductStore } from '../../../../stores/productS';
import * as z from 'zod';
import { storeToRefs } from 'pinia';
import { useAlert } from '../../../../composables/useAlert';

const alert = useAlert();
const isLoading = ref(false);
const productStore = useProductStore(); // Asume que tienes un store para productos

// Referencias del store
const { productTypes, ingredients, categories } = storeToRefs(productStore);

const formRef = ref();
const emits = defineEmits(['completed']);

// Variables para manejo de ingredientes
const selectedIngredients = ref([]);
const newIngredientId = ref('');

defineExpose({
    submit: () => {
        formRef.value.submit();
    },
    isLoading
});

const productData = ref({
    product_name: '',
    product_price: '',
    product_cost: '',
    product_desc: '',
    product_stock: null,
    low_stock_level: null,
    critical_stock_level: null,
    product_types_id_type: '',
    product_categories_id_category: '',
    product_image: null
});



const productSchema = toTypedSchema(
    z.object({
        product_name: z
            .string({ required_error: 'El nombre del producto es obligatorio' })
            .min(3, 'El nombre debe tener al menos 3 caracteres'),

        product_desc: z
            .string({ required_error: 'La descripción es obligatoria' })
            .min(10, 'La descripción debe tener al menos 10 caracteres'),

        product_price: z
            .union([
                z.string().transform(val => val === '' ? null : Number(val)),
                z.number()
            ])
            .refine(val => val !== null && val > 0, {
                message: 'El precio debe ser mayor a 0',
            }),

        product_cost: z
            .union([
                z.string().transform(val => val === '' ? null : Number(val)),
                z.number()
            ])
            .refine(val => val !== null && val > 0, {
                message: 'El costo debe ser mayor a 0',
            }),

        product_types_id_type: z
            .union([
                z.string().transform(val => val === '' ? null : Number(val)),
                z.number()
            ])
            .nullable()
            .refine(val => val !== null, {
                message: 'Debe seleccionar un tipo de producto',
            }),

        product_categories_id_category: z
            .union([
                z.string().transform(val => val === '' ? null : Number(val)),
                z.number()
            ])
            .nullable()
            .refine(val => val !== null, {
                message: 'Debe seleccionar una categoría',
            }),

        // Campos condicionales para productos no preparados
        product_stock: z
            .union([
                z.string().transform(val => val === '' ? null : Number(val)),
                z.number()
            ])
            .nullable()
            .optional(),

        low_stock_level: z
            .union([
                z.string().transform(val => val === '' ? null : Number(val)),
                z.number()
            ])
            .nullable()
            .optional(),

        critical_stock_level: z
            .union([
                z.string().transform(val => val === '' ? null : Number(val)),
                z.number()
            ])
            .nullable()
            .optional(),

        ingredient_statuses_id_status: z
            .union([
                z.string().transform(val => val === '' ? null : Number(val)),
                z.number()
            ])
            .nullable()
            .optional(),

        product_image: z
            .instanceof(File, { message: 'La imagen es obligatoria y debe ser un archivo válido (PNG o JPG)' })
    })
        .superRefine((data, ctx) => {
            if (data.product_types_id_type === 2) {
                if (data.product_stock === null) {
                    ctx.addIssue({
                        path: ['product_stock'],
                        code: 'custom',
                        message: 'Este campo es obligatorio',
                    });
                }
                if (data.low_stock_level === null) {
                    ctx.addIssue({
                        path: ['low_stock_level'],
                        code: 'custom',
                        message: 'Este campo es obligatorio',
                    });
                }
                if (data.critical_stock_level === null) {
                    ctx.addIssue({
                        path: ['critical_stock_level'],
                        code: 'custom',
                        message: 'Este campo es obligatorio',
                    });
                }
                if (data.ingredient_statuses_id_status === null) {
                    ctx.addIssue({
                        path: ['ingredient_statuses_id_status'],
                        code: 'custom',
                        message: 'Este campo es obligatorio',
                    });
                }
            }
        })
);




const onFormSubmit = async (values) => {
    isLoading.value = true;

    try {
        // Preparar datos según el tipo de producto
        const productPayload = {
            product_name: values.product_name,
            product_price: values.product_price,
            product_cost: values.product_cost,
            product_desc: values.product_desc,
            product_types_id_type: values.product_types_id_type,
            product_categories_id_category: values.product_categories_id_category
        };

        if (values.product_types_id_type === 1) {
            // Producto preparado
            productPayload.product_stock = null;
            productPayload.low_stock_level = null;
            productPayload.critical_stock_level = null;
            productPayload.ingredient_statuses_id_status = null;
        } else {
            // Producto no preparado
            productPayload.product_stock = values.product_stock;
            productPayload.low_stock_level = values.low_stock_level;
            productPayload.critical_stock_level = values.critical_stock_level;
            productPayload.ingredient_statuses_id_status = values.ingredient_statuses_id_status;
        }

        // Crear producto sin imagen
        const createdProduct = await productStore.addProduct(productPayload);

        // Si hay imagen, subirla por separado
        if (values.product_image && createdProduct.id) {
            await productStore.uploadProductImage(createdProduct.id, values.product_image);
        }

        emits('completed');
        alert.show({
            variant: 'success',
            title: 'Producto creado',
            message: `El producto ${values.product_name} ha sido creado exitosamente.`,
        });

    } catch (error) {
        console.error("Error al crear producto:", error);

        alert.show({
            variant: 'error',
            title: 'Error al crear producto',
            message: error?.message || 'Ocurrió un error al crear el producto. Inténtalo de nuevo más tarde.',
        });
    } finally {
        isLoading.value = false;
    }
};

onMounted(async () => {
    // Cargar datos necesarios del store
    if (productTypes.value?.length === 0 || productTypes.value === null) {
        await productStore.fetchTypes();
    }


    if (categories.value?.length === 0 || categories.value === null) {
        await productStore.fetchAllCategories();
    }
});
</script>

<style scoped>
@reference "../../../../style.css";

.ingredients-section {
    @apply border border-gray-200 rounded-lg p-4 bg-gray-50;
}

.selected-ingredients {
    @apply space-y-3;
}

.ingredient-item {
    @apply flex items-center gap-3 p-3 bg-white rounded border;
}

.ingredient-name {
    @apply flex-1 font-medium text-gray-700;
}

.remove-ingredient-btn {
    @apply p-1 text-error hover:bg-error-light rounded transition-colors duration-200;
}
</style>