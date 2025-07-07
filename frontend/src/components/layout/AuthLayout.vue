<!-- src/components/layout/AuthLayout.vue (MEJORADO) -->
<template>
  <div class="auth-container">
    <header class="auth-header">
      <div class="logo-container">
        <div class="logo-icon">
          <i-mdi-coffee-outline class="w-full h-full" />
        </div>
        <div class="logo-content">
          <h1 class="logo-text">Café Buen Sabor</h1>
          <p class="logo-subtitle">Auténtico sabor colombiano</p>
        </div>
      </div>
    </header>

    <main class="auth-main">
      <div class="auth-card">
        <slot></slot>
      </div>
    </main>

    <footer class="auth-footer">
      <div class="footer-content">
        <p class="footer-text">© 2025 Café Buen Sabor</p>
        <div class="footer-links">
          <a href="#" class="footer-link">Términos</a>
          <span class="footer-divider">•</span>
          <a href="#" class="footer-link">Privacidad</a>
          <span class="footer-divider">•</span>
          <a href="#" class="footer-link">Ayuda</a>
        </div>
      </div>
    </footer>
  </div>
</template>

<style scoped>
/* Estilos base para prevenir desbordamiento horizontal */
* {
  box-sizing: border-box;
}

/*
  IMPORTANTE: Esta ruta debe ser correcta desde /src/components/layout/
  hasta /src/assets/styles/.
*/
@reference "../../style.css";



.auth-container {
  @apply min-h-screen w-full flex flex-col items-center justify-between p-4 sm:p-6 lg:p-8 overflow-hidden bg-primary-dark;
  /* Forzar que ocupe todo el ancho sin desbordamiento */
  width: 100vw;
  max-width: 100%;
  margin: 0;
  box-sizing: border-box;
}

.auth-header {
  @apply w-full max-w-md sm:max-w-lg lg:max-w-xl py-6 sm:py-8 lg:py-10 relative;
  /* Animación de entrada para el header */
  animation: slideDownFadeIn 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) both;
}

/* Línea de acento animada debajo del logo */
.auth-header::after {
  content: '';
  @apply absolute bottom-2 sm:bottom-4 left-1/2 w-0 h-px bg-accent;
  transform: translateX(-50%);
  animation: drawLine 0.6s 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
}

.logo-container {
  @apply flex items-center justify-center gap-3 sm:gap-4 lg:gap-5;
}

.logo-icon {
  /* Responsive icon size */
  @apply w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 text-accent flex-shrink-0;
}

.logo-content {
  @apply text-center sm:text-left;
}

.logo-text {
  @apply text-xl sm:text-2xl lg:text-3xl font-bold text-text-light tracking-wider leading-tight;
  font-family: var(--font-serif);
}

.logo-subtitle {
  @apply text-xs sm:text-sm lg:text-base text-text-light opacity-80 mt-1 font-light italic;
  font-family: var(--font-serif);
}

.auth-main {
  @apply flex-1 flex items-center justify-center w-full py-4 sm:py-6;
}

.auth-card {
  @apply w-full max-w-sm sm:max-w-md lg:max-w-lg p-6 sm:p-8 lg:p-10 rounded-xl border border-primary-light bg-primary;
  /* Sombra sutil para profundidad, con un brillo de acento */
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2), 0 0 15px #9d7d2a20;
  /* Animación de entrada para la tarjeta */
  animation: fadeInUp 0.7s 0.2s cubic-bezier(0.25, 0.46, 0.45, 0.94) both;
}

.auth-footer {
  @apply w-full max-w-md sm:max-w-lg lg:max-w-xl py-4 sm:py-6;
  /* Animación de entrada para el footer */
  animation: fadeInUp 0.8s 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94) both;
}

.footer-content {
  @apply flex flex-col sm:flex-row items-center justify-between gap-2 sm:gap-4;
}

.footer-text {
  @apply text-sm sm:text-base text-text-light opacity-70 font-light;
}

.footer-links {
  @apply flex items-center gap-2 sm:gap-3;
}

.footer-link {
  @apply text-xs sm:text-sm text-text-light opacity-70 hover:opacity-100 hover:text-accent transition-all duration-200 cursor-pointer;
}

.footer-link:hover {
  transform: translateY(-1px);
}

.footer-divider {
  @apply text-xs sm:text-sm text-text-light opacity-50;
}

/* Animaciones */
@keyframes slideDownFadeIn {
  from {
    opacity: 0;
    transform: translateY(-30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px) scale(0.98);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes drawLine {
  to {
    width: 60px;
  }
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .auth-container {
    @apply p-3;
  }
  
  .logo-container {
    @apply flex-col gap-2;
  }
  
  .logo-content {
    @apply text-center;
  }
  
  .auth-header::after {
    @apply w-12;
    animation: drawLine 0.6s 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
  }
  
  .footer-content {
    @apply text-center;
  }
}

/* Mejoras para pantallas muy pequeñas */
@media (max-width: 375px) {
  .auth-card {
    @apply p-4 mx-2;
  }
  
  .logo-text {
    @apply text-lg;
  }
  
  .logo-subtitle {
    @apply text-xs;
  }
}

/* Mejoras para pantallas grandes */
@media (min-width: 1024px) {
  .auth-container {
    @apply min-h-screen;
  }
  
  .auth-header::after {
    animation: drawLine 0.6s 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
  }
  
  @keyframes drawLine {
    to {
      width: 100px;
    }
  }
}
</style>