# Mercuriale App - Gestion de Stock

Application Symfony 8 de gestion de stock equivalant au logiciel Laravel.

## Prerequis

- PHP 8.2+
- Composer
- Node.js (pour les assets optionnels)
- SQLite ou MySQL/PostgreSQL

## Installation

### 1. Cloner et installer les dependances

```bash
cd stock-app
composer install
```

### 2. Configurer l'environnement

Copiez le fichier `.env` et configurez les parametres de base de donnees :

```bash
cp .env .env.local
```

Modifiez `.env.local` pour configurer votre base de donnees :

```ini
DATABASE_URL="sqlite://%kernel.project_dir%/var/data.db"
# ou pour MySQL
# DATABASE_URL="mysql://user:password@localhost:3306/stock_app?serverVersion=8.0"

APP_SECRET="votre_secret_aleatoire"
```

### 3. Creer la base de donnees et les tables

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 4. Charger les donnees de test

```bash
php bin/console doctrine:fixtures:load
```

### 5. Lancer le serveur de developpement

```bash
symfony server:start
# ou
php -S localhost:8000 -t public/
```

L'application sera accessible a l'adresse `http://localhost:8000`

## Configuration des Assets

L'application utilise Tailwind CSS via CDN par defaut. Si vous preferez utiliser un build local :

1. Installez les dependances npm :
```bash
npm install
```

2. Lancez la compilation :
```bash
npm run dev
# ou en mode watch
npm run watch
```

3. Mettez a jour le layout pour utiliser le fichier local au lieu du CDN

## Structure du projet

- `src/Controller/Web/` - Controlleurs pour l'interface web
- `src/Controller/Api/` - Controlleurs pour l'API REST
- `src/Entity/` - Entites Doctrine
- `src/Repository/` - Repository pour les entites
- `src/Form/` - Formulaires Symfony
- `src/Data/` - DTOs pour la validation
- `src/Security/` - Composants de securite
- `src/Serializer/` - Normalisateurs pour les reponses API
- `src/Command/` - Commandes console
- `templates/` - Templates Twig
- `migrations/` - Migrations Doctrine
- `config/` - Configuration Symfony

## Routes principales

### Web
- `/` - Redirection vers le login
- `/login` - Connexion
- `/dashboard` - Tableau de bord
- `/families` - Liste des familles
- `/sub-familles` - Liste des sous-familles
- `/units` - Liste des unites
- `/packagings` - Liste des conditionnements
- `/vats` - Liste des TVA
- `/suppliers` - Liste des fournisseurs
- `/articles` - Liste des articles
- `/inventory` - Inventaire
- `/entries` - Entrees en stock
- `/profile` - Profile utilisateur
- `/users` - Gestion des utilisateurs (admin seulement)

### API (requiert l'authentification par token)
- `/api/families` - CRUD pour les familles
- `/api/sub-families` - CRUD pour les sous-familles
- `/api/units` - CRUD pour les unites
- `/api/packagings` - CRUD pour les conditionnements
- `/api/vats` - CRUD pour les TVA
- `/api/suppliers` - CRUD pour les fournisseurs
- `/api/articles` - CRUD pour les articles
- `/api/price-histories` - CRUD pour les historiques de prix

## Authentification API

Pour utiliser l'API avec l'authentification par token :

1. Connectez-vous via l'interface web
2. Veuillez consulter la documentation pour la creation de tokens

Les requetes API doivent inclure le header :
```
Authorization: Bearer VOTRE_TOKEN
```

## Tests

```bash
php bin/phpunit
```

## Commandes utiles

- Creer une migration : `php bin/console make:migration`
- Executer les migrations : `php bin/console doctrine:migrations:migrate`
- Charger les fixtures : `php bin/console doctrine:fixtures:load`
- Vider le cache : `php bin/console cache:clear`
- Lister les routes : `php bin/console debug:router`

## Technologies utilisees

- Symfony 8
- Doctrine ORM
- Twig
- Tailwind CSS
- Alpine.js
- Symfony Security
- Symfony Validator
- Symfony Form
- Symfony Serializer

## Licence

Ce projet est destine a un usage interne.
