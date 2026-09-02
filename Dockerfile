FROM php:8.1-cli

# Install ekstensi database MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Set direktori kerja di dalam container
WORKDIR /var/www/html

# Copy seluruh file project
COPY . .

# Expose port yang digunakan
EXPOSE 8080

# Jalankan server bawaan PHP yang mengarah ke folder public CodeIgniter 4
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]