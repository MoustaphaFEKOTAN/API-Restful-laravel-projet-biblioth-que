#!/usr/bin/env bash
set -e

echo "=== Publication des fichiers de configuration de Scribe ==="
php artisan vendor:publish --tag=scribe-config

echo "=== Publication des fichiers de vue de Scribe ==="
php artisan vendor:publish --tag=scribe-views


echo "=== Génération de la documentation API ==="
php artisan scribe:generate

echo "=== Cache des configurations ==="
php artisan config:cache

# echo "=== Cache des routes ==="
# php artisan route:cache

echo "=== Exécution des migrations ==="
php artisan migrate --force

echo "=== Remplissage de la base de données ==="
php artisan db:seed

