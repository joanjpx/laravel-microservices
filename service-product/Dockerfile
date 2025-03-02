FROM php:8.2-apache

# Set the correct Document Root
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Modify Apache config to use new DocumentRoot
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf && \
    sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf && \
    a2enmod rewrite

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    ssl-cert \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql gd zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Allow Git safe directory (fix "dubious ownership" error)
RUN git config --global --add safe.directory /var/www
