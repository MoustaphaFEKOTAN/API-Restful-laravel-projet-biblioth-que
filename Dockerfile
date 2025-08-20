FROM richarvey/nginx-php-fpm:3.1.6

# Copier le code source
COPY . .

# Image config
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

# Installer les extensions PHP manquantes (dont bcmath)
RUN apk update && apk add --no-cache \
    php82-bcmath \
    php82-pdo_mysql \
    php82-tokenizer \
    php82-mbstring \
    php82-xml \
    php82-ctype \
    php82-opcache \
    php82-curl \
    php82-zip

# Lancer le script par défaut
CMD ["/start.sh"]
