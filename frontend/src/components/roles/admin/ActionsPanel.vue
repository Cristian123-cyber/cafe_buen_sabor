<!-- src/components/admin/ActionsPanel.vue -->
<script setup>
import { ref } from 'vue';
import { useToasts } from '../../../composables/useToast';
import { useAlert } from '../../../composables/useAlert';

const alert = useAlert();

const { addToast } = useToasts();

const formComponent = ref(null); // ref para el formulario hijo

const loadingAction = ref(null); // 'renew' | 'report' | 'announce' | null

const showAnnounceModal = ref(false);
const announcementMessage = ref('');

const handleRenewQRs = async () => {

    const isConfirmed = await alert.show({
        variant: 'warning',
        title: '¿Confirmar Renovacion?',
        message: 'Todos los QRs de todas las mesas seran renovados. ¿Deseas continuar?',
        confirmButtonText: 'Sí, renovar',
        cancelButtonText: 'Cancelar',
    });


    if (isConfirmed) {
        loadingAction.value = 'renew';
        try {
            // await adminService.renewAllQRs();
            await new Promise(res => setTimeout(res, 1000));



             alert.show({
                variant: 'success',
                title: 'QRs Renovados!',
                message: 'Todos los QRs han sido renovados con éxito.'
            });

        } catch (error) {
            await alert.show({
                variant: 'error',
                title: 'Error al renovar QRs ' + e,
                message: 'No se pudo completar la accion. Por favor, intenta de nuevo.'
            });
        } finally {
            loadingAction.value = null;
        }

    }


};

const handleGenerateReport = async () => {
    loadingAction.value = 'report';
    try {
        // const blob = await adminService.generateDailyReport();
        // const url = window.URL.createObjectURL(blob);
        // const a = document.createElement('a');
        // a.href = url;
        // a.download = `reporte-diario-${new Date().toISOString().split('T')[0]}.pdf`;
        // document.body.appendChild(a);
        // a.click();
        // a.remove();
        // notificationStore.add({ message: 'Reporte generado con éxito.', type: 'success' });

        await new Promise(res => setTimeout(res, 2000)) 
        console.log("Éxito al generar reporte (simulado)");
    } catch (error) {
        // notificationStore.add({ message: 'Error al generar el reporte.', type: 'error' });
        console.error("Error al generar reporte (simulado)");
    } finally {
        loadingAction.value = null;
    }
};

const triggerSubmit = async () => {
    formComponent.value.submit();
};
</script>

<template>
    <section class="actions-panel">
        <div class="panel-header">
            <h3 class="panel-title">Acciones Rápidas</h3>
            <p class="panel-subtitle">Gestiona las operaciones principales de tu cafetería</p>
        </div>
        
        <div class="actions-grid">
            <!-- Renovar QRs -->
            <div 
                class="action-card qr-card"
                :class="{ 'loading': loadingAction === 'renew' }"
                @click="handleRenewQRs"
            >
                <div class="action-icon-container">
                    <div v-if="loadingAction === 'renew'" class="loading-spinner"></div>

                    
                    <i-material-symbols-qr-code-2-rounded v-else class="action-icon" />
                </div>
                
                <div class="action-content">
                    <h4 class="action-title">Renovar QRs</h4>
                    <p class="action-description">
                        Genera nuevos códigos QR para todas las mesas. Esta acción invalida los códigos anteriores.
                    </p>
                    <div class="action-badge">
                        <span class="badge-text">Seguridad</span>
                    </div>
                </div>
                
                <div class="action-arrow">
                    <i-material-symbols-arrow-forward-ios-rounded />
                </div>
            </div>

            <!-- Generar Reporte -->
            <div 
                class="action-card report-card"
                :class="{ 'loading': loadingAction === 'report' }"
                @click="handleGenerateReport"
            >
                <div class="action-icon-container">
                    <div v-if="loadingAction === 'report'" class="loading-spinner"></div>
                    <i-material-symbols-analytics-outline-rounded v-else class="action-icon" />
                </div>
                
                <div class="action-content">
                    <h4 class="action-title">Generar Reporte</h4>
                    <p class="action-description">
                        Descarga el reporte diario con estadísticas de ventas, productos más vendidos y análisis financiero.
                    </p>
                    <div class="action-badge">
                        <span class="badge-text">Análisis</span>
                    </div>
                </div>
                
                <div class="action-arrow">
                    <i-material-symbols-arrow-forward-ios-rounded />
                </div>
            </div>

            <!-- Enviar Anuncio -->
            <div 
                class="action-card announcement-card"
                :class="{ 'loading': loadingAction === 'announce' }"
                @click="showAnnounceModal = true"
            >
                <div class="action-icon-container">
                   
                    <i-material-symbols-campaign-outline-rounded class="action-icon" />
                </div>
                
                <div class="action-content">
                    <h4 class="action-title">Enviar Anuncio</h4>
                    <p class="action-description">
                        Comunica ofertas especiales, cambios en el menú o mensajes importantes a todos los clientes.
                    </p>
                    <div class="action-badge">
                        <span class="badge-text">Comunicación</span>
                    </div>
                </div>
                
                <div class="action-arrow">
                    <i-material-symbols-arrow-forward-ios-rounded />
                </div>
            </div>
        </div>
    </section>

    <BaseModal v-model="showAnnounceModal" title="Enviar notification" max-width="lg">
        <SendNotificationForm ref="formComponent">
        </SendNotificationForm>
        
        <template #footer>
            <BaseButton variant="terciary" @click="showAnnounceModal = false">
                Cancelar
            </BaseButton>
            <BaseButton @click="triggerSubmit" variant="accent" :loading="formComponent?.isLoading">
                Guardar Producto
            </BaseButton>
        </template>
    </BaseModal>
</template>

<style scoped>
@reference "../../../style.css";

.actions-panel {
    @apply bg-primary rounded-2xl border border-border-dark p-8;
}

.panel-header {
    @apply mb-8;
}

.panel-title {
    @apply text-2xl font-bold text-text-light mb-2;
   
}

.panel-subtitle {
    @apply text-text-muted text-sm;
   
}

.actions-grid {
    @apply grid gap-6 grid-cols-1 lg:grid-cols-2 xl:grid-cols-3;
}

.action-card {
    @apply relative bg-surface rounded-xl p-6 cursor-pointer transition-all duration-300 ease-out;
    @apply border border-border-light hover:border-accent-light;
    @apply hover:shadow-lg hover:shadow-accent/10;
    @apply overflow-hidden;
}

.action-card:hover {
    @apply transform hover:-translate-y-1;
}

.action-card.loading {
    @apply pointer-events-none opacity-75;
}

.action-icon-container {
    @apply relative mb-4 w-12 h-12 flex items-center justify-center;
    @apply bg-accent/10 rounded-xl transition-all duration-300;
}

.action-card:hover .action-icon-container {
    @apply bg-accent/20 scale-110;
}

.action-icon {
    @apply w-6 h-6 text-accent transition-colors duration-300;
}

.action-card:hover .action-icon {
    @apply text-accent-dark;
}

.loading-spinner {
    @apply w-6 h-6 border-2 border-blue-600 border-t-transparent rounded-full animate-spin;
}

.action-content {
    @apply flex-1;
}

.action-title {
    @apply text-lg font-semibold text-text mb-2;
    
}

.action-description {
    @apply text-text-muted text-sm leading-relaxed mb-4;
    
    @apply group-hover:text-text transition-colors duration-300;
}

.action-badge {
    @apply inline-flex items-center;
}

.badge-text {
    @apply px-3 py-1 text-xs font-medium rounded-full;
    @apply bg-accent/10 text-accent;
    @apply group-hover:bg-accent/20;
  
}

.action-arrow {
    @apply absolute top-6 right-6 w-5 h-5 text-border opacity-0;
    @apply group-hover:opacity-100 group-hover:text-accent;
    @apply transition-all duration-300 transform translate-x-2;
    @apply group-hover:translate-x-0;
}

.action-arrow i {
    @apply w-full h-full;
}

/* Variantes específicas para cada acción */
.qr-card:hover {
    @apply shadow-info/20;
}

.qr-card .action-icon-container {
    @apply bg-info/10;
}

.qr-card:hover .action-icon-container {
    @apply bg-info/20;
}

.qr-card .action-icon {
    @apply text-info;
}

.qr-card:hover .action-icon {
    @apply text-info;
}

.qr-card .badge-text {
    @apply bg-info/10 text-info;
}

.qr-card:hover .badge-text {
    @apply bg-info/20;
}

.report-card:hover {
    @apply shadow-success/20;
}

.report-card .action-icon-container {
    @apply bg-success/10;
}

.report-card:hover .action-icon-container {
    @apply bg-success/20;
}

.report-card .action-icon {
    @apply text-success;
}

.report-card:hover .action-icon {
    @apply text-success;
}

.report-card .badge-text {
    @apply bg-success/10 text-success;
}

.report-card:hover .badge-text {
    @apply bg-success/20;
}

.announcement-card:hover {
    @apply shadow-warning/20;
}

.announcement-card .action-icon-container {
    @apply bg-warning/10;
}

.announcement-card:hover .action-icon-container {
    @apply bg-warning/20;
}

.announcement-card .action-icon {
    @apply text-warning;
}

.announcement-card:hover .action-icon {
    @apply text-warning;
}

.announcement-card .badge-text {
    @apply bg-warning/10 text-warning;
}

.announcement-card:hover .badge-text {
    @apply bg-warning/20;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .actions-panel {
        @apply p-6;
    }
    
    .panel-title {
        @apply text-xl;
    }
    
    .actions-grid {
        @apply gap-4;
    }
    
    .action-card {
        @apply p-5;
    }
}
</style>