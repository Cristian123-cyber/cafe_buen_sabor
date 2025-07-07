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

