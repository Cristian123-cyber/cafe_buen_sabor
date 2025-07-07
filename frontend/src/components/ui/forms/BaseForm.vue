<!-- src/components/ui/BaseForm.vue (REDISENADO Y SIMPLIFICADO) -->
<template>
  <!-- El componente 'Form' de VeeValidate sigue siendo el cerebro -->
  <Form @submit="handleSubmit" :validation-schema="validationSchema" :initial-values="initialValues"
    v-slot="{ errors }">
    <div class="relative" :class="{ 'form-disabled': disabled }">
      <!-- Overlay de Carga se mantiene, es una excelente funcionalidad -->
      <div v-if="isSubmitting"
        class="fixed inset-0 z-50 flex items-center rounded-xl justify-center bg-white/30 dark:bg-neutral-900/50 rouded"
        aria-live="assertive" role="status">
       
      </div>

      <!-- El fieldset es crucial para deshabilitar todos los campos a la vez -->
      <fieldset :disabled="isSubmitting || disabled" class="flex flex-col gap-y-6">

        <!-- Slot para el header del formulario -->
        <div v-if="$slots.header">
          <slot name="header"></slot>
        </div>

        <!-- Slot por defecto para los campos del formulario -->
        <div class="space-y-5">
          <slot :errors="getCombinedErrors(errors)"></slot>
        </div>

        <!-- Slot para los botones de acción -->
        <div v-if="$slots.actions">
          <slot name="actions" :is-submitting="isSubmitting" :disabled="disabled"></slot>
        </div>

        <!-- Slot opcional para contenido extra (ej. links) -->
        <div v-if="$slots.footer" class="pt-4">
          <slot name="footer"></slot>
        </div>

      </fieldset>
    </div>
  </Form>
</template>

<script setup>
import { Form } from 'vee-validate';

const props = defineProps({
  initialValues: { type: Object, default: () => ({}) },
  isSubmitting: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  serverErrors: { type: Object, default: () => ({}) },
  validationSchema: { type: Object, default: null },
});

const emit = defineEmits(['submit']);

const getCombinedErrors = (veeErrors) => {
  return { ...veeErrors, ...props.serverErrors };
};

const handleSubmit = (values) => {
  emit('submit', values);
};
</script>

<style scoped>
/*
  IMPORTANTE: Esta ruta debe ser correcta desde /src/components/ui/
  hasta /src/assets/styles/.
*/
@reference "../../../style.css";

.form-disabled {
  @apply opacity-60 cursor-not-allowed;
}
</style>