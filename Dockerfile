# Use PHP 8.1 (or higher) with Apache
FROM php:8.1-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    default-mysql-client

# Set environment variables
ENV CI_ENVIRONMENT=development
ENV app_baseURL=http://localhost:8080/

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions and their dependencies
RUN apt-get update && apt-get install -y \
    libicu-dev \
    && docker-php-ext-install \
    mysqli \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    intl \
    fileinfo

# Configure PHP for development
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" && \
    sed -i \
        -e 's/;error_reporting = .*/error_reporting = E_ALL/' \
        -e 's/;display_errors = .*/display_errors = On/' \
        -e 's/;display_startup_errors = .*/display_startup_errors = On/' \
        -e 's/;log_errors = .*/log_errors = On/' \
        -e 's|^;upload_tmp_dir =.*|upload_tmp_dir = /var/www/html/writable/uploads/tmp|' \
        -e 's|^upload_max_filesize =.*|upload_max_filesize = 10M|' \
        -e 's|^post_max_size =.*|post_max_size = 10M|' \
        "$PHP_INI_DIR/php.ini" && \
    echo "file_uploads = On" >> "$PHP_INI_DIR/php.ini"

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Set permissions and ensure helper file is in the right place
RUN mkdir -p /var/www/html/writable/uploads/tmp /var/www/html/writable/uploads/attachments \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/writable \
    && chmod -R 777 /var/www/html/writable/uploads/tmp \
    && chmod -R 775 /var/www/html/writable/uploads/attachments \
    && chmod 644 /var/www/html/app/Helpers/response_helper.php

# Configure Apache DocumentRoot and PHP
RUN sed -i -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && sed -i -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && echo "php_value display_errors On" >> /etc/apache2/conf-available/php.conf \
    && echo "php_value display_startup_errors On" >> /etc/apache2/conf-available/php.conf \
    && a2enconf php

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
