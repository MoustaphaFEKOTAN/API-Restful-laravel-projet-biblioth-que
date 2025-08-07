<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# API REST Laravel - Projet Bibliothèque

Ce projet est une API RESTful développée avec Laravel 11, utilisant Sanctum et Fortify pour l'authentification, Scribe pour la documentation automatique, et des tests unitaires/feature avec des factories personnalisées.

---

## Étapes de création de l'API REST

1. **Installation de Laravel et configuration initiale**  
   Création du projet Laravel, configuration de la base de données, des migrations, modèles et contrôleurs.

2. **Mise en place de l'authentification API avec Laravel Fortify et Sanctum**

   - Installer Fortify :  
     ```bash
     composer require laravel/fortify
     ```
   
   - Publier les ressources Fortify :  
     ```bash
     php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"
     ```
   
   - Configurer dans `config/fortify.php` pour utiliser le guard `sanctum` :
     ```php
     'guard' => 'sanctum',
     ```
   
   - Définir les routes et contrôleurs nécessaires pour gérer l'inscription, la connexion, la vérification email, la réinitialisation et la modification du mot de passe.

3. **Documentation automatique de l'API avec Scribe**

   - Installer Scribe :  
     ```bash
     composer require knuckleswtf/scribe
     ```
   
   - Publier la configuration et les vues :  
     ```bash
     php artisan vendor:publish --tag=scribe-config
     php artisan vendor:publish --tag=scribe-views
     ```
   
   - Générer la documentation :  
     ```bash
     php artisan scribe:generate
     ```
   
   - Pour tester la documentation interactive (Try It Out), modifier l'URL de l'API dans `resources/views/vendor/scribe/index.blade.php` :  
     ```js
     var tryItOutBaseUrl = "http://127.0.0.1:8000";
     ```  
     ou simplement modifier la variable `APP_URL` dans le `.env`.

   - Pour activer l'en-tête Bearer token sur les routes protégées, configurer `scribe.php`.

4. **Écriture des factories pour les tests**

   - Les factories permettent de générer des données factices en mémoire, ce qui facilite les tests sans polluer la base de données réelle.

   - Exemple pour créer une factory Role :  
     ```bash
     php artisan make:factory RoleFactory --model=Roles
     ```

5. **Tests unitaires et feature**

   - Créer un test :  
     ```bash
     php artisan make:test Auth/NomDuTest
     ```
   
   - Important : sauvegarder la base de données avant de lancer les tests pour éviter d’écraser des données importantes.

   - Pour lancer tous les tests :  
     ```bash
     php artisan test
     ```
   
   - Pour lancer un test spécifique :  
     ```bash
     php artisan test tests/Feature/CheminVersFichierTest.php
     ```
   
   - Pour lancer une méthode spécifique d’un test :  
     ```bash
     php artisan test --filter=nomDeLaMethode
     ```

---

## Comment tester l’API avec Postman

- Pour tester les routes **d’inscription** et **connexion** :  
  - Utiliser les headers suivants :  
    ```
    Accept: application/json
    Content-Type: application/json
    ```
  - Mettre le corps en `raw` JSON.

- Pour tester les routes nécessitant une authentification :  
  - Ajouter dans l’en-tête HTTP :  
    ```
    Authorization: Bearer <votre_token>
    ```

---

## Remarques

- Les tests utilisent des factories pour générer des données temporaires.
- La documentation Scribe est à jour et permet d'interagir facilement avec l'API.
- L’authentification est sécurisée via Laravel Sanctum et Fortify.

---

Merci de prendre le temps de regarder ce projet. N’hésitez pas à me contacter pour toute question !


