FROM php:8.1-cli

# Install ekstensi MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Set working directory
WORKDIR /var/www/html

# Copy seluruh file project
COPY . .

# Expose port yang digunakan
EXPOSE 8080

# Jalankan server bawaan CodeIgniter / PHP
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]