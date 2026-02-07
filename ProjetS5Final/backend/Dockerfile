FROM php:8.2-apache

# Installer les dépendances
RUN apt-get update && apt-get install -y \
    curl libpng-dev libonig-dev libxml2-dev libzip-dev zip unzip \
    && docker-php-ext-install pdo pdo_mysql mbstring gd zip \
    && a2enmod rewrite

# Supprimer le warning ServerName
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier le code source Laravel
COPY src/ .

# Créer les dossiers nécessaires pour Laravel
RUN mkdir -p storage/framework/{cache,sessions,testing,views} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Fallback si public/index.php n'existe pas (pour debug)
RUN if [ ! -f "public/index.php" ]; then \
        echo "<?php echo 'Garage Elite API - dossier public manquant'; ?>" > public/index.php; \
    fi

# === IMPORTANT : on copie la config Apache personnalisée ===
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# On s'assure que le site est activé (normalement déjà fait, mais au cas où)
RUN a2ensite 000-default.conf

CMD ["apache2-foreground"]