#!/usr/bin/env bash
set -e

echo "=== Génération de la documentation API ==="
php artisan scribe:generate

echo "=== Cache des configurations ==="
php artisan config:cache

echo "=== Suppression du cache des routes Scribe pour éviter 404 sur /docs ==="
php artisan route:clear

echo "=== Mise en cache des routes normales ==="
# Ici on met en cache toutes les routes sauf celles qui posent problème
# En général, Laravel ne permet pas d'exclure des routes, donc on mettra le cache après avoir généré la doc
php artisan route:cache || echo "Certaines routes (ex: docs) ne peuvent pas être mises en cache. Ignoré."

echo "=== Exécution des migrations ==="
php artisan migrate --force

echo "=== Remplissage de la base de données ==="
php artisan db:seed

echo "=== Déploiement terminé ==="
