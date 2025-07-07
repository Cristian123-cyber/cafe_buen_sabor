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
