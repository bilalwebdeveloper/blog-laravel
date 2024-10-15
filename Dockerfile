# Use the official PHP image
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies, including cron
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    cron \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install pdo pdo_mysql zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy composer files and install PHP dependencies before copying the rest of the application
COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-scripts --optimize-autoloader --no-dev --verbose

# Copy the rest of the application source
COPY . .

# Copy the entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Create a cron job file
COPY ./cronjobs /etc/cron.d/laravel-cron

# Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/laravel-cron

# Apply cron job
RUN crontab /etc/cron.d/laravel-cron

# Create the log file to be able to run tail
RUN touch /var/log/cron.log

# Set the entrypoint
ENTRYPOINT ["entrypoint.sh"]

# Start the cron service
CMD ["cron", "-f"]

# Expose the port
EXPOSE 8000
