FROM php:8.1-apache

# Enable mod_rewrite for clean URLs
RUN a2enmod rewrite

# Install extensions (if needed)
RUN docker-php-ext-install pdo pdo_mysql

# Copy project files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
