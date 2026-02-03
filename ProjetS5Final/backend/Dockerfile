FROM php:8.2-apache

# 1. Installer dépendances de base
RUN apt-get update && apt-get install -y \
    curl libpng-dev libonig-dev libxml2-dev libzip-dev zip unzip \
    && docker-php-ext-install pdo pdo_mysql mbstring gd zip \
    && a2enmod rewrite

# 2. Définir répertoire
WORKDIR /var/www/html

# 3. Copier code source
COPY src/ .

# 4. Créer dossiers Laravel
RUN mkdir -p storage/framework/{cache,sessions,testing,views} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache

# 5. Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 storage bootstrap/cache

# 6. Fichier index.php par défaut
RUN if [ ! -f "public/index.php" ]; then \
    echo "<?php echo 'Garage Elite API'; ?>" > public/index.php; \
    fi

CMD ["apache2-foreground"]