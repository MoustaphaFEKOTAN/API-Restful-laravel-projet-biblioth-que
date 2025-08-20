#!/usr/bin/env bash
set -e

echo "=== Installation des dépendances Composer ==="
composer install --no-dev --optimize-autoloader --working-dir=/var/www/html

echo "=== Cache des configurations ==="
php artisan config:cache

echo "=== Cache des routes ==="
php artisan route:cache

echo "=== Exécution des migrations ==="
php artisan migrate --force

echo "=== Remplissage de la base de données ==="
php artisan db:seed

echo "=== Génération de la documentation API ==="
php artisan scribe:generate
