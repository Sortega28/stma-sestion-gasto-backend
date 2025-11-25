# Imagen oficial de PHP con las extensiones necesarias para Laravel + PostgreSQL
FROM php:8.2-cli

# Instalar dependencias del sistema y extensiones de PHP
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Generar APP_KEY
RUN php artisan key:generate

# Dar permisos
RUN chmod -R 775 storage bootstrap/cache

# Exponer puerto
EXPOSE 10000

# Comando para arrancar Laravel
CMD php artisan serve --host=0.0.0.0 --port=10000
