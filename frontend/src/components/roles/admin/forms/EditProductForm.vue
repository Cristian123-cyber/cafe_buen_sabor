<template>
    <div>
       
        <BaseForm ref="formRef" :initial-values="productData" :validation-schema="productSchema"
            :is-submitting="isLoading" @submit="onFormSubmit">

            <!-- El slot nos da acceso a los valores reactivos del formulario, lo cual es útil para la lógica condicional -->
            <template #default="{ values }">
                <BaseFormRow :cols="2">
                    <BaseInput name="product_name" label="Nombre del Producto" placeholder="Ej: Café Americano">
                        <template #prefix>
                            <i-mdi-package-variant class="w-5 h-5 text-border-dark"></i-mdi-package-variant>
                        </template>
                    </BaseInput>
                    <BaseSelect name="product_types_id_type" label="Tipo de Producto" :options="productTypes"
                        option-label="type_name" option-value="id_type" placeholder="Selecciona un tipo">
                    </BaseSelect>
                </BaseFormRow>

                <BaseFormRow :cols="2">
                    <BaseInput name="product_price" type="number" step="0.01" label="Precio" placeholder="Ej: 2.50">
                        <template #prefix>
                            <i-mdi-currency-usd class="w-5 h-5 text-border-dark" />
                        </template>
                    </BaseInput>
                    <BaseInput name="product_cost" type="number" step="0.01" label="Costo" placeholder="Ej: 0.80">
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

                <!-- Campos condicionales para productos PREPARADOS (tipo 1) -->
                <div v-if="values.product_types_id_type == 1" class="ingredients-section mt-4">
                    <BaseFormRow>
                        <BaseCheckBoxGroup name="ingredients" label="Ingredientes del Producto" :options="ingredients"
                            option-label="ingredient_name" option-value="id_ingredient"
                            help-text="Selecciona los ingredientes que componen este producto." />
                    </BaseFormRow>
                </div>

                <!-- Campos condicionales para productos NO PREPARADOS (tipo 2) -->
                <div v-if="values.product_types_id_type == 2">
                    <BaseFormRow :cols="3">
                        <BaseInput name="product_stock" type="number" label="Stock Inicial" placeholder="Ej: 100">
                            <template #prefix>
                                <i-mdi-package class="w-5 h-5 text-border-dark" />
                            </template>
                        </BaseInput>
                        <BaseInput name="low_stock_level" type="number" label="Nivel Stock Bajo" placeholder="Ej: 20">
                            <template #prefix>
                                <i-mdi-alert-circle-outline class="w-5 h-5 text-border-dark" />
                            </template>
                        </BaseInput>
                        <BaseInput name="critical_stock_level" type="number" label="Nivel Stock Crítico"
                            placeholder="Ej: 5">
                            <template #prefix>
                                <i-mdi-alert class="w-5 h-5 text-border-dark" />
                            </template>
                        </BaseInput>
                    </BaseFormRow>
                </div>

                <BaseFormRow>
                    <BaseTextArea name="product_desc" label="Descripción" placeholder="Una breve descripción del producto..."
                        :autoResize="true" :showCounter="true" :maxLength="150"></BaseTextArea>
                </BaseFormRow>

                <!-- El campo de imagen es opcional al editar. -->
                <BaseFormRow>
                    <BaseImageInput name="product_image" label="Cambiar Imagen del Producto (Opcional)"
                        help-text="Sube una nueva imagen para reemplazar la actual. Formatos: PNG, JPG (máx 5MB)." />
                </BaseFormRow>
            </template>
        </BaseForm>
    </div>
</template>

<script setup>
import { ref, defineExpose, onMounted, watch, computed } from 'vue';
import { toTypedSchema } from '@vee-validate/zod';
import { useProductStore } from '../../../../stores/productS';
import * as z from 'zod';
import { storeToRefs } from 'pinia';
import { useAlert } from '../../../../composables/useAlert';
import { useToasts } from '../../../../composables/useToast';

// --- PROPS Y EMITS ---
const props = defineProps({
    currentProduct: {
        type: Object,
        default: null
    },
});
const emits = defineEmits(['completed']);

// --- COMPOSABLES Y STORE ---
const alert = useAlert();
const { addToast } = useToasts();
const productStore = useProductStore();
const { productTypes, ingredients, categories } = storeToRefs(productStore);
const formCategories = computed(() => {
    // Si categories.value aún no está cargado o es nulo, devuelve un array vacío.
    if (!categories.value) return [];
    
    // .filter() crea un NUEVO array, sin modificar el original del store.
    return categories.value.filter(category => category.id_category !== 0);
});



// --- STATE ---
const isLoading = ref(false);
const formRef = ref(null);


// --- EXPOSE ---
// Permite al componente padre llamar al método submit del formulario
defineExpose({
    submit: () => {
        formRef.value.submit();
    },
    isLoading
});

// --- DATOS INICIALES Y CONSTANTES ---
const productData = ref({
    id_product: null,
    product_name: '',
    product_price: '',
    product_cost: '',
    product_desc: '',
    product_stock: null,
    low_stock_level: null,
    critical_stock_level: null,
    product_types_id_type: '',
    product_categories_id_category: '',
    product_image: null, // La imagen siempre empieza como null
    ingredients: [],
});

const PREPARED_PRODUCT_TYPE = 1;
const NON_PREPARED_PRODUCT_TYPE = 2;

// --- SCHEMA DE VALIDACIÓN (ZOD) ---
const productSchema = toTypedSchema(
    z.object({
        id_product: z.number().optional(), // El ID es necesario para la edición
        product_name: z.string({ required_error: 'El nombre es obligatorio' }).min(3, 'Mínimo 3 caracteres'),
        product_desc: z.string({ required_error: 'La descripción es obligatoria' }).min(10, 'Mínimo 10 caracteres'),
        product_price: z.coerce.number({ required_error: 'El precio es obligatorio' }).positive('El precio debe ser positivo'),
        product_cost: z.coerce.number({ required_error: 'El costo es obligatorio' }).positive('El costo debe ser positivo'),
        product_types_id_type: z.coerce.number({ required_error: 'Debe seleccionar un tipo' }),
        product_categories_id_category: z.coerce.number({ required_error: 'Debe seleccionar una categoría' }),

        // La imagen es opcional al editar
        product_image: z.instanceof(File).optional().nullable(),
        
        ingredients: z.array(z.coerce.number()).optional().nullable(),

        // Campos condicionales (igual que en crear)
        product_stock: z.coerce.number().optional().nullable(),
        low_stock_level: z.coerce.number().optional().nullable(),
        critical_stock_level: z.coerce.number().optional().nullable(),
    })
    .superRefine((data, ctx) => {
        if (data.product_types_id_type === PREPARED_PRODUCT_TYPE) {
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
            if (data.low_stock_level !== null && data.critical_stock_level !== null && data.low_stock_level <= data.critical_stock_level) {
                ctx.addIssue({ path: ['low_stock_level'], code: 'custom', message: 'Debe ser mayor al nivel crítico' });
            }
        }
    })
);

// --- LÓGICA DE ENVÍO ---
const onFormSubmit = async (values) => {
    isLoading.value = true;
    try {
        const { product_image, ...productPayload } = values;

        // Limpieza final del payload según el tipo de producto (idéntico a crear)
        if (productPayload.product_types_id_type === PREPARED_PRODUCT_TYPE) {
            productPayload.product_stock = null;
            productPayload.low_stock_level = null;
            productPayload.critical_stock_level = null;
        } else if (productPayload.product_types_id_type === NON_PREPARED_PRODUCT_TYPE) {
            delete productPayload.ingredients;
        }

        // Llamamos a la acción de la store para editar, pasando el ID, el payload y la imagen (si existe)
        await productStore.editProduct(
            productPayload.id_product,
            productPayload,
            product_image // Será un archivo o `null`
        );

        addToast({
            message: 'Producto actualizado exitosamente',
            title: 'Éxito',
            type: 'info',
            duration: 3000
        });
        emits('completed');

    } catch (error) {
        alert.show({
            variant: 'error',
            title: 'Error al actualizar',
            message: error.message || 'No se pudo actualizar el producto.'
        });
    } finally {
        isLoading.value = false;
    }
};

// --- WATCHER PARA ACTUALIZAR DATOS ---
// Este es el corazón de la lógica de "edición"
watch(() => props.currentProduct, (newProduct) => {
    if (newProduct) {
        console.log('BINDEANDO PRODUCTO: ', newProduct);
        productData.value = {
            id_product: newProduct.id_product || null,
            product_name: newProduct.product_name || '',
            product_desc: newProduct.product_desc || '',
            product_price: newProduct.product_price || '',
            product_cost: newProduct.product_cost || '',
            product_types_id_type: newProduct.product_types_id_type || '',
            product_categories_id_category: newProduct.product_category || '',
            
            // Los campos de stock pueden ser 0, así que verificamos que no sean nulos/indefinidos
            product_stock: newProduct.product_stock ?? null,
            low_stock_level: newProduct.low_stock_level ?? null,
            critical_stock_level: newProduct.critical_stock_level ?? null,
            
            // Los ingredientes son un array, aseguramos que sea un array vacío si no viene nada
            ingredients: newProduct.ingredients?.map(ing => ing.id) || [],

            product_image: null, // Siempre iniciamos sin imagen para no reenviarla innecesariamente
        };
       
    }
}, { immediate: true, deep: true });

// --- CICLO DE VIDA ---
onMounted(async () => {
    // Cargar datos necesarios para los selects, igual que en el formulario de crear
    if (!productTypes.value?.length) {
        await productStore.fetchTypes();
    }
    if (!categories.value?.length) {
        await productStore.fetchAllCategories();
    }
    if (!ingredients.value?.length) {
        await productStore.fetchAllIngredients();
    }

});
</script>

<style scoped>
/* Las mismas clases que en el formulario de crear para mantener consistencia */
@reference "../../../../style.css";

.ingredients-section {
    @apply border border-gray-200 rounded-lg p-4 bg-surface;
}
</style>