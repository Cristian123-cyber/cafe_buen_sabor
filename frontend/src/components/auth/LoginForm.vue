<!-- src/components/auth/LoginForm.vue (REDISENADO) -->
<template>
    <div class="login-form">
        <BaseForm :initial-values="userCredentials" :validation-schema="credentialSchema" :is-submitting="isLoading"
            @submit="handleLogin">
            <!-- SLOTS CON ESTILO PROPIO -->
            <template #header>
                <div class="text-center mb-6">
                    <h2 class="form-title">Bienvenido  de nuevo</h2>
                    <p class="form-subtitle">Accede a tu panel de control</p>
                </div>
            </template>

            <template #default>

                <BaseFormRow>
                    <BaseInput name="email" label="Correo Electrónico" type="email" variant="dark">
                        <template #prefix>
                            <i-mdi-email-outline class="input-icon" />
                        </template>
                    </BaseInput>
                </BaseFormRow>


                <BaseFormRow>


                    <BaseInput name="password" label="Contraseña" type="password" variant="dark">
                        <template #prefix>
                            <i-mdi-lock-outline class="input-icon" />
                        </template>
                    </BaseInput>
                </BaseFormRow>
            </template>

            <template #actions="{ isSubmitting }">
                <BaseButton variant="accent" :disabled="isSubmitting" type="submit" size="lg" class="w-full mt-4"
                    :loading="isSubmitting">
                    <template #icon-left>
                        <i-mdi-login-variant />
                    </template>
                    Iniciar Sesión
                </BaseButton>
            </template>

            <template #footer>
                <div class="dev-options">
                    <span class="dev-options-title">Opciones de Desarrollo</span>
                    <div class="flex gap-2 justify-center flex-wrap">

                        <button @click.prevent="devLogin('Administrador')" class="dev-button">Admin</button>
                        <button @click.prevent="devLogin('Mesero')" class="dev-button">Mesero</button>
                        <button @click.prevent="devLogin('Cocinero')" class="dev-button">Cocinero</button>
                        <button @click.prevent="devLogin('Cajero')" class="dev-button">Cajero</button>
                    </div>
                </div>
            </template>


        </BaseForm>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { toTypedSchema } from '@vee-validate/zod';
import * as z from 'zod';
import { useAuthStore } from '../../stores/authS'; // <- Asegúrate que la ruta es correcta
import { useAuth } from '../../composables/useAuth';
import { useAlert } from '../../composables/useAlert'; // <- Y esta también
import { useRouter } from 'vue-router';
const { fakeLogin, login } = useAuth();


// Stores y Composables
const authStore = useAuthStore();
const alert = useAlert();

const router = useRouter();



// Estado
const isLoading = ref(false);
const userCredentials = reactive({
    email: '', // Puedes dejarlo vacío
    password: ''
});

// Schema de validación
const credentialSchema = toTypedSchema(
    z.object({
        email: z
            .string({ required_error: 'El correo es obligatorio' })
            .email('El correo no es válido'),
        password: z
            .string({ required_error: 'La contraseña es obligatoria' })
            .min(6, 'Debe tener al menos 6 caracteres'),
    })
);

// Métodos
const handleLogin = async (values) => {
    isLoading.value = true;
    try {
        await login(values);


    } catch (error) {
        alert.show({
            title: 'Error de Autenticación',
            message: error?.message || 'Credenciales incorrectas. Inténtalo de nuevo.',
            variant: 'error',
        });
    } finally {
        isLoading.value = false;
    }
};

const devLogin = (role) => {


    fakeLogin(role)

}
</script>

<style scoped>
/*
  IMPORTANTE: Esta ruta debe ser correcta desde /src/components/auth/
  hasta /src/assets/styles/.
*/
@reference "../../style.css";

/* Estilos específicos para el contenido del formulario de login */
.form-title {
    @apply text-3xl font-bold text-text-light;
}

.form-subtitle {
    @apply text-base text-text-muted mt-1;
}

/* Estilo para los iconos dentro de los inputs */
.input-icon {
    @apply w-5 h-5 text-border-dark;
}

/* Sección de desarrollo */
.dev-options {
    @apply w-full text-center border-t border-primary-light pt-6;
}

.dev-options-title {
    @apply block text-sm text-text-muted mb-3 font-semibold tracking-wider uppercase;
}

.dev-button {
    @apply px-3 py-1 text-xs text-text-muted bg-primary-light rounded-md transition-colors;
}

.dev-button:hover {
    @apply bg-accent text-primary-dark;
}
</style>