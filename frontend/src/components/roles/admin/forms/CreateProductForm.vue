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
                    <BaseSelect name="product_categories_id_category" label="Categoría" :options="formCategories"
                        option-label="category_name" option-value="id_category" placeholder="Selecciona una categoría">
                    </BaseSelect>
                </BaseFormRow>

                <div v-if="values.product_types_id_type == 1" class="ingredients-section mt-4">
                    <BaseFormRow>
                        <BaseCheckBoxGroup name="ingredients" label="Ingredientes del Producto" :options="ingredients"
                            option-label="ingredient_name" option-value="id_ingredient"
                            help-text="Selecciona los ingredientes que componen este producto." />
                    </BaseFormRow>
                </div>

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
import { useToasts } from '../../../../composables/useToast';


const alert = useAlert();
const { addToast } = useToasts();

const isLoading = ref(false);
const productStore = useProductStore(); // Asume que tienes un store para productos

// Referencias del store
const { productTypes, ingredients, categories } = storeToRefs(productStore);
const formCategories = computed(() => {
    // Si categories.value aún no está cargado o es nulo, devuelve un array vacío.
    if (!categories.value) return [];
    
    // .filter() crea un NUEVO array, sin modificar el original del store.
    return categories.value.filter(category => category.id_category !== 0);
});

const formRef = ref();
const emits = defineEmits(['completed']);


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
    product_image: null,
    ingredients: [],
});

const PREPARED_PRODUCT_TYPE = 1;
const NON_PREPARED_PRODUCT_TYPE = 2;



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

        ingredients: z
            .array(
                z.union([
                    z.string().transform(val => val === '' ? null : Number(val)),
                    z.number()
                ])
            )
            .optional()
            .nullable(),

        product_image: z
            .instanceof(File, { message: 'La imagen es obligatoria y debe ser un archivo válido (PNG o JPG)' })
    })
        .superRefine((data, ctx) => {
            if (data.product_types_id_type === PREPARED_PRODUCT_TYPE) {
                console.log(data);
                if (!data.ingredients || data.ingredients.length === 0) {
                    ctx.addIssue({
                        code: z.ZodIssueCode.custom,
                        message: 'Debe seleccionar al menos un ingrediente',
                        path: ['ingredients'],
                    });
                }
            }

            if (data.product_types_id_type === NON_PREPARED_PRODUCT_TYPE) {
                if (data.product_stock === null || data.product_stock < 0) {
                    ctx.addIssue({ path: ['product_stock'], code: 'custom', message: 'El stock es obligatorio y debe ser >= 0' });
                }
                if (data.low_stock_level === null || data.low_stock_level < 0) {
                    ctx.addIssue({ path: ['low_stock_level'], code: 'custom', message: 'El nivel bajo es obligatorio y debe ser >= 0' });
                }
                if (data.critical_stock_level === null || data.critical_stock_level < 0) {
                    ctx.addIssue({ path: ['critical_stock_level'], code: 'custom', message: 'El nivel crítico es obligatorio y debe ser >= 0' });
                }
                // Validar que critico < bajo < stock
                if (data.low_stock_level <= data.critical_stock_level) {
                    ctx.addIssue({ path: ['low_stock_level'], code: 'custom', message: 'Debe ser mayor al nivel crítico' });
                }

            }
        })

);




const onFormSubmit = async (values) => {
    isLoading.value = true;
    try {
        console.log('VALUES: ', values);
        // 1. Preparar el payload JSON
        // Vee-validate nos da todos los valores, filtramos los que no van a la API de creación.
        const { product_image, ...productPayload } = values;

        // Limpieza final del payload según el tipo de producto
        if (productPayload.product_types_id_type === PREPARED_PRODUCT_TYPE) {
            // Para productos preparados, enviamos el array de IDs de ingredientes
            // y nos aseguramos de que los campos de stock sean nulos.
            productPayload.product_stock = null;
            productPayload.low_stock_level = null;
            productPayload.critical_stock_level = null;
            // La API debe esperar un array de IDs en el campo 'ingredients'
        } else if (productPayload.product_types_id_type === NON_PREPARED_PRODUCT_TYPE) {
            // Para no preparados, eliminamos el campo 'ingredients'.
            delete productPayload.ingredients;
        }

        // 2. Enviar el payload JSON para crear el producto
        await productStore.addProduct(productPayload, product_image);


        addToast({
            message: 'Mesa creada exitosamente',
            title: 'Exito',
            type: 'info',
            duration: 2000
        });
        emits('completed');

    } catch (error) {
        console.log('ERROR CREARNDO')
        alert.show({ variant: 'error', title: 'Error', message: error.message || 'No se pudo crear el producto.' });
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

    if (ingredients.value?.length === 0 || ingredients.value === null) {
        await productStore.fetchAllIngredients();
    }
});
</script>

<style scoped>
@reference "../../../../style.css";

.ingredients-section {
    @apply border border-gray-200 rounded-lg p-4 bg-surface;
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