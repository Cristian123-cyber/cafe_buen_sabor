<!-- src/components/ui/BaseForm.vue -->
<template>
  <!-- Este componente 'Form' de VeeValidate es el cerebro -->
  <Form
    @submit="handleSubmit"
    :validation-schema="validationSchema"
    :initial-values="initialValues"
    :validate-on-mount="false"
    :validate-on-blur="false"
    :validate-on-input="false"
    :validate-on-change="false"
    v-slot="{ errors }"
  >
    <div class="relative" :class="{ 'form-disabled': disabled }">
      <!-- Overlay de Carga -->
      <div
        v-if="isSubmitting"
        class="absolute inset-0 z-10 flex items-center justify-center bg-white/70 dark:bg-neutral-dark/70"
        aria-live="assertive"
        role="status"
      >
        <i-svg-spinners-gooey-balls-2 class="h-5 w-5" />
      </div>

      <!-- El fieldset deshabilita todos los campos hijos a la vez -->
      <fieldset :disabled="isSubmitting || disabled" class="space-y-6">
        <header v-if="$slots.header" class="form-header">
          <slot name="header"></slot>
        </header>

        <div class="form-fields">
          <!-- Pasamos los errores combinados al slot -->
          <slot :errors="getCombinedErrors(errors)"></slot>
        </div>

        <footer v-if="$slots.actions" class="form-actions">
          <slot name="actions" :is-submitting="isSubmitting" :disabled="disabled"></slot>
        </footer>
      </fieldset>
    </div>
  </Form>
</template>

<script setup>
import { Form } from 'vee-validate';
import { computed } from 'vue';


const props = defineProps({
  initialValues: {
    type: Object,
    default: () => ({}),
  },
  isSubmitting: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  serverErrors: {
    type: Object,
    default: () => ({}),
  },
  validationSchema: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits(['submit']);

const getCombinedErrors = (veeErrors) => {
  // En un futuro, podrías combinar los errores de VeeValidate con los del servidor
  return { ...veeErrors, ...props.serverErrors };
};

const handleSubmit = (values) => {
  emit('submit', values);
};
</script>

<style scoped>


</style>