# Imagen PHP FPM con herramientas necesarias
FROM php:8.2-fpm

# Instalar dependencias del sistema necesarias
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    zip \
    unzip \
    git \
    curl

# Instalar extensiones de PHP necesarias
RUN docker-php-ext-install pdo pdo_pgsql zip mbstring

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos de la aplicación
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Generar APP_KEY
RUN php artisan key:generate

# Dar permisos correctos
RUN chmod -R 775 storage bootstrap/cache

# Exponer el puerto que usa Laravel
EXPOSE 10000

# Comando para arrancar el servidor
CMD php artisan serve --host=0.0.0.0 --port=10000
