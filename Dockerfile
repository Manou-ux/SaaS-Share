FROM php:8.2-apache

# Installation des dépendances système et des extensions PHP requises pour PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql

# Configuration d'Apache pour Laravel (pointer vers /public)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Activation du module rewrite d'Apache
RUN a2enmod rewrite

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copie du code source dans le conteneur
WORKDIR /var/www/html
COPY . .

# Installation des dépendances Laravel
RUN composer install --no-dev --optimize-autoloader

# Attribution des droits corrects pour le stockage Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Utilisation du port fourni dynamiquement par Render
EXPOSE 80

# Script de démarrage pour vider les caches et lancer Apache
CMD php artisan config:cache && php artisan route:cache && apache2-foreground