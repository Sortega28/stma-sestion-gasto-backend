# Imagen PHP FPM con herramientas necesarias para Laravel
FROM php:8.2-fpm

# Instalar dependencias del sistema necesarias
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_pgsql zip

# Instalar extensiones adicionales necesarias para Laravel
RUN docker-php-ext-install mbstring

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Generar clave APP_KEY
RUN php artisan key:generate

# Dar permisos a storage y bootstrap
RUN chmod -R 777 storage bootstrap/cache

# Exponer el puerto que usa Laravel
EXPOSE 10000

# Arrancar Laravel
CMD php artisan serve --host=0.0.0.0 --port=10000
