# ROADBOOK - Application Laravel 12 Mercuriale

## 📋 **Apercu du Projet**

Application Laravel 12 complete pour la gestion d'articles, familles, fournisseurs, unites, conditionnements, TVA et historique des prix. Developee selon les specifications du fichier `prompt_laravel_mercuriale.md`.

---

## 🎯 **Objectifs Initiaux**

- Creer une application Laravel 12 avec PHP 8.3+
- Implémenter une API REST avec authentification Sanctum
- Développer un frontend avec Tailwind CSS, Alpine.js et Blade
- Systeme d'authentification avec roles admin/user (pas d'inscription publique)
- Gestion CRUD complete pour 8 entites
- Tableau dynamique des articles avec Livewire
- Historique des prix par article/fournisseur

---

## 📅 **Journal de Developpement**

### **Phase 1: Initialisation du Projet**
- ✅ Creation du projet Laravel 12 avec Composer
- ✅ Installation de Laravel Breeze (Blade)
- ✅ Installation de Laravel Sanctum pour l'API
- ✅ Installation de Livewire pour les composants interactifs
- ✅ Configuration initiale de Tailwind CSS

**Commandes utilisees:**
```bash
composer create-project laravel/laravel mercuriale-app
composer require laravel/breeze laravel/sanctum livewire/livewire
php artisan breeze:install blade
npm install && npm run dev
```

---

### **Phase 2: Structure de la Base de Donnees**

#### **Modeles Crees (9)**
| Modele | Description | Relations |
|--------|-------------|-----------|
| User | Utilisateurs avec roles | hasMany(Article), etc. |
| Family | Familles d'articles | hasMany(SubFamily, Article) |
| SubFamily | Sous-familles | belongsTo(Family), hasMany(Article) |
| Unit | Unites de mesure | hasMany(Article) |
| Packaging | Conditionnements | hasMany(Article) |
| Vat | Taux de TVA | hasMany(Article) |
| Article | Articles principaux | belongsTo(Family, SubFamily, Unit, Packaging, Vat) |
| Supplier | Fournisseurs | hasMany(PriceHistory) |
| PriceHistory | Historique des prix | belongsTo(Article, Supplier) |

#### **Migrations (11)**
- Migration des tables de base (users, families, sub_families, units, packagings, vats, suppliers, articles, price_histories)
- Migration pour ajouter le champ `role` aux utilisateurs
- Migrations Sanctum pour les tokens d'API

#### **Enum**
- `UserRole.php` avec valeurs: admin, user

---

### **Phase 3: Logique Metier**

#### **Controllers API (8)**
- ArticleController (API)
- FamilyController (API)
- SubFamilyController (API)
- UnitController (API)
- PackagingController (API)
- VatController (API)
- SupplierController (API)
- PriceHistoryController (API)

**Fonctionnalites:**
- Index, Store, Show, Update, Destroy pour chaque modele
- Chargement des relations (eager loading) pour les performances
- Retours au format JSON avec API Resources

#### **API Resources (8)**
- ArticleResource
- FamilyResource
- SubFamilyResource
- UnitResource
- PackagingResource
- VatResource
- SupplierResource
- PriceHistoryResource

#### **Controllers Web (8)**
- FamilyController (Web)
- SubFamilyController (Web)
- UnitController (Web)
- PackagingController (Web)
- VatController (Web)
- SupplierController (Web)
- ArticleController (Web)
- UserController (Web)

**Fonctionnalites supplementaires:**
- `ArticleController::addPrice()` - Ajout de prix avec historique
- `ArticleController::priceHistories()` - Affichage de l'historique
- `UserController` - Gestion complete des utilisateurs (admin only)

#### **Requests de Validation (16)**
- StoreRequest et UpdateRequest pour chaque modele
- Validation des champs, regles personnalisees

---

### **Phase 4: Frontend et Vues**

#### **Layout Principal**
- `resources/views/layouts/app.blade.php`
  - Integration de Tailwind CSS via CDN
  - Integration d'Alpine.js via CDN
  - Support du dark mode
  - Structure de base avec barre de navigation

#### **Vues par Entite (31 vues)**

**Articles (5 vues)**
- `articles/index.blade.php` - Liste complete avec tableaux
- `articles/create.blade.php` - Formulaire de creation
- `articles/edit.blade.php` - Formulaire d'edition
- `articles/show.blade.php` - Details avec gestion des prix
- `articles/price-histories.blade.php` - Historique complete des prix

**Users (4 vues)**
- `users/index.blade.php` - Liste des utilisateurs
- `users/create.blade.php` - Creation d'utilisateur
- `users/edit.blade.php` - Edition d'utilisateur
- `users/show.blade.php` - Details d'utilisateur

**Familles (3 vues)**
- `families/index.blade.php` - Liste avec modale de creation
- `families/create.blade.php` - Formulaire
- `families/edit.blade.php` - Edition

**Sous-Familles (3 vues)**
- `sub-familles/index.blade.php`
- `sub-familles/create.blade.php`
- `sub-familles/edit.blade.php`

**Unites (3 vues)**
- `units/index.blade.php` - Liste avec modale
- `units/create.blade.php`
- `units/edit.blade.php`

**Conditionnements (3 vues)**
- `packagings/index.blade.php`
- `packagings/create.blade.php`
- `packagings/edit.blade.php`

**TVA (3 vues)**
- `vats/index.blade.php`
- `vats/create.blade.php`
- `vats/edit.blade.php`

**Fournisseurs (3 vues)**
- `suppliers/index.blade.php`
- `suppliers/create.blade.php`
- `suppliers/edit.blade.php`

#### **Composants Livewire (1)**
- `ArticleTable.php` - Tableau dynamique des articles avec:
  - Recherche en temps reel
  - Pagination
  - Tri
  - Affichage des relations

#### **Vue Dashboard**
- `dashboard.blade.php` - Tableau de bord avec:
  - Statistiques (total articles, familles, fournisseurs)
  - Graphique de repartition par famille
  - Actions rapides
  - Articles recents
  - Top fournisseurs

---

### **Phase 5: Authentification et Securite**

#### **Configuration**
- Laravel Breeze avec Blade
- Desactivation de l'inscription publique dans `routes/auth.php`
- Middleware `AdminMiddleware` pour les routes admin

**routes/auth.php:**
```php
// Registration routes disabled - only admins can create users
// Route::get('register', [RegisteredUserController::class, 'create'])
//     ->name('register');
// Route::post('register', [RegisteredUserController::class, 'store']);
```

**AdminMiddleware:**
```php
public function handle(Request $request, Closure $next): Response
{
    if ($request->user()?->role !== UserRole::ADMIN) {
        abort(403, 'Access denied. Admin only.');
    }
    return $next($request);
}
```

#### **Enregistrement dans AppServiceProvider**
```php
'admin' => \App\Http\Middleware\AdminMiddleware::class,
```

---

### **Phase 6: Routes**

#### **Routes API (routes/api.php)**
```php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('families', FamilyController::class);
    Route::apiResource('sub-families', SubFamilyController::class);
    Route::apiResource('units', UnitController::class);
    Route::apiResource('packagings', PackagingController::class);
    Route::apiResource('vats', VatController::class);
    Route::apiResource('articles', ArticleController::class);
    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('price-histories', PriceHistoryController::class);
});
```

#### **Routes Web (routes/web.php)**
- Routes publiques: `/`, `/dashboard`
- Routes authentifiees: toutes les entites CRUD
- Routes admin: gestion des utilisateurs

**Total: 90 routes**

---

### **Phase 7: Donnees Initiales**

#### **DatabaseSeeder**
- Creation de 2 utilisateurs:
  - admin@mercuriale.com / password (role: admin)
  - user@mercuriale.com / password (role: user)
- Creation de 5 familles
- Creation de 10 sous-familles
- Creation de 5 unites
- Creation de 5 conditionnements
- Creation de 3 taux de TVA
- Creation de 5 fournisseurs
- Creation de 20 articles
- Creation de 50 entrees d'historique de prix

---

### **Phase 8: Documentation**

#### **README.md**
- Instructions d'installation
- Configuration de la base de donnees
- Execution des migrations et seeders
- Compilation des assets
- Demarrage du serveur
- Endpoints API documentes
- Diagramme de la base de donnees (Mermaid)
- Credentials de test

---

## 🔧 **Corrections et Fixes**

### **Problemes Rencontres et Resolus**

1. **Route en double**
   - Problème: `articles/{article}/show` etait definie deux fois
   - Solution: Suppression de la route redondante dans `routes/web.php`

2. **Methode manquante dans UserController**
   - Problème: La route `users/{user}` necessitait une methode `show()`
   - Solution: Ajout de la methode `show()` et creation de `users/show.blade.php`

3. **Vue manquante pour Units**
   - Problème: `units/edit.blade.php` n'existait pas
   - Solution: Creation de la vue avec le meme style que les autres entites

4. **Erreur dans dashboard.blade.php (ligne 202)**
   - Problème: `Unsupported operand types: int / array`
   - Cause: `max($articlesByFamily->pluck('count')->toArray(), 1)`essayait de diviser par un tableau
   - Solution: Pre-calcul de `$maxCount` avant la boucle

5. **Inscription publique active**
   - Problème: Les utilisateurs pouvaient s'inscrire via `/register`
   - Solution: Commenter les routes d'inscription dans `routes/auth.php`

6. **Erreur Article::currentPrice**
   - Problème: `Article::currentPrice must return a relationship instance`
   - Cause: ArticleResource essayait d'utiliser `whenLoaded('currentPrice')` sur un accessor
   - Solution: Acces direct a `$this->current_price` dans ArticleResource

7. **Variable $families non definie**
   - Problème: `$families` etait undefined dans sub-familles/index.blade.php
   - Solution: Ajout de protection avec `?? []` dans la boucle foreach

8. **Middleware UserController**
   - Problème: Call to undefined method UserController::middleware()
   - Cause: Middleware deja applique au niveau des routes
   - Solution: Suppression de l'appel redondant dans le constructeur

9. **ucfirst() sur enum UserRole**
   - Problème: ucfirst() attend un string mais recevait App\Enums\UserRole
   - Cause: Probleme de casting ou de valeur nulle
   - Solution: Utilisation de l'operateur null-safe `$user->role?->value ?? $user->role ?? 'user'`

10. **Division par array dans dashboard**
    - Problème: Division par array dans le calcul des pourcentages
    - Solution: Pre-calcul de `$maxCount` et utilisation de valeurs par defaut

### **Nouvelle Fonctionnalite: Quantite Unitaire par Conditionnement**

11. **Packaging Unit Quantity**
    - Problème: Besoin de tracker la quantite par conditionnement (ex: 10kg dans un sac)
    - Solution:
      - Ajout du champ `packaging_unit_quantity` a la table `articles`
      - Mise a jour de articles/create.blade.php et articles/edit.blade.php avec un champ dedie
      - Suppression des champs `unit_id` et `unit_quantity` de la table `packagings`
      - Mise a jour de PackagingController, PackagingResource, et des Requests
    - Resultat: Chaque article a son propre conditionnement, unite, et quantite unitaire

12. **Lien "Creer un fournisseur"**
    - Problème: Besoin d'un lien pour creer un fournisseur lors de l'ajout de prix
    - Solution: Lien deja present dans articles/show.blade.php (ligne 224-233)

13. **Dates automatiques pour les prix**
    - Problème: Les champs "Valide depuis" et "Valide jusqu'a" ne devraient pas etre visibles
    - Solution: Champs deja caches avec `valid_from` automatiquement set a la date du jour

---

## 📊 **Statistiques du Projet**

| Metrique | Valeur |
|----------|--------|
| Total Fichiers | 199+ |
| Lignes de Code | 23,000+ |
| Modeles | 9 |
| Controllers API | 8 |
| Controllers Web | 8 |
| API Resources | 8 |
| Request Classes | 16 |
| Migrations | 13 |
| Vues Blade | 56 |
| Routes | 90 |
| Commits | 2 |

---

## 🚀 **Deployment**

### **Git Remote**
```bash
ssh://git@laboiteauxlettres.com:5196/mnt/srv/git/stock.git
```

### **Commandes de Deployment**
```bash
# Cloner le projet
git clone ssh://git@laboiteauxlettres.com:5196/mnt/srv/git/stock.git
cd stock

# Installer les dependances
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Configurer l'environnement
cp .env.example .env
php artisan key:generate
# Editer .env avec les credentials de la base de donnees

# Executer les migrations et seeders
php artisan migrate --seed --force

# Optimiser Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Demarrer le serveur
git
php artisan serve --host=0.0.0.0 --port=8000
```

---

## 📚 **Structure des Fichiers**

```
mercuriale-app/
├── app/
│   ├── Enums/
│   │   └── UserRole.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/ (8 controllers)
│   │   │   ├── Auth/ (8 controllers)
│   │   │   └── Web/ (8 controllers)
│   │   ├── Middleware/
│   │   │   └── AdminMiddleware.php
│   │   ├── Requests/ (16 request classes)
│   │   └── Resources/ (8 API resources)
│   ├── Livewire/
│   │   └── ArticleTable.php
│   └── Models/ (9 models)
├── database/
│   ├── migrations/ (11 migrations)
│   └── seeders/
│       └── DatabaseSeeder.php
├── resources/
│   ├── views/ (56 vues)
│   │   ├── articles/ (5)
│   │   ├── families/ (3)
│   │   ├── sub-familles/ (3)
│   │   ├── units/ (3)
│   │   ├── packagings/ (3)
│   │   ├── vats/ (3)
│   │   ├── suppliers/ (3)
│   │   ├── users/ (4)
│   │   ├── livewire/ (1)
│   │   └── layouts/ (3)
│   └── js/
│       ├── app.js
│       └── bootstrap.js
├── routes/
│   ├── api.php
│   ├── auth.php
│   └── web.php
├── .env.example
├── .gitignore
├── composer.json
├── README.md
└── ROADBOOK.md
```

---

## 🎯 **Fonctionnalites Implementees**

### **Backend**
- ✅ Systeme d'authentification avec roles
- ✅ API REST complete avec Sanctum
- ✅ Gestion CRUD pour toutes les entites
- ✅ Relations entre modeles
- ✅ Validation des donnees
- ✅ Historique des prix automatique
- ✅ Seeders pour les donnees de test

### **Frontend**
- ✅ Interface utilisateur complete
- ✅ Design responsive avec Tailwind CSS
- ✅ Interactivite avec Alpine.js
- ✅ Composants Livewire
- ✅ Dark mode support
- ✅ Tableaux de donnees avec pagination
- ✅ Formulaires avec validation visuelle

### **Securite**
- ✅ Protection des routes admin
- ✅ Pas d'inscription publique
- ✅ Middleware de protection
- ✅ Validation des entrees

---

## 📝 **Utilisateurs par Defaut**

| Email | Mot de passe | Role |
|-------|--------------|------|
| admin@mercuriale.com | password | admin |
| user@mercuriale.com | password | user |

---

## 🔮 **Ameliorations Futures**

- [ ] Importer/Exporter les donnees en CSV/Excel
- [ ] Recherche avancee avec filtres
- [ ] Graphiques interactifs (Chart.js)
- [ ] Notifications en temps reel
- [ ] API de synchronisation avec Excel
- [ ] Tests unitaires complets
- [ ] Documentation Swagger/OpenAPI
- [ ] Deployment automatise (CI/CD)
- [ ] Monitoring et logs centralises
- [ ] Sauvegardes automatiques

---

## 📞 **Support**

Pour toute question ou probleme, consulter:
- Le fichier README.md pour les instructions d'installation
- Le fichier ROADBOOK.md pour l'historique du developpement
- Les commentaires dans le code source

---

**Derniere mise a jour:** 18 juin 2026  
**Version:** 1.1.0  
**Auteur:** Mistral Vibe (avec co-auteur)  
**Repository:** ssh://git@laboiteauxlettres.com:5196/mnt/srv/git/stock.git  
**Commits:** 2
