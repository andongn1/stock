# Mercuriale App - Gestion des Articles

Une application Laravel 12 complete pour la gestion des articles, familles, sous-familles, unites, conditionnements, TVA et fournisseurs avec historique des prix.

## Fonctionnalites

### Backend
- **Laravel 12** avec PHP 8.3+
- **API REST** complete avec authentification Sanctum
- **CRUD** pour toutes les entites :
  - Familles
  - Sous-Familles
  - Unites
  - Conditionnements
  - TVA
  - Articles
  - Fournisseurs
  - Historique des prix
- **Authentification** avec Laravel Breeze (Blade)
- **Roles** : Admin et User
- **Middleware Admin** pour proteger les routes sensibles

### Frontend
- **Tailwind CSS** pour le style
- **Alpine.js** pour l'interactivite
- **Livewire** pour les composants reactifs
- **Blade** pour les templates
- **Mode sombre** pris en charge
- **Responsive design**

### Base de donnees
- 8 tables avec relations complexes
- Migrations Laravel
- Seeders pour les donnees initiales

## Installation

### Pre-requis
- PHP 8.3+
- Composer 2.5+
- Node.js 18+
- MySQL, PostgreSQL ou SQLite

### Etapes d'installation

1. **Cloner le projet**
```bash
cd /votre/dossier
git clone https://github.com/votre-utilisateur/mercuriale-app.git
cd mercuriale-app
```

2. **Installer les dependances PHP**
```bash
composer install
```

3. **Installer les dependances Node.js**
```bash
npm install
```

4. **Configurer l'environnement**
- Copier `.env.example` en `.env`
- Configurer les variables d'environnement (BDD, mail, etc.)
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configurer la base de donnees**
- Creer une base de donnees MySQL
- Mettre a jour les informations dans `.env` :
  ```
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=mercuriale
  DB_USERNAME=root
  DB_PASSWORD=
  ```

6. **Executer les migrations et les seeders**
```bash
php artisan migrate --seed
```

7. **Compiler les assets**
```bash
npm run dev
# Pour la production
npm run build
```

8. **Lancer le serveur de developpement**
```bash
php artisan serve
```

## Utilisation

### Acces
- **URL** : http://localhost:8000
- **Admin** : admin@mercuriale.com / password
- **User** : user@mercuriale.com / password

### API REST

#### Authentification
1. Creer un token Sanctum :
   ```bash
   POST /sanctum/token
   {
       "email": "admin@mercuriale.com",
       "password": "password",
       "device_name": "test"
   }
   ```
   
2. Utiliser le token dans les headers :
   ```
   Authorization: Bearer {token}
   ```

#### Endpoints
- `GET /api/families` - Liste des familles
- `POST /api/families` - Creer une famille
- `GET /api/families/{id}` - Details d'une famille
- `PUT /api/families/{id}` - Mettre a jour une famille
- `DELETE /api/families/{id}` - Supprimer une famille

- `GET /api/sub-families` - Liste des sous-familles
- `GET /api/units` - Liste des unites
- `GET /api/packagings` - Liste des conditionnements
- `GET /api/vats` - Liste des taux de TVA
- `GET /api/suppliers` - Liste des fournisseurs
- `GET /api/articles` - Liste des articles
- `GET /api/articles/search?search=...` - Rechercher des articles
- `GET /api/price-histories` - Liste des historiques de prix
- `GET /api/price-histories/current` - Prix actuels
- `GET /api/articles/{article}/price-histories` - Historique des prix pour un article

## Diagramme de la Base de Donnees

```
+-------------+       +----------------+       +--------------+
|    User     |       |    Family      |       |   SubFamily   |
+-------------+       +----------------+       +--------------+
| id          |       | id             |       | id           |
| name        |       | name           |       | name         |
| email       |       | created_at     |<----->| family_id    |
| password    |       | updated_at     |       | created_at   |
| role        |       +----------------+       | updated_at   |
| created_at  |               ^                  +--------------+
| updated_at  |               |                           ^
+-------------+               |                           |
      ^                       |                           |
      |                       v                           v
      |               +----------------+       +--------------+
      |               |    Article     |       |   Supplier   |
      +-------------->| id             |       +--------------+
      |               | name           |       | id           |
      |               | brand          |       | name         |
+-------------+       | family_id      |<----->| contact_info |
|  PriceHistory|       | sub_family_id |       +--------------+
+-------------+       | unit_id        |              ^
| id          |       | packaging_id   |              |
| article_id  |<----->| vat_id         |              |
| supplier_id |       | created_at     |--------------+
| price       |       | updated_at     |
| currency    |       +----------------+
| valid_from  |              ^
| valid_to    |              |
+-------------+              |
      ^                      v
      |              +----------------+
      |              |     Unit       |
      |              +----------------+
      |              | id             |
      |              | name           |
      |              | created_at     |
      +-------------->| updated_at     |
      |              +----------------+
      |
      |
      v
+----------------+
|  Packaging    |
+----------------+
| id             |
| name           |
| created_at     |
| updated_at     |
+----------------+

+----------------+
|     VAT       |
+----------------+
| id             |
| rate           |
| label          |
| created_at     |
| updated_at     |
+----------------+
```

## Technologies Utilisees

### Backend
- Laravel 12
- PHP 8.3+
- MySQL/PostgreSQL/SQLite
- Laravel Sanctum (API Auth)
- Laravel Breeze (Auth)
- Laravel Livewire

### Frontend
- Tailwind CSS
- Alpine.js
- Blade

### Outils
- Composer
- NPM
- Vite

## Contribution

1. Forker le projet
2. Creer une branche (`git checkout -b feature/AmazingFeature`)
3. Commit vos modifications (`git commit -m 'Add some AmazingFeature'`)
4. Pousser vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

## Licence

MIT
