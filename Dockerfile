FROM richarvey/nginx-php-fpm:3.1.6

# Copier le code
COPY . .

# Config Laravel
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

ENV APP_ENV production
ENV APP_DEBUG true
ENV LOG_CHANNEL stderr
ENV COMPOSER_ALLOW_SUPERUSER 1

# Installer les dépendances Laravel
RUN composer install --no-dev --optimize-autoloader --working-dir=/var/www/html

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

CMD ["/start.sh"]





