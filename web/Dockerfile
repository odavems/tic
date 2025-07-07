FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    zip \
    unzip \
    git \
    curl \
    libxml2-dev \
    libzip-dev \
    libssl-dev \    
    libpq-dev \
    && docker-php-ext-install mysqli pdo_mysql mbstring exif pcntl bcmath gd xml pdo_pgsql

#RUN docker-php-ext-install mysqli

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

#solo para artisan, debe tener composer.json
#RUN composer install --no-dev --optimize-autoloader

EXPOSE 80

#solo para artisan o falta configurar default.conf
#COPY default.conf /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite

#solo para artisan
#RUN chmod +x artisan
