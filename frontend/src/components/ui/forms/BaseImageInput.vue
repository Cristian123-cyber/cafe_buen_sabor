<!-- src/components/ui/BaseImageInput.vue -->
<template>
  <div class="form-group" :class="`variant-${variant}`">
    <label v-if="label" :for="name" class="form-label">
      {{ label }}
    </label>

    <div class="image-input-wrapper" ref="wrapperRef">
      <!-- Vista previa de la imagen -->
      <div v-if="previewUrl" class="relative inline-block">
        <div class="image-preview">
          <img :src="previewUrl" :alt="label || 'Preview'" class="preview-image" />
        </div>
        <button type="button" @click="removeImage" class="remove-image-btn" :class="`variant-${variant}`">
          <i-mdi-close class="w-4 h-4" />
        </button>
      </div>

      <!-- Área de drop/click para subir imagen -->
      <div v-else class="image-drop-zone" :class="[`variant-${variant}`, { 'is-dragover': isDragOver }]"
        @click="triggerFileInput" @dragover.prevent="handleDragOver" @dragleave.prevent="handleDragLeave"
        @drop.prevent="handleDrop">
        <div class="drop-zone-content">
          <i-mdi-cloud-upload class="upload-icon" />
          <p class="upload-text">
            Haz clic o arrastra una imagen aquí
          </p>
          <p class="upload-subtext">
            PNG, JPG hasta 5MB
          </p>
        </div>
      </div>

      <!-- Input file oculto -->
      <input ref="fileInputRef" :id="name" :name="name" type="file" accept="image/png,image/jpeg,image/jpg"
        class="hidden-file-input" @change="handleFileChange" :aria-invalid="!!errorMessage" />
    </div>

    <p v-if="errorMessage" class="form-error-text">
      {{ errorMessage }}
    </p>
    <p v-else-if="helpText" class="form-help-text">
      {{ helpText }}
    </p>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { useField } from 'vee-validate';

defineOptions({
  inheritAttrs: false
});

const props = defineProps({
  name: {
    type: String,
    required: true,
  },
  label: {
    type: String,
    default: '',
  },
  helpText: {
    type: String,
    default: '',
  },
  variant: {
    type: String,
    default: 'light',
    validator: (value) => ['light', 'dark'].includes(value)
  },
  maxSize: {
    type: Number,
    default: 5 * 1024 * 1024 // 5MB por defecto
  }
});

const wrapperRef = ref(null);
const fileInputRef = ref(null);
const previewUrl = ref(null);
const isDragOver = ref(false);

const { value, errorMessage, setValue } = useField(() => props.name, undefined, {
  validateOnMount: false,
  validateOnValueUpdate: false
});

const triggerFileInput = () => {
  fileInputRef.value?.click();
};

const handleFileChange = (event) => {
  const file = event.target.files?.[0];
  if (file) {
    processFile(file);
  }
};

const handleDrop = (event) => {
  isDragOver.value = false;
  const files = event.dataTransfer.files;
  if (files.length > 0) {
    processFile(files[0]);
  }
};

const handleDragOver = () => {
  isDragOver.value = true;
};

const handleDragLeave = () => {
  isDragOver.value = false;
};

const processFile = (file) => {
  // Validar tipo de archivo
  if (!file.type.match(/^image\/(png|jpeg|jpg)$/)) {
    setValue(null);
    return;
  }

  // Validar tamaño
  if (file.size > props.maxSize) {
    setValue(null);
    return;
  }

  // Crear preview
  const reader = new FileReader();
  reader.onload = (e) => {
    previewUrl.value = e.target.result;
  };
  reader.readAsDataURL(file);

  // Establecer el valor en el formulario
  setValue(file);
};

const removeImage = () => {
  setValue(null);
  previewUrl.value = null;
  if (fileInputRef.value) {
    fileInputRef.value.value = '';
  }
};

// Watch para manejar animación de error
watch(errorMessage, (newError) => {
  if (newError && wrapperRef.value) {
    wrapperRef.value.classList.remove('animate-shake', 'is-invalid');
    void wrapperRef.value.offsetWidth;
    wrapperRef.value.classList.add('animate-shake', 'is-invalid');
  } else {
    wrapperRef.value?.classList.remove('animate-shake', 'is-invalid');
  }
});
</script>

<style scoped>
@reference "../../../style.css";


.image-input-wrapper {
  @apply relative;
}

.hidden-file-input {
  @apply sr-only;
}

/* Preview de imagen */
.image-preview {
  @apply relative inline-block rounded-lg overflow-hidden border-2 border-gray-200;
}

.preview-image {
  @apply w-32 h-32 object-cover;
}

.remove-image-btn {
  @apply absolute -top-2 -right-2 w-6 h-6 rounded-full flex items-center justify-center cursor-pointer transition-all duration-200;
}

.remove-image-btn.variant-light {
  @apply bg-primary-light text-white hover:bg-primary-dark;
}

.remove-image-btn.variant-dark {
  @apply bg-primary-light text-white hover:bg-primary-dark;
}

/* Drop zone */
.image-drop-zone {
  @apply w-full h-32 border-2 border-dashed rounded-lg cursor-pointer transition-all duration-200 flex items-center justify-center;
}

.image-drop-zone.variant-light {
  @apply border-gray-300 bg-gray-50 hover:border-accent hover:bg-accent/5;
}

.image-drop-zone.variant-dark {
  @apply border-border-dark bg-primary hover:border-accent hover:bg-accent/5;
}

.image-drop-zone.is-dragover {
  @apply border-accent bg-accent/10;
}

.drop-zone-content {
  @apply text-center;
}

.upload-icon {
  @apply w-8 h-8 mx-auto mb-2 text-gray-400;
}

.upload-text {
  @apply text-sm font-medium;
}

.upload-subtext {
  @apply text-xs text-text-muted mt-1;
}

/* Variantes de color para texto */
.form-group.variant-light .upload-text {
  @apply text-gray-600;
}

.form-group.variant-dark .upload-text {
  @apply text-text-light;
}

/* Estado de error */
.image-input-wrapper.is-invalid .image-drop-zone {
  @apply border-error;
}

.image-input-wrapper.is-invalid .image-preview {
  @apply border-error;
}
</style>