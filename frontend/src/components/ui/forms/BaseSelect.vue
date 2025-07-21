<template>
    <div class="form-group" :class="`variant-${variant}`">
        <!-- 1. Label, consistente con los otros componentes -->
        <label v-if="label" :for="name" class="form-label">
            {{ label }}
        </label>

        <!-- 2. Wrapper para posicionar nuestro icono de flecha -->
        <div class="select-native-wrapper">

            <!-- 3. El <select> nativo -->
            <select ref="selectRef" :id="name" v-model="value" :class="selectClasses" v-bind="$attrs"
                :required="!!placeholder" @focus="isOpen = true" @blur="handleBlur">
                <!-- Opción de Placeholder (si se proporciona) -->
                <option v-if="placeholder" value="" disabled hidden>
                    {{ placeholder }}
                </option>

                <!-- Opciones generadas desde el array -->
                <option v-for="option in options" :key="getOptionValue(option)" :value="getOptionValue(option)">
                    {{ getOptionLabel(option) }}
                </option>
            </select>

            <!-- 4. Nuestro icono de flecha personalizado -->
            <div :class="['select-native-chevron']">
                <i-mynaui-chevron-down-solid
                    :class="['transition-transform duration-200', { 'rotate-180 animate-bounce-scale': isOpen }]" />
            </div>

        </div>

        <!-- 5. Texto de Error o Ayuda -->
        <p v-if="errorMessage" class="form-error-text">{{ errorMessage }}</p>
        <p v-else-if="helpText" class="form-help-text">{{ helpText }}</p>
    </div>
</template>

<script setup>

import { ref } from 'vue';
import { defineProps, defineEmits, defineOptions, computed } from 'vue';
import { useField } from 'vee-validate';

defineOptions({
    inheritAttrs: false
});

const selectRef = ref(null);
const isOpen = ref(false);

// --- PROPS ---
const props = defineProps({
    name: { type: String, required: true },
    label: { type: String, default: '' },
    options: { type: Array, default: () => [] },
    placeholder: { type: String, default: '' },
    helpText: { type: String, default: '' },

    // Props de adaptabilidad, mantenemos esta excelente característica
    optionValue: { type: String, default: 'value' },
    optionLabel: { type: String, default: 'label' },
    variant: {
        type: String,
        default: 'light',
        validator: (value) => ['light', 'dark'].includes(value)
    }
});

const selectClasses = computed(() => [
    'form-input', `variant-${props.variant}`, {
        'is-invalid': !!errorMessage.value
    }
])

const { value, errorMessage, handleChange } = useField(() => props.name);

const handleBlur = () => {
    setTimeout(() => {
        isOpen.value = false; // Cierra el select después de un breve retraso
    }, 150); // Ajusta el tiempo según sea necesario
};
// --- EMITS ---
const emit = defineEmits();

// --- MÉTODOS DE ADAPTABILIDAD ---
const getOptionValue = (option) => option[props.optionValue];
const getOptionLabel = (option) => option[props.optionLabel];

</script>