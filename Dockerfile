FROM php:8.2-cli

LABEL fly_launch_runtime="laravel"

# Instalar dependencias del sistema
RUN apt-get update && \
    apt-get install -y zip libpng-dev libonig-dev libxml2-dev

# Instalar la extensiones de PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar el directorio de trabajo
WORKDIR /proyecto

# Copiar los ficheros en /proyecto
COPY . /proyecto

# Instalar dependencias del proyecto
RUN composer install --prefer-dist --no-dev --optimize-autoloader --no-interaction

# Exponer el puerto 8000
EXPOSE 8080

# Start the container running php /artisan serve
CMD ["php", "/proyecto/artisan", "serve", "--host=0.0.0.0", "--port=8080"]