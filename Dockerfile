# Imagen PHP FPM con herramientas necesarias
FROM php:8.2-fpm

# Instalar dependencias del sistema necesarias
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl

# Instalar extensiones de PHP necesarias para Laravel y Excel
RUN docker-php-ext-install pdo pdo_pgsql zip mbstring

# Configurar e instalar GD (necesario para PhpSpreadsheet y maatwebsite/excel)
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg
RUN docker-php-ext-install gd

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

# Permisos
RUN chmod -R 775 storage bootstrap/cache

# Exponer puerto
EXPOSE 10000

# Iniciar Laravel
CMD php artisan serve --host=0.0.0.0 --port=10000
# Imagen PHP FPM con herramientas necesarias
FROM php:8.2-fpm

# Instalar dependencias del sistema necesarias
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl

# Instalar extensiones de PHP necesarias para Laravel y Excel
RUN docker-php-ext-install pdo pdo_pgsql zip mbstring

# Configurar e instalar GD (necesario para PhpSpreadsheet y maatwebsite/excel)
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg
RUN docker-php-ext-install gd

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

# Permisos
RUN chmod -R 775 storage bootstrap/cache

# Exponer puerto
EXPOSE 10000

# Iniciar Laravel
CMD php artisan serve --host=0.0.0.0 --port=10000
# Imagen PHP FPM con herramientas necesarias
FROM php:8.2-fpm

# Instalar dependencias del sistema necesarias
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl

# Instalar extensiones de PHP necesarias para Laravel y Excel
RUN docker-php-ext-install pdo pdo_pgsql zip mbstring

# Configurar e instalar GD (necesario para PhpSpreadsheet y maatwebsite/excel)
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg
RUN docker-php-ext-install gd

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

# Permisos
RUN chmod -R 775 storage bootstrap/cache

# Exponer puerto
EXPOSE 10000

# Iniciar Laravel
CMD php artisan serve --host=0.0.0.0 --port=10000
# Imagen PHP FPM con herramientas necesarias
FROM php:8.2-fpm

# Instalar dependencias del sistema necesarias
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl

# Instalar extensiones de PHP necesarias para Laravel y Excel
RUN docker-php-ext-install pdo pdo_pgsql zip mbstring

# Configurar e instalar GD (necesario para PhpSpreadsheet y maatwebsite/excel)
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg
RUN docker-php-ext-install gd

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

# Permisos
RUN chmod -R 775 storage bootstrap/cache

# Exponer puerto
EXPOSE 10000

# Iniciar Laravel
CMD php artisan serve --host=0.0.0.0 --port=10000
# Imagen PHP FPM con herramientas necesarias
FROM php:8.2-fpm

# Instalar dependencias del sistema necesarias
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl

# Instalar extensiones de PHP necesarias para Laravel y Excel
RUN docker-php-ext-install pdo pdo_pgsql zip mbstring

# Configurar e instalar GD (necesario para PhpSpreadsheet y maatwebsite/excel)
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg
RUN docker-php-ext-install gd

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

# Permisos
RUN chmod -R 775 storage bootstrap/cache

# Exponer puerto
EXPOSE 10000

# Iniciar Laravel
CMD php artisan serve --host=0.0.0.0 --port=10000
