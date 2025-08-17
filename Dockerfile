# Image de base PHP 8.3 avec FPM
FROM php:8.3-fpm

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
 && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
 && rm -rf /var/lib/apt/lists/*

# Créer un utilisateur non-root
RUN useradd -ms /bin/bash laraveluser
USER laraveluser

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier le code de l'application
COPY --chown=laraveluser:laraveluser . .

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Exposer le port (facultatif si on utilise Nginx en front)
EXPOSE 9000

# Commande par défaut
CMD ["php-fpm"]
