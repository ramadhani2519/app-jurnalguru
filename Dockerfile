FROM php:8.1-apache

# Install ekstensi database
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Aktifkan rewrite module
RUN a2enmod rewrite

# Copy seluruh file project
COPY . /var/www/html/

# Atur DocumentRoot ke public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# Hapus paksa semua symlink MPM yang aktif di Apache agar tidak bentrok
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf \
    && a2enmod mpm_prefork

EXPOSE 80

CMD ["apache2-foreground"]