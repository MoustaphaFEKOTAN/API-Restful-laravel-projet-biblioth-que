#!/usr/bin/env bash 
echo "Exécution de composer" 
# composer global require hirak/prestissimo 
composer install --no-dev --working-dir=/var/www/html 

echo "génération de la clé d'application..." 
php artisan key:generate --show 

echo "Configuration de mise en cache..." 
php artisan config:cache 

echo "Mise en cache des routes..." 
php artisan route:cache 

echo "Migrations en cours..." 
php artisan migrate --force

echo "Remplissage de la base de données..." 
php artisan db:seed