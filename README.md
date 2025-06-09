# ☕ Café Buen Sabor - Proyecto Full-Stack

Este es el repositorio oficial para el proyecto "Café Buen Sabor". Contiene una aplicación full-stack con un backend en PHP y un frontend en Vue.js, todo orquestado con Docker para un entorno de desarrollo consistente y fácil de replicar.

## ✨ Características del Entorno

*   **Frontend:** Vue.js 3 con Vite.
*   **Backend:** API REST con PHP 8.2 (sin frameworks).
*   **Base de Datos:** MySQL 8.0.
*   **Gestor de BBDD:** phpMyAdmin.
*   **Entorno:** Totalmente dockerizado con Docker Compose.

---

## 🚀 Puesta en Marcha (Entorno de Desarrollo)

Para levantar el entorno de desarrollo local, los únicos requisitos son tener [Git](https://git-scm.com/) y [Docker con Docker Compose](https://www.docker.com/products/docker-desktop) instalados en tu sistema.

Sigue estos pasos para tener la aplicación corriendo en minutos:

### 1. Clonar el Repositorio

Abre tu terminal y clona el proyecto en tu máquina local.

```bash
git clone https://github.com/tu-usuario/cafe-buen-sabor.git
cd cafe-buen-sabor


# 📄 Configuración de Variables de Entorno

Este proyecto utiliza archivos `.env` para gestionar la configuración sensible y específica del entorno. Para empezar, debes crear tus propios archivos `.env` locales a partir de las siguientes plantillas.

---

### 1. Entorno de Docker (Raíz del Proyecto)

Este archivo controla la configuración de la infraestructura de Docker (puertos, base de datos, etc.).

**Copiar el archivo -> `.env.example` a un nuevo archivo llamado .env en el mismo nivel**

# PARA EL .ENV DEL FRONTEND PARA VITE ES LO MISMO
**Copiar el archivo -> `.env.example` a un nuevo archivo llamado .env en el mismo nivel**


Este README fue generado para el equipo de Café Buen Sabor. ¡A programar!