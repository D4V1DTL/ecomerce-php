FROM php:8.2-apache

# Instala dependencias necesarias para Laravel y SQLite
RUN apt-get update \
    && apt-get install -y libsqlite3-dev sqlite3 zip unzip \
    && docker-php-ext-install pdo pdo_sqlite

# Habilita el módulo de reescritura de Apache (para rutas amigables)
RUN a2enmod rewrite

# Cambia el DocumentRoot de Apache a /var/www/html/public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Opcional: instala Composer globalmente (si lo necesitas dentro del contenedor)
# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer