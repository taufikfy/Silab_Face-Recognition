# php.Dockerfile
FROM php:8.2-apache

# Install ekstensi PHP yang dibutuhkan (pdo_mysql)
RUN docker-php-ext-install pdo pdo_mysql

# Copy semua file proyek ke folder web server di dalam container
COPY . /var/www/html/

# Pastikan folder 'faces' bisa ditulis oleh server
RUN chown -R www-data:www-data /var/www/html/