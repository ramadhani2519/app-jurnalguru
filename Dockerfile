FROM php:8.1-apache

# Install ekstensi database
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Aktifkan mod_rewrite untuk CodeIgniter 4
RUN a2enmod rewrite

# Copy seluruh project ke container
COPY . /var/www/html/

# Arahkan DocumentRoot ke folder public CodeIgniter 4
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

EXPOSE 80

# Bersihkan file konfigurasi MPM ganda sebelum menjalankan Apache
CMD ["sh", "-c", "rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf && a2enmod mpm_prefork && apache2-foreground"]