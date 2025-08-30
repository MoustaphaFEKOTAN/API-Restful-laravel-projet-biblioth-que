FROM richarvey/nginx-php-fpm:3.1.6

# Installer bcmath (et autres si besoin)
RUN docker-php-ext-install bcmath

# Copier le code
COPY . /var/www/html

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

COPY conf/nginx-site.conf /etc/nginx/conf.d/default.conf



CMD ["/start.sh"]





