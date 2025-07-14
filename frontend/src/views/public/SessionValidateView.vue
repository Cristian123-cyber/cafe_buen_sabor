<template>
  <div class="min-h-screen bg-surface flex items-center justify-center p-4">
    <!-- Contenedor principal -->
    <div class="w-full max-w-md">
      <!-- Card principal -->
      <div class="bg-white rounded-3xl shadow-2xl border border-amber-200 overflow-hidden backdrop-blur-sm">
        <!-- Header con logo/branding -->
        <div class="bg-gradient-to-r from-amber-400 via-amber-500 to-yellow-500 p-6 text-center relative overflow-hidden">
          <!-- Elementos decorativos -->
          <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-20 h-20 bg-white rounded-full -translate-x-10 -translate-y-10"></div>
            <div class="absolute bottom-0 right-0 w-16 h-16 bg-black rounded-full translate-x-8 translate-y-8"></div>
          </div>
          
          <div class="relative z-10">
            <div class="w-16 h-16 mx-auto mb-3 bg-black/20 rounded-full flex items-center justify-center border-2 border-white/30">
              <i-mdi-coffee class="w-8 h-8 text-white" />
            </div>
            <h1 class="text-2xl font-bold text-black tracking-wide">Café Buen Sabor</h1>
            <p class="text-black/70 text-sm mt-1 font-medium">Validando tu mesa</p>
          </div>
        </div>

        <!-- Contenido dinámico -->
        <div class="p-8 bg-gradient-to-b from-white to-amber-50/30">
          <!-- Estado de Carga -->
          <div v-if="isLoading" class="text-center">
            <div class="mb-6">
              <i-svg-spinners-pulse-rings-3 class="w-20 h-20 mx-auto text-amber-500" />
            </div>
            <h2 class="text-2xl font-bold text-black mb-3">
              Verificando tu mesa...
            </h2>
            <p class="text-neutral-700 text-base leading-relaxed">
              Estamos validando tu código QR y preparando tu experiencia de pedido.
            </p>
            <div class="mt-6">
              <div class="flex items-center justify-center space-x-2 text-sm text-neutral-600">
                <div class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></div>
                <span>Conectando con el servidor</span>
              </div>
            </div>
          </div>

          <!-- Estado de Éxito -->
          <div v-else-if="!error && !isLoading" class="text-center">
            <div class="mb-6">
              <div class="w-20 h-20 mx-auto bg-green-200 rounded-full flex items-center justify-center border-2 border-green-500">
                <i-mdi-check-circle class="w-16 h-16 text-green-500" />
              </div>
            </div>
            <h2 class="text-2xl font-bold text-black mb-3">
              ¡Perfecto!
            </h2>
            <p class="text-neutral-700 text-base leading-relaxed mb-6">
              Tu mesa ha sido validada exitosamente. Redirigiendo al menú...
            </p>
            <div class="flex items-center justify-center space-x-2 text-sm text-amber-600">
              <i-mdi-loading class="w-4 h-4 animate-spin" />
              <span>Cargando menú</span>
            </div>
          </div>

          <!-- Estado de Error -->
          <div v-else class="text-center">
            <div class="mb-6">
              <div class="w-20 h-20 mx-auto bg-gradient-to-br from-red-100 to-red-200 rounded-full flex items-center justify-center border-2 border-red-300">
                <i-mdi-alert-circle class="w-16 h-16 text-red-600" />
              </div>
            </div>
            <h2 class="text-2xl font-bold text-black mb-3">
              ¡Ups! Algo salió mal
            </h2>
            <div class="bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 rounded-lg p-4 mb-6">
              <p class="text-red-700 font-medium text-base">
                {{ error }}
              </p>
            </div>
            
            <!-- Sugerencias de solución -->
            <div class="text-left bg-gradient-to-br from-amber-50 to-yellow-50 rounded-lg p-4 mb-6 border border-amber-200">
              <h3 class="font-semibold text-black mb-2 flex items-center">
                <i-mdi-lightbulb class="w-4 h-4 mr-2 text-amber-600" />
                ¿Qué puedes hacer?
              </h3>
              <ul class="text-sm text-neutral-700 space-y-1">
                <li class="flex items-start">
                  <span class="text-amber-600 mr-2 font-bold">•</span>
                  <span>Verifica que el código QR esté vigente</span>
                </li>
                <li class="flex items-start">
                  <span class="text-amber-600 mr-2 font-bold">•</span>
                  <span>Intenta escanear el código QR nuevamente</span>
                </li>
                <li class="flex items-start">
                  <span class="text-amber-600 mr-2 font-bold">•</span>
                  <span>Solicita ayuda a nuestro personal</span>
                </li>
              </ul>
            </div>

            
          </div>
        </div>

        <!-- Footer -->
        <div class="bg-gradient-to-r from-black to-neutral-900 px-6 py-4 border-t border-amber-300">
          <div class="flex items-center justify-center text-xs text-amber-300 space-x-4">
            <span class="flex items-center">
              <i-mdi-shield-check class="w-4 h-4 mr-1" />
              Conexión segura
            </span>
            <span class="flex items-center">
              <i-mdi-wifi class="w-4 h-4 mr-1" />
              En línea
            </span>
          </div>
        </div>
      </div>

      <!-- Información adicional -->
      <div class="mt-6 text-center">
        <p class="text-sm text-text">
          ¿Necesitas ayuda? Contacta a nuestro personal
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useClientSession } from '../../composables/useClientSession.js';

const clientSession = useClientSession();
const route = useRoute();
const router = useRouter();

const isLoading = ref(true);
const error = ref(null);



onMounted(async () => {
  const qrToken = route.query.token;
  const tableId = route.query.table;

  if (!qrToken || !tableId) {
    error.value = "El código QR no es válido o está incompleto.";
    isLoading.value = false;
    return;
  }

  try {

    isLoading.value = true;
    // La acción `validateAndSetSession` llamará a la API y guardará el token
    const result = await clientSession.startSession(qrToken, tableId);

    if (result) {

      isLoading.value = false;
      await new Promise((resolve) => setTimeout(resolve, 3000));
      router.replace({ name: "ClientMenu" }); // Redirige al menú si todo fue bien

    }
  } catch (err) {
    isLoading.value = false;
    error.value = err.response?.data?.message || 'No se pudo conectar con el servidor. Revisa tu conexión a internet.';
  }
});
</script>

<style scoped>
@reference "../../style.css";

/* Animaciones personalizadas */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes shimmer {
  0% {
    background-position: -200px 0;
  }
  100% {
    background-position: calc(200px + 100%) 0;
  }
}

.animate-fade-in {
  animation: fadeIn 0.6s ease-out;
}

.shimmer {
  background: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0) 0%,
    rgba(255, 255, 255, 0.2) 20%,
    rgba(255, 255, 255, 0.5) 60%,
    rgba(255, 255, 255, 0)
  );
  background-size: 200px 100%;
  animation: shimmer 2s infinite;
}

/* Mejoras en las transiciones */
.transition-all {
  transition: all 0.3s ease;
}

/* Hover effects mejorados */
button:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(245, 158, 11, 0.3);
}

/* Efectos de glassmorphism */
.backdrop-blur-sm {
  backdrop-filter: blur(4px);
}

/* Responsive improvements */
@media (max-width: 480px) {
  .text-2xl {
    font-size: 1.5rem;
  }
  
  .w-20 {
    width: 4rem;
  }
  
  .h-20 {
    height: 4rem;
  }
  
  .p-8 {
    padding: 1.5rem;
  }
}

/* Efectos adicionales para elementos interactivos */
.card-hover:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

/* Mejoras en contraste y legibilidad */
.text-shadow {
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}
</style>