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

# Créer un utilisateur non-root
RUN useradd -ms /bin/bash laraveluser

# Définir le dossier de travail
WORKDIR /var/www/html

# Copier tout le code du projet
COPY --chown=laraveluser:laraveluser . .

# Configurer Nginx pour écouter sur le port $PORT
RUN echo 'server { \
    listen ${PORT}; \
    root /var/www/html/public; \
    index index.php index.html; \
    location / { try_files $uri /index.php?$query_string; } \
    location ~ \.php$$ { \
        include fastcgi_params; \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
    } \
}' > /etc/nginx/conf.d/default.conf

# Exposer le port
EXPOSE 8000

# Passer sur l’utilisateur laraveluser
USER laraveluser

# Commande par défaut 
CMD service nginx start && php-fpm -F
 