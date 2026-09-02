FROM php:8.1-apache

# Matikan modul MPM lain agar tidak bentrok
RUN a2dismod mpm_event mpm_worker || true \
    && a2enmod mpm_prefork rewrite

# Install ekstensi database
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy seluruh file project
COPY . /var/www/html/

# Ubah DocumentRoot Apache ke folder public CodeIgniter 4
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

EXPOSE 80

CMD ["apache2-foreground"]