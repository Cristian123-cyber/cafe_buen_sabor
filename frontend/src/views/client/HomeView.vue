<template>
    <ClientLayout title="Inicio">
        <!-- Hero Section -->
        <div class="relative hero-section h-screen w-full overflow-hidden">

            <!-- 🎬 Video de fondo -->
            <video class="absolute top-0 left-0 w-full h-full object-cover" autoplay muted loop playsinline
                poster="https://img.freepik.com/free-photo/steaming-cup-coffee-saucer-with-coffee-beans-scattered-around_9975-124454.jpg">
                <source src="/videos/cafe-hero.mp4" type="video/mp4" />
                Tu navegador no soporta el video.
            </video>

            <!-- 🌒 Gradiente encima del video -->
            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-black/30 to-black/60 z-10"></div>

            <!-- 🌟 Contenido principal -->
            <div
                class="relative z-20 hero-content flex flex-col items-center justify-center text-center text-white h-full px-4">
                <h1 class="hero-title text-4xl md:text-5xl font-bold mb-4">Café Buen Sabor</h1>
                <p class="hero-subtitle text-lg md:text-xl mb-6">
                    Ordena tu café y comida favorita directamente desde tu mesa.<br>
                    <span class="highlight font-semibold text-yellow-400">Escanea, ordena y disfruta.</span>
                </p>

                <router-link :to="{ name: 'ClientMenu' }"
                    class="cta-button inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-6 py-3 rounded-full transition duration-300 group">
                    <i-mdi-food-outline class="w-5 h-5" />
                    <span>Explorar Menú</span>
                    <i-mdi-arrow-right class="w-4 h-4 transition-transform group-hover:translate-x-1" />
                </router-link>
            </div>

            <!-- ✨ Floating elements -->
            <div class="floating-elements absolute inset-0 z-30 pointer-events-none">
                <div class="floating-element floating-element-1"></div>
                <div class="floating-element floating-element-2"></div>
                <div class="floating-element floating-element-3"></div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- How it Works Section -->
            <section class="how-it-works-section">
                <div class="section-header">
                    <h2 class="section-title">¿Cómo funciona?</h2>
                    <p class="section-subtitle">
                        Cuatro pasos simples para disfrutar de la mejor experiencia
                    </p>
                </div>

                <div class="steps-container">
                    <!-- Step 1 -->
                    <div class="step-card" data-step="1">
                        <div class="step-number">01</div>
                        <div class="step-icon-wrapper">
                            <i-mdi-qrcode-scan class="step-icon" />
                        </div>
                        <div class="step-content">
                            <h3 class="step-title">Escanea el QR</h3>
                            <p class="step-description">
                                Usa tu teléfono para escanear el código QR de tu mesa
                            </p>
                        </div>
                        <div class="step-connector"></div>
                    </div>

                    <!-- Step 2 -->
                    <div class="step-card" data-step="2">
                        <div class="step-number">02</div>
                        <div class="step-icon-wrapper">
                            <i-mdi-book-open-page-variant-outline class="step-icon" />
                        </div>
                        <div class="step-content">
                            <h3 class="step-title">Explora el Menú</h3>
                            <p class="step-description">
                                Navega por nuestras categorías y descubre nuestros productos
                            </p>
                        </div>
                        <div class="step-connector"></div>
                    </div>

                    <!-- Step 3 -->
                    <div class="step-card" data-step="3">
                        <div class="step-number">03</div>
                        <div class="step-icon-wrapper">
                            <i-mdi-cart-plus class="step-icon" />
                        </div>
                        <div class="step-content">
                            <h3 class="step-title">Personaliza tu Pedido</h3>
                            <p class="step-description">
                                Selecciona y ajusta tus productos favoritos a tu gusto
                            </p>
                        </div>
                        <div class="step-connector"></div>
                    </div>

                    <!-- Step 4 -->
                    <div class="step-card" data-step="4">
                        <div class="step-number">04</div>
                        <div class="step-icon-wrapper">
                            <i-mdi-check-circle-outline class="step-icon" />
                        </div>
                        <div class="step-content">
                            <h3 class="step-title">Confirma y Disfruta</h3>
                            <p class="step-description">
                                Confirma tu pedido y nosotros lo llevaremos a tu mesa
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section class="features-section">
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i-mdi-clock-fast class="w-6 h-6" />
                        </div>
                        <h3 class="feature-title">Rápido y Eficiente</h3>
                        <p class="feature-description">Sin esperas innecesarias</p>
                    </div>

                    <!-- Opción 5: Enfoque en la experiencia -->
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i-mdi-star-circle class="w-6 h-6" />
                        </div>
                        <h3 class="feature-title">Experiencia Única</h3>
                        <p class="feature-description">Servicio innovador y cómodo</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">
                            <i-mdi-heart class="w-6 h-6" />
                        </div>
                        <h3 class="feature-title">Hecho con Amor</h3>
                        <p class="feature-description">Ingredientes de calidad</p>
                    </div>
                </div>
            </section>
        </div>
    </ClientLayout>
</template>

<script setup>
// No se necesita lógica de script para esta vista presentacional.
</script>

<style scoped>
/* Referenciamos la hoja de estilos principal para usar @apply */
@reference "../../style.css";

/* --- Variables CSS personalizadas --- */
:root {
    --accent-gradient: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
    --glass-bg: rgba(255, 255, 255, 0.1);
    --glass-border: rgba(255, 255, 255, 0.2);
}

/* --- Hero Section --- */
.hero-section {
    @apply relative flex min-h-[70vh] w-full flex-col items-center justify-center bg-cover bg-center bg-no-repeat p-6 text-center overflow-hidden rounded-lg;
}

.hero-content {
    @apply relative z-10 flex flex-col items-center gap-6 max-w-4xl;
}

.hero-badge {
    @apply flex items-center gap-2 px-4 py-2 rounded-full bg-white/20 backdrop-blur-sm border border-white/30 text-white text-sm font-medium;
}

.hero-title {
    @apply text-white text-5xl font-black leading-tight tracking-tight;
    text-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
}

.hero-subtitle {
    @apply text-white/90 text-lg font-normal leading-relaxed max-w-2xl;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.highlight {
    @apply text-accent-light font-semibold;
}

.cta-button {
    @apply mt-4 flex min-w-[180px] cursor-pointer items-center justify-center gap-x-3 overflow-hidden rounded-full bg-accent px-8 py-4 text-lg font-bold text-white shadow-xl transition-all duration-300 hover:scale-105 hover:bg-accent-dark hover:shadow-2xl active:scale-95;

}

/* Floating elements decoration */
.floating-elements {
    @apply absolute inset-0 pointer-events-none;
}

.floating-element {
    @apply absolute rounded-full bg-white/10 backdrop-blur-sm;
    animation: float 6s ease-in-out infinite;
}

.floating-element-1 {
    @apply w-20 h-20 top-1/4 left-1/4;
    animation-delay: 0s;
}

.floating-element-2 {
    @apply w-16 h-16 top-3/4 right-1/4;
    animation-delay: 2s;
}

.floating-element-3 {
    @apply w-12 h-12 top-1/2 right-1/3;
    animation-delay: 4s;
}

@keyframes float {

    0%,
    100% {
        transform: translateY(0px) rotate(0deg);
    }

    50% {
        transform: translateY(-20px) rotate(180deg);
    }
}

/* --- Main Content --- */
.main-content {
    @apply p-6 space-y-16;
}

/* --- How it Works Section --- */
.how-it-works-section {
    @apply mx-auto max-w-7xl px-4 sm:px-6 lg:px-8;
}

.section-header {
    @apply text-center mb-12 lg:mb-16;
}

.section-title {
    @apply text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-gray-900 mb-4;
}

.section-subtitle {
    @apply text-base sm:text-lg lg:text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed;
}

/* Steps Container - Mobile First Approach */
.steps-container {
    @apply relative space-y-8 sm:space-y-12;
}

/* Step Card Base Styles */
.step-card {
    @apply relative flex flex-col items-center text-center p-6 sm:p-8 lg:p-10 rounded-2xl bg-gradient-to-br from-white to-gray-50 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-accent/20;
}

.step-number {
    @apply absolute -top-4 -right-4 w-10 h-10 bg-gradient-to-r from-accent to-accent-dark text-white rounded-full flex items-center justify-center text-sm font-bold shadow-lg z-10;
}

.step-icon-wrapper {
    @apply flex h-16 w-16 sm:h-20 sm:w-20 items-center justify-center rounded-full bg-accent/10 text-accent mb-6 group-hover:bg-accent/15 transition-all duration-300;
}

.step-icon {
    @apply h-8 w-8 sm:h-10 sm:w-10;
}

.step-content {
    @apply space-y-3 max-w-sm;
}

.step-title {
    @apply text-xl sm:text-2xl font-semibold text-gray-800 group-hover:text-accent transition-colors duration-300;
}

.step-description {
    @apply text-gray-600 leading-relaxed text-sm sm:text-base;
}

/* Connectors - Hidden on mobile */
.step-connector {
    @apply hidden;
}

/* --- Tablet Styles (768px+) --- */
@media (min-width: 768px) {
    .steps-container {
        @apply grid grid-cols-2 gap-8 lg:gap-12 space-y-0;
    }

    .step-card {
        @apply min-h-[280px] justify-center;
    }

    /* Horizontal connectors for tablet 2x2 grid */
    .step-card[data-step="1"] .step-connector,
    .step-card[data-step="2"] .step-connector {
        @apply block absolute top-1/2 -bottom-8 left-1/2 w-0.5 h-16 bg-gradient-to-b from-accent/40 to-transparent transform -translate-x-1/2;
    }

    .step-card[data-step="1"] .step-connector {
        @apply -right-4 left-auto w-8 h-0.5 top-1/2 -translate-y-1/2 bg-gradient-to-r from-accent/40 to-transparent;
    }
}

/* --- Desktop Styles (1024px+) --- */
@media (min-width: 1024px) {
    .steps-container {
        @apply grid-cols-4 gap-6 xl:gap-8;
    }

    .step-card {
        @apply min-h-[320px] hover:scale-105 hover:-translate-y-2;
    }

    /* Reset tablet connectors */
    .step-card[data-step="1"] .step-connector,
    .step-card[data-step="2"] .step-connector {
        @apply -right-3 xl:-right-4 left-auto w-6 xl:w-8 h-0.5 top-1/2 -translate-y-1/2 bg-gradient-to-r from-accent/40 to-transparent;
    }

    .step-card[data-step="3"] .step-connector {
        @apply block absolute -right-3 xl:-right-4 top-1/2 w-6 xl:w-8 h-0.5 bg-gradient-to-r from-accent/40 to-transparent -translate-y-1/2;
    }

    .step-card[data-step="4"] .step-connector {
        @apply hidden;
    }
}

/* --- Large Desktop Styles (1280px+) --- */
@media (min-width: 1280px) {
    .steps-container {
        @apply gap-8;
    }

    .step-card {
        @apply p-12 min-h-[350px];
    }

    .step-content {
        @apply max-w-none;
    }

    .step-title {
        @apply text-2xl;
    }

    .step-description {
        @apply text-base;
    }
}

/* --- Animation Enhancements --- */
@keyframes stepAppear {
    from {
        opacity: 0;
        transform: translateY(30px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.step-card {
    animation: stepAppear 0.6s ease-out forwards;
}

.step-card[data-step="1"] {
    animation-delay: 0.1s;
}

.step-card[data-step="2"] {
    animation-delay: 0.2s;
}

.step-card[data-step="3"] {
    animation-delay: 0.3s;
}

.step-card[data-step="4"] {
    animation-delay: 0.4s;
}

/* --- Accessibility Improvements --- */
.step-card:focus-within {
    @apply ring-2 ring-accent/50 ring-offset-2;
}

@media (prefers-reduced-motion: reduce) {
    .step-card {
        animation: none;
    }

    .step-card:hover {
        @apply scale-100 translate-y-0;
    }
}

/* --- Features Section --- */
.features-section {
    @apply mx-auto max-w-4xl;
}

.features-grid {
    @apply grid grid-cols-1 gap-6;
}

.feature-card {
    @apply flex items-center gap-4 p-6 rounded-xl bg-white border border-gray-200 hover:border-accent/30 transition-all duration-300;
}

.feature-icon {
    @apply flex h-12 w-12 items-center justify-center rounded-lg bg-accent/10 text-accent flex-shrink-0;
}

.feature-title {
    @apply text-lg font-semibold text-gray-800;
}

.feature-description {
    @apply text-gray-600 text-sm;
}

/* --- Responsive Design --- */
@media (min-width: 640px) {
    .hero-title {
        @apply text-6xl;
    }

    .hero-subtitle {
        @apply text-xl;
    }

    .features-grid {
        @apply grid-cols-3;
    }

    .feature-card {
        @apply flex-col text-center;
    }
}

@media (min-width: 768px) {
    .steps-container {
        @apply grid-cols-2 gap-12;
    }

    .step-connector {
        @apply block absolute top-1/2 -right-6 w-12 h-0.5 bg-gradient-to-r from-accent/30 to-transparent;
    }

    .step-card:nth-child(even) .step-connector {
        @apply hidden;
    }
}

@media (min-width: 1024px) {
    .hero-section {
        @apply min-h-[80vh];
    }

    .hero-title {
        @apply text-7xl;
    }

    .steps-container {
        @apply grid-cols-4 gap-8;
    }

    .step-connector {
        @apply block;
    }

    .step-card:last-child .step-connector {
        @apply hidden;
    }
}
</style>