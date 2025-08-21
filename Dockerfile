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

# Installer Composer (tant qu'on est root)
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer


# Définir le dossier de travail
WORKDIR /var/www/html

# Copier tout le code du projet
COPY . .


# Commande par défaut 
CMD service php8.2-fpm start && nginx -g 'daemon off;'