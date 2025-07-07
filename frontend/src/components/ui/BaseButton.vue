<template>
  <component :is="componentType" :class="buttonClasses" :disabled="disabled || loading"
    :aria-disabled="disabled || loading" :to="to" :href="href">
    <!-- Icono a la izquierda -->
    <span v-if="$slots['icon-left']" :class="['mr-2', { 'opacity-0': loading }]">
      <slot name="icon-left"></slot>
    </span>

    <!-- Spinner de carga -->
    <div v-if="loading" class="absolute">
      <i-svg-spinners-270-ring-with-bg class="h-7 w-7" />
    </div>

    <!-- Contenido principal del botón (texto) -->
    <span :class="{ 'opacity-0': loading }">
      <slot></slot>
    </span>

    <!-- Icono a la derecha -->
    <span v-if="$slots['icon-right']" :class="['ml-2', { 'opacity-0': loading }]">
      <slot name="icon-right"></slot>
    </span>
  </component>
</template>

<script setup>
import { computed } from 'vue';
import { RouterLink } from 'vue-router';

// 1. DEFINICIÓN DE PROPS (La API de nuestro componente)
const props = defineProps({
  // Permite renderizar como <button>, <a> o <router-link>
  as: {
    type: [String, Object],
    default: 'button',
  },
  // Variante de color
  variant: {
    type: String,
    default: 'accent',
    validator: (value) => ['primary', 'accent', 'secondary','gradient-terciary-2', 'gradient-terciary-3', 'terciary', 'danger', 'success', 'ghost', 'gradient-primary', 'gradient-secondary', 'gradient-terciary'].includes(value),
  },
  // Tamaño
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg', 'icon'].includes(value),
  },
  // Estado de carga
  loading: {
    type: Boolean,
    default: false,
  },
  // Estado deshabilitado
  disabled: {
    type: Boolean,
    default: false,
  },
  // Para enlaces <router-link>
  to: {
    type: [String, Object],
    default: null,
  },
  // Para enlaces <a>
  href: {
    type: String,
    default: null,
  },
});

// 2. LÓGICA DE CLASES (Mantener el template limpio)
const buttonClasses = computed(() => {
  return [
    'btn', // Clase base
    `btn-${props.variant}`, // Clase de variante
    `btn-${props.size}`, // Clase de tamaño
    {
      'relative': props.loading, // Añade posición relativa para el spinner
    }
  ];
});

// 3. LÓGICA DE POLIMORFISMO (El superpoder del componente)
const componentType = computed(() => {
  if (props.to) {
    return RouterLink;
  }
  if (props.href) {
    return 'a';
  }
  // Si `as` es un objeto (como un componente importado), lo usamos. Si no, default a 'button'.
  return typeof props.as === 'string' ? props.as : 'button';
});
</script>