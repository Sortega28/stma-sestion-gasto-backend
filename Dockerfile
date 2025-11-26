# Imagen PHP FPM con herramientas necesarias
FROM php:8.2-fpm

# Instalar dependencias del sistema necesarias
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_pgsql zip mbstring

# Configurar e instalar GD
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Dar permisos
RUN chmod -R 775 storage bootstrap/cache

# Exponer puerto (solo informativo para Docker/Render)
EXPOSE 10000

# Comando al iniciar: usar el puerto que Render indica
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
