# Étape 1 : Image PHP
FROM php:8.2-fpm

# Installer dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mbstring pdo pdo_mysql zip exif pcntl bcmath \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# Créer utilisateur non-root
RUN useradd -ms /bin/bash laraveluser

# Définir le dossier de travail
WORKDIR /var/www/html

# Copier le code
COPY --chown=laraveluser:laraveluser . .

# Passer sur l’utilisateur laraveluser
USER laraveluser

# Exposer le port (nécessaire pour Render)
EXPOSE 10000

# Commande de lancement
CMD ["php-fpm"]
