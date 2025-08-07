Necesito desplegar este proyecto dockerizado con docker compose, de divide en estas partes:
- Contenedor para frontend: Proyecto vite + vue.js 
- Contenedor backend: API rest hecha con PHP y librerias como bramus con composer
- Contenedor para el cron job: cron job que ejecuta un script de cambios en la base de datos cada 10 minutos


Necesito que me guies para desplegarlo en un VPS

Tengo este docker-compose-yml:
version: '3.9'

services:
  mysql:
    image: mysql:8.0
    container_name: cafeteria_mysql
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD:-rootpass}
      MYSQL_DATABASE: ${DB_NAME:-cafeteria_db}
      MYSQL_USER: ${DB_USER:-cafeuser}           
      MYSQL_PASSWORD: ${DB_PASSWORD:-12345}
      TZ: America/Bogota

    volumes:
      - cafeteria_mysql_data:/var/lib/mysql
      - ./docker/db/init.sql:/docker-entrypoint-initdb.d/init.sql
    ports:
      - "${DB_PORT:-3306}:3306"
    networks:
      - cafeteria_net

  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    container_name: cafeteria_phpmyadmin
    environment:
      PMA_HOST: mysql
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD:-rootpass}
    ports:
      - "${PHPMYADMIN_PORT:-8080}:80"
    depends_on:
      - mysql
    networks:
      - cafeteria_net

  backend:
    build: ./backend
    container_name: cafeteria_backend
    environment:
      DB_HOST: mysql
      DB_NAME: ${DB_NAME:-cafeteria_db}
      DB_USER: ${DB_USER:-cafeuser}
      DB_PASSWORD: ${DB_PASSWORD:-cafepass}
      TZ: America/Bogota
    volumes:
      - ./backend:/var/www/html
    ports:
      - "${BACKEND_PORT:-8000}:80"
    depends_on:
      - mysql
    networks:
      - cafeteria_net

  frontend:
    build: ./frontend
    container_name: cafeteria_frontend
    environment:
      VITE_API_URL: ${VITE_API_URL:-http://localhost:8000/api}
    volumes:
      - ./frontend:/app
      - cafeteria_node_modules:/app/node_modules  # ✔️ volumen nombrado que se llena bien desde el build
    ports:
      - "${FRONTEND_PORT:-5173}:5173"
    working_dir: /app
    command: npm run dev
    networks:
      - cafeteria_net
  cron:
    build: ./cron
    container_name: cafeteria_cron
    depends_on:
      - mysql
    environment:
      DB_HOST: mysql
      DB_NAME: ${DB_NAME:-cafeteria_db}
      DB_USER: ${DB_USER:-cafeuser}
      DB_PASSWORD: ${DB_PASSWORD:-12345}
    volumes:
      - ./backend:/var/www/html   # Solo se monta como volumen, NO se copia
    networks:
      - cafeteria_net


volumes:
  cafeteria_mysql_data:
  cafeteria_node_modules:

networks:
  cafeteria_net:

necesito crear el docker-compose.prod.yml.


DockersFiles:
Frontend:

# Usar Node.js 18
FROM node:18-alpine

# Establecer directorio de trabajo
WORKDIR /app

# Copiar package.json y package-lock.json
COPY package*.json ./

# Instalar dependencias
RUN npm install

# Copiar el resto del código de la aplicación
COPY . .

# Exponer puerto 5173
EXPOSE 5173
 



Backend:
# Usar PHP 8.2 con Apache
FROM php:8.2-apache

# Instalar extensiones PHP para MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilitar módulos de Apache necesarios
RUN a2enmod rewrite headers

# ------------------------------------------------------------------
# --- PASO CLAVE AÑADIDO ---
# Copiamos nuestra configuración personalizada de Virtual Host.
# Esto le dice a Apache que sirva el sitio desde la carpeta /public,
# que es la práctica recomendada y la solución a tus errores 403 y 404.
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf
# ------------------------------------------------------------------

# Configurar Apache para .htaccess
# Esta línea ya no es estrictamente necesaria porque nuestro .conf 
# ya tiene "AllowOverride All", pero no hace daño dejarla.
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf


# Configurar zona horaria
RUN apt-get update && apt-get install -y tzdata
ENV TZ=America/Bogota
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Configurar zona horaria en PHP
RUN echo "date.timezone = America/Bogota" > /usr/local/etc/php/conf.d/timezone.ini
# Establecer directorio de trabajo
WORKDIR /var/www/html

# Exponer puerto 80
EXPOSE 80   


Cron:
FROM php:8.2-cli

RUN apt-get update \
    && apt-get install -y cron \
    && docker-php-ext-install pdo_mysql

# Directorio donde montaremos el backend (no copiamos nada aquí)
WORKDIR /var/www/html

# Copiamos crontab y el script de inicio
COPY crontab.txt /etc/cron.d/mesa-cron
COPY start.sh /start.sh

# Permisos y configuración
RUN chmod 0644 /etc/cron.d/mesa-cron \
    && chmod +x /start.sh \
    && crontab /etc/cron.d/mesa-cron

CMD ["/start.sh"]




EL backend esta con la imagen de php:8.2-apache. Pero creeria que opara el front deberia configuarse algo con nginx por qie el servidor vite no es para produccion.

Ademas te paso las configuraciones que tengo para que me digas que hay que modificar.

.env:
# Base de datos
DB_ROOT_PASSWORD=root123
DB_NAME=cafe_buen_sabor_db
DB_USER=cafeuser
DB_PASSWORD=12345
DB_PORT=3306

# Puertos
BACKEND_PORT=8000
FRONTEND_PORT=5173
PHPMYADMIN_PORT=8080

# API URL para el frontend
VITE_API_URL=http://localhost:8000/api


.env frontend:
# URL del backend para el proxy de Vite en desarrollo
VITE_PROXY_TARGET=http://backend:80
VITE_API_BASE_URL=/api
# Puerto para el servidor de desarrollo de Vite
VITE_PORT=5173

vite.config:
/* import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue()],
}) */

import { defineConfig, loadEnv } from "vite";
import vue from "@vitejs/plugin-vue";
import tailwindcss from "@tailwindcss/vite";
import Components from "unplugin-vue-components/vite";
import Icons from "unplugin-icons/vite";
import IconsResolver from "unplugin-icons/resolver";

export default defineConfig(({ mode }) => {
  // Carga las variables de entorno desde el .env de la carpeta frontend
  const env = loadEnv(mode, process.cwd(), "");

  return {
    plugins: [
      vue(),
      tailwindcss(),
      Components({
        dirs: ["src/components"], // Autoimporta los componentes locales
        extensions: ["vue"],
        deep: true,
        resolvers: [
          IconsResolver({
            prefix: "i", // permite usar <i-mdi-home />, <i-fa6-solid-user />, etc.
          }),
        ],
      }),
      Icons({
        autoInstall: true, // Descarga automáticamente los íconos usados
      }),
    ],

    server: {
      host: "0.0.0.0", // Esto se mantiene fijo para Docker

      // Usamos la variable de entorno para el puerto.
      // Hacemos un parseInt para asegurarnos de que es un número.
      port: parseInt(env.VITE_PORT) || 5173,
      watch: {
        // ---- ESTE ES EL BLOQUE IMPORTANTE ----
        // Reactivamos el polling porque los eventos nativos no funcionan
        usePolling: true,

        // Le decimos a Vite que revise los archivos cada 500 milisegundos (medio segundo)
        // Puedes ajustar este valor. 1000ms (1 segundo) también es una buena opción.
        // ¡Esto es lo que reduce drásticamente el uso de CPU!
        interval: 1000,
      },

      proxy: {
        "/api": {
          // Usamos la variable de entorno para el target del proxy
          target: env.VITE_PROXY_TARGET || "http://localhost:8000",
          changeOrigin: true,
        },
      },
    },
  };
});


.env backend
JWT_CLIENT_SESSION_EXPIRATION_HOURS=3

JWT_SECRET="cafe_buen_sabor_kevin_pro"
JWT_EXPIRATION_HOURS=1



Database config
<?php

namespace Config;

// Clase para manejar la conexión a la base de datos usando Singleton
class Database
{
    private static $instance = null;
    private $host = "cafeteria_mysql"; // Usa el nombre del servicio MySQL en Docker
    private $db_name = "cafe_buen_sabor_bd";
    private $username = "root";
    private $password = "root123";
    private $conn = null;

    // Constructor privado para evitar instanciación directa
    private function __construct() {}

    // Método para obtener la instancia única (Singleton)
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Método para obtener la conexión PDO
    public function getConnection()
    {
        if ($this->conn === null) {
            try {
                $this->conn = new \PDO(
                    "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                    $this->username,
                    $this->password,
                    array(
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                        \PDO::ATTR_EMULATE_PREPARES => false
                    )
                );
            } catch (\PDOException $e) {
                // Lanza la excepción para que el controlador la maneje
                throw $e;
            }
        }
        return $this->conn;
    }

    // Prevenir clonación del objeto
    private function __clone() {}

    // Prevenir deserialización del objeto (debe ser público)
    public function __wakeup()
    {
        throw new \Exception("No se puede deserializar un singleton");
    }
}

