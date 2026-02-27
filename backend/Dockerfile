# 1. Usamos una imagen oficial de PHP ligera (CLI es esencial para RoadRunner)
FROM php:8.3-cli-bullseye

# 2. Instalamos las dependencias del sistema operativo
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libpng-dev \
    libzip-dev \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# 3. Instalamos las extensiones de PHP necesarias
# pcntl y sockets son OBLIGATORIAS para Octane y Horizon
RUN docker-php-ext-install pdo_pgsql pcntl sockets gd zip

# 4. Instalamos la extensión de Redis vía PECL
RUN pecl install redis && docker-php-ext-enable redis

# 5. Descargamos Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 6. Configuramos el directorio de trabajo
WORKDIR /var/www/html

# 7. Copiamos los archivos de dependencias PRIMERO (para aprovechar la caché de Docker)
COPY composer.json composer.lock ./

# 8. Instalamos dependencias de PHP SIN las de desarrollo (--no-dev)
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-progress

# 9. Copiamos el resto del código de la aplicación
COPY . .

# 9.5 Limpiamos cualquier caché residual que se haya colado
RUN rm -rf bootstrap/cache/*.php

# 10. Obtenemos el binario de RoadRunner para la arquitectura correcta
RUN php artisan octane:install --server=roadrunner

# 11. Ajustamos permisos (Laravel necesita escribir en storage y bootstrap/cache)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 12. Exponemos el puerto de RoadRunner
EXPOSE 8000

# El comando de arranque se define en el docker-compose.yml,
# pero dejamos este por defecto por seguridad.
CMD ["php", "artisan", "octane:start", "--server=roadrunner", "--host=0.0.0.0", "--port=8000"]
