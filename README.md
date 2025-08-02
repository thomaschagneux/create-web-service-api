# API de Service Web

## Description
Ce projet est une API RESTful développée avec Symfony 7.2. Elle permet la gestion de produits et d'acheteurs avec authentification JWT.

## Prérequis
- PHP 8.2 ou supérieur
- Composer
- Symfony CLI (recommandé pour le développement)
- Base de données (MySQL, PostgreSQL, etc.)

## Installation
1. Clonez le dépôt :
   ```bash
   git clone [url-du-dépôt]
   cd create-web-service-api
   ```

2. Installez les dépendances :
   ```bash
   composer install
   ```

3. Configurez votre fichier `.env` avec vos paramètres de base de données et JWT :
   ```
   DATABASE_URL="mysql://user:password@127.0.0.1:3306/db_name"
   JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
   JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
   JWT_PASSPHRASE=your_passphrase
   ```

4. Créez la base de données et exécutez les migrations :
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

5. (Optionnel) Chargez les fixtures pour avoir des données de test :
   ```bash
   php bin/console doctrine:fixtures:load
   ```

## Utilisation
Démarrez le serveur Symfony :
```bash
symfony server:start
```

L'API sera accessible à l'adresse : `http://localhost:8000`

La documentation de l'API est disponible à l'adresse : `http://localhost:8000/api/doc`

## Points d'accès API

### Produits
- `GET /api/product-list` - Récupérer la liste des produits (nécessite ROLE_USER)
- `GET /api/product/{id}` - Récupérer un produit spécifique (nécessite ROLE_ADMIN)

### Acheteurs
- `POST /api/buyer` - Créer un nouvel acheteur (nécessite ROLE_ADMIN)
- `GET /api/buyers` - Récupérer la liste des acheteurs
- `GET /api/buyer/{id}` - Récupérer un acheteur spécifique
- `DELETE /api/buyer/{id}` - Supprimer un acheteur

## Authentification
L'API utilise JWT pour l'authentification. Pour obtenir un token, envoyez une requête POST à `/api/login_check` avec les identifiants de l'utilisateur.

## Cache
L'API implémente un système de cache pour améliorer les performances, notamment pour la liste des produits.

## Développement
- Vérification du style de code : `php-cs-fixer fix`
- Analyse statique : `phpstan analyse`

## Licence
Propriétaire - Tous droits réservés
