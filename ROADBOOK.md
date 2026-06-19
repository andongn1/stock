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
- Gestion des prix de vente par article avec calcul de ratio

---

## 📅 **Journal de Developpement**

### **Phase 1: Initialisation du Projet**
- Creation du projet Laravel 12 avec Composer
- Installation de Laravel Breeze (Blade)
- Installation de Laravel Sanctum pour l'API
- Installation de Livewire pour les composants interactifs
- Configuration initiale de Tailwind CSS

**Commandes utilisees:**
```bash
composer create-project laravel/laravel mercuriale-app
composer require laravel/breeze laravel/sanctum livewire/livewire
php artisan breeze:install blade
npm install && npm run dev
```

### **Phase 2: Structure de la Base de Donnees**

#### **Modeles (10)**
| Modele | Description | Relations |
|--------|-------------|-----------|
| User | Utilisateurs avec roles | hasMany(Article), etc. |
| Family | Familles d'articles | hasMany(SubFamily, Article) |
| SubFamily | Sous-familles | belongsTo(Family), hasMany(Article) |
| Unit | Unites de mesure | hasMany(Article) |
| Packaging | Conditionnements | hasMany(Article) |
| Vat | Taux de TVA | hasMany(Article) |
| Article | Articles principaux | belongsTo(Family, SubFamily, Unit, Packaging, Vat), hasMany(PriceHistory), hasOne(SellingPrice) |
| Supplier | Fournisseurs | hasMany(PriceHistory) |
| PriceHistory | Historique des prix | belongsTo(Article, Supplier) |
| SellingPrice | Prix de vente | belongsTo(Article) |

#### **Migrations (16)**
- Tables de base (users, families, sub_families, units, packagings, vats, suppliers, articles, price_histories)
- Champ `role` pour les utilisateurs
- Tables Sanctum pour les tokens d'API
- Champ `packaging_unit_quantity` dans articles
- Champ `price_type` dans price_histories
- Table selling_prices pour les prix de vente

#### **Enums (3)**
- `UserRole.php`: admin, user
- `PriceType.php`: per_unit, per_packaging
- `SaleType.php`: per_unit, per_packaging (pour les prix de vente)

---

### **Phase 3: Logique Metier**

#### **Controllers API (8)**
ArticleController, FamilyController, SubFamilyController, UnitController, PackagingController, VatController, SupplierController, PriceHistoryController

#### **API Resources (8)**
ArticleResource, FamilyResource, SubFamilyResource, UnitResource, PackagingResource, VatResource, SupplierResource, PriceHistoryResource

#### **Controllers Web (8)**
FamilyController, SubFamilyController, UnitController, PackagingController, VatController, SupplierController, ArticleController, UserController

#### **Requests de Validation (18)**
StoreRequest et UpdateRequest pour chaque modele + ProfileUpdateRequest

#### **Middleware (1)**
- AdminMiddleware

---

### **Phase 4: Frontend et Vues**

#### **Layout Principal**
`resources/views/layouts/app.blade.php` avec Tailwind CSS, Alpine.js et support dark mode

#### **Vues (56 vues Blade)**
- Articles (5): index, create, edit, show, price-histories
- Users (4): index, create, edit, show
- Familles, Sous-Familles, Unites, Conditionnements, TVA, Fournisseurs (3 chacun)
- Authentication (6), Profile (4), Components (12)

#### **Composants Livewire (1)**
- ArticleTable.php: recherche, pagination, tri

#### **Vue Dashboard**
Statistiques, graphiques, articles recents, top fournisseurs

---

### **Phase 5: Authentification et Securite**
- Laravel Breeze avec Blade
- Inscription publique desactivee
- Middleware AdminMiddleware

---

### **Phase 6: Routes**
- Routes API: 8 ressources avec authentification Sanctum
- Routes Web: 90+ routes (publiques, authentifiees, admin)

---

### **Phase 7: Donnees Initiales**
**DatabaseSeeder:**
- 2 utilisateurs (admin@mercuriale.com, user@mercuriale.com)
- 5 familles, 10 sous-familles, 5 unites, 5 conditionnements
- 3 taux de TVA, 5 fournisseurs
- 20 articles avec packaging_unit_quantity
- 50 entrees d'historique de prix avec price_type

---

## 🔧 **Historique des Commits**

### **Commit 1: 2d70853**
**feat: Complete Laravel 12 application with REST API, Tailwind CSS, Alpine.js, Blade**
- Creation complete de l'application
- Tous les modeles, migrations, controllers, vues
- Configuration Tailwind CSS et Alpine.js
- DatabaseSeeder avec donnees initiales
- AdminMiddleware

### **Commit 2: ef3de99**
**docs: Add ROADBOOK.md and fix dashboard division by array error**
- Ajout ROADBOOK.md initial
- Fix: `max($articlesByFamily->pluck('count')->toArray(), 1)` → pre-calcul de $maxCount

### **Commit 3: 4458b79**
**fix: Convert currentPrice to accessor to resolve relationship error**
- Conversion de currentPrice() en accessor getCurrentPriceAttribute()
- Fix: `Article::currentPrice must return a relationship instance`

### **Commit 4: 8cb5da9**
**fix: Add missing $families variable to SubFamilyController index method**
- Chargement des familles dans SubFamilyController::index()
- Fix: `Undefined variable $families`

### **Commit 5: d227a3b**
**fix: Extend Controller class from Illuminate\Routing\Controller**
- Correction de l'extension des controllers
- Fix: `Call to undefined method UserController::middleware()`

### **Commit 6: 24f1c1e**
**fix: Handle UserRole enum in views - use ->value for string operations**
- Utilisation de `$user->role?->value` dans les vues
- Fix: `ucfirst() expects string, got UserRole enum`

### **Commit 7: 3a078d0**
**feat: Auto-set date for price history - remove date fields from form**
- `valid_from` = today() automatique
- Champs de date masques dans le formulaire

### **Commit 8: 81ab529**
**feat: Add link to create supplier in add price form**
- Lien "Creer un fournisseur" dans articles/show.blade.php
- Lien vers /suppliers/create

### **Commit 9: 8c95d54**
**feat: Add unit and unit_quantity to Packaging model**
- Ajout champs unit_id, unit_quantity a packagings (supprime plus tard)

### **Commit 10: 5bad2d9**
**feat: Display packaging unit info in article forms with Alpine.js**
- Affichage dynamique de l'unite et quantite du conditionnement
- Utilisation d'Alpine.js pour l'interactivite

### **Commit 11: 2be9697**
**Fix multiple errors and implement packaging unit quantity feature**
- Implementation complete de packaging_unit_quantity sur Article
- Champ `packaging_unit_quantity` dans la table articles
- Formulaires articles/create.blade.php et articles/edit.blade.php mis a jour
- Accesseur getPackagingUnitInfoAttribute() dans Article model

### **Commit 12: 150289a**
**Update ROADBOOK.md with latest fixes and features**
- Mise a jour de la documentation
- Documentation des fixes et nouvelles fonctionnalites

### **Commit 13: 7fba200**
**Add price_type to price histories for unit vs packaging pricing**
- Creation de l'enum PriceType (per_unit, per_packaging)
- Champ `price_type` dans price_histories
- Accesseurs getUnitPriceAttribute() et getPackagingPriceAttribute()
- Calcul automatique des deux prix (unite et conditionnement)

### **Commit 14: ec9b459**
**Update DatabaseSeeder with price_type and packaging_unit_quantity**
- Mise a jour du seeder pour inclure price_type
- Generer des prix avec les deux types (per_unit, per_packaging)
- packaging_unit_quantity pour tous les articles

### **Commit 15: a049841**
**Update ROADBOOK.md with price type feature documentation**
- Documentation complete de la fonctionnalite price_type
- Mise a jour des statistiques

### **Commit 16: 93d8714**
**Fix Firefox dark mode override with gray color palette**
- Add `<meta name="color-scheme" content="light dark">` to prevent Firefox auto-dark-mode
- Enable Tailwind class-based dark mode (`darkMode: 'class'`)
- Move Alpine.js theme toggle from `<body>` to `<html>` element
- Preserve original gray color palette (not slate)

---

## 🔧 **Corrections et Fixes**

### **Erreurs Fixes**

1. **Route en double**
   - Problème: `articles/{article}/show` definie deux fois
   - Solution: Suppression de la route redondante

2. **Methode manquante dans UserController**
   - Problème: Route `users/{user}` necessitait show()
   - Solution: Ajout de la methode show() et creation de users/show.blade.php

3. **Vue manquante pour Units**
   - Problème: `units/edit.blade.php` n'existait pas
   - Solution: Creation de la vue

4. **Division par array dans dashboard**
   - Problème: `max($articlesByFamily->pluck('count')->toArray(), 1)`
   - Solution: Pre-calcul de $maxCount

5. **Inscription publique active**
   - Problème: Utilisateurs pouvaient s'inscrire via /register
   - Solution: Commenter les routes dans routes/auth.php

6. **Article::currentPrice relationship error**
   - Problème: `must return a relationship instance`
   - Solution: Conversion en accessor getCurrentPriceAttribute()

7. **Variable $families non definie**
   - Problème: Dans sub-familles/index.blade.php
   - Solution: Chargement des familles dans le controller + protection `?? []`

8. **Middleware UserController**
   - Problème: `Call to undefined method UserController::middleware()`
   - Solution: Suppression de l'appel redondant, extension correcte de Controller

9. **ucfirst() sur enum UserRole**
   - Problème: ucfirst() attend un string, pas un enum
   - Solution: Utilisation de `$user->role?->value ?? $user->role ?? 'user'`

---

## ✨ **Nouvelles Fonctionnalites**

### **Fonctionnalite 1: Quantite Unitaire par Conditionnement**
**Contexte:** Besoin de tracker la quantite par conditionnement (ex: 10kg dans un sac)

**Implementation:**
- Ajout du champ `packaging_unit_quantity` (decimal 10,2) dans la table `articles`
- Suppression des champs `unit_id` et `unit_quantity` de la table `packagings` (car l'unite de l'article est independante du conditionnement)
- Formulaires articles/create.blade.php et articles/edit.blade.php:
  - Selection du conditionnement (packaging)
  - Selection de l'unite (unit)
  - Champ pour la quantite unitaire du conditionnement
- Accesseur dans Article model:
  ```php
  public function getPackagingUnitInfoAttribute(): string
  {
      if ($this->packaging_unit_quantity && $this->unit) {
          return $this->packaging_unit_quantity . ' ' . $this->unit->name;
      }
      return '';
  }
  ```
- Exemple: Article "Riz Basmati" avec packaging="Sac", unit="kg", packaging_unit_quantity=10 → affiche "10 kg"

**Avantages:**
- Chaque article a son propre conditionnement, unite, et quantite unitaire
- Deux articles avec le meme conditionnement peuvent avoir des unites et quantites differentes
- Exemple: Article A: Sac de 10 kg, Article B: Sac de 250 pieces

**Migrations:**
- 2026_06_18_000002_add_packaging_unit_quantity_to_articles_table.php
- 2026_06_18_114301_remove_unit_fields_from_packagings_table.php

---

### **Fonctionnalite 2: Lien pour Creer un Fournisseur**
**Contexte:** Besoin d'un lien pour creer un fournisseur lors de l'ajout de prix

**Implementation:**
- Dans articles/show.blade.php, section "Ajouter un prix":
  ```html
  <div class="mt-2 text-sm">
      <span class="text-gray-600 dark:text-gray-400">
          Le fournisseur n'existe pas? 
      </span>
      <a href="{{ route('suppliers.create') }}" 
         class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
          Creez-le
      </a>
  </div>
  ```
- Lien place en dessous du dropdown de selection des fournisseurs

---

### **Fonctionnalite 3: Dates Automatiques pour les Prix**
**Contexte:** Les champs "Valide depuis" et "Valide jusqu'a" ne devraient pas etre visibles

**Implementation:**
- Dans PriceHistoryController::store():
  ```php
  $validated['valid_from'] = now()->toDateString();
  $validated['valid_to'] = null; // Pas de date de fin par defaut
  ```
- Champs de date supprimes du formulaire dans articles/show.blade.php
- La date du jour est automatiquement enregistree

**Avantages:**
- Simplification du formulaire
- Historique des prix avec dates automatiques
- Possibilite de suivre l'evolution des prix dans le temps

---

### **Fonctionnalite 4: Prix par Unite ou par Conditionnement**
**Contexte:** Besoin de distinguer les prix fournisseurs par unite ou par conditionnement

**Implementation:**

**1. Enum PriceType:**
```php
namespace App\Enums;

enum PriceType: string
{
    case PER_UNIT = 'per_unit';
    case PER_PACKAGING = 'per_packaging';

    public function label(): string
    {
        return match($this) {
            self::PER_UNIT => 'Par unite',
            self::PER_PACKAGING => 'Par conditionnement',
        };
    }

    public static function options(): array
    {
        return [
            self::PER_UNIT->value => self::PER_UNIT->label(),
            self::PER_PACKAGING->value => self::PER_PACKAGING->label(),
        ];
    }
}
```

**2. Migration:**
- Ajout du champ `price_type` (string, default: 'per_unit') dans price_histories

**3. Model PriceHistory:**
```php
// Accesseurs pour calculer les deux prix
public function getUnitPriceAttribute(): float
{
    if ($this->price_type === PriceType::PER_UNIT) {
        return (float) $this->price;
    }
    // Price is per packaging, calculate unit price
    if ($this->article && $this->article->packaging_unit_quantity > 0) {
        return (float) ($this->price / $this->article->packaging_unit_quantity);
    }
    return (float) $this->price;
}

public function getPackagingPriceAttribute(): float
{
    if ($this->price_type === PriceType::PER_PACKAGING) {
        return (float) $this->price;
    }
    // Price is per unit, calculate packaging price
    if ($this->article && $this->article->packaging_unit_quantity > 0) {
        return (float) ($this->price * $this->article->packaging_unit_quantity);
    }
    return (float) $this->price;
}

public function getPriceTypeLabelAttribute(): string
{
    if ($this->price_type instanceof PriceType) {
        return $this->price_type->label();
    }
    return ucfirst(str_replace('_', ' ', $this->price_type ?? 'per_unit'));
}
```

**4. Formulaire (articles/show.blade.php):**
```html
<select name="price_type" id="price_type" 
        class="w-full p-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600">
    <option value="per_unit" {{ old('price_type') === 'per_unit' ? 'selected' : '' }}>Par unite</option>
    <option value="per_packaging" {{ old('price_type') === 'per_packaging' ? 'selected' : '' }}>Par conditionnement</option>
</select>
```

**5. Affichage des prix:**
```html
@foreach($article->priceHistories as $priceHistory)
    <tr>
        <td>{{ $priceHistory->supplier->name }}</td>
        <td>{{ $priceHistory->price }} {{ $priceHistory->currency }} ({{ $priceHistory->price_type_label }})</td>
        <td>{{ number_format($priceHistory->unit_price, 2) }} {{ $article->unit->name ?? '' }} / unite</td>
        <td>{{ number_format($priceHistory->packaging_price, 2) }} {{ $article->currency }} / {{ $article->packaging->name ?? '' }}</td>
    </tr>
@endforeach
```

**Exemples:**
1. **Fournisseur A:** Prix = 2,58€, Type = per_unit, Article avec packaging_unit_quantity = 10 kg
   - Prix par unite: 2,58€/kg
   - Prix par conditionnement: 25,80€/sac (2,58 × 10)

2. **Fournisseur B:** Prix = 25€, Type = per_packaging, Article avec packaging_unit_quantity = 10 kg
   - Prix par unite: 2,50€/kg (25 ÷ 10)
   - Prix par conditionnement: 25€/sac

**Avantages:**
- Chaque fournisseur peut proposer un prix par unite ou par conditionnement
- Les deux prix (unite et conditionnement) sont toujours calcules et affiches
- Permet une comparaison facile entre fournisseurs
- Flexibilite totale pour les fournisseurs

**Migrations:**
- 2026_06_18_123125_add_price_type_to_price_histories_table.php

---

### **Fonctionnalite 5: Prix de Vente par Article avec Ratio**
**Contexte:** Besoin de gerer un prix de vente TTC par article, avec la possibilite de vendre par unite ou par conditionnement, et de calculer le ratio de vente par fournisseur.

**Implementation:**

**1. Migration et Modele SellingPrice:**
```php
// Migration: 2026_06_18_230358_create_selling_prices_table.php
Schema::create('selling_prices', function (Blueprint $table) {
    $table->id();
    $table->foreignId('article_id')->constrained()->cascadeOnDelete();
    $table->decimal('price_ttc', 10, 2);
    $table->string('sale_type', 20)->default('per_unit'); // per_unit ou per_packaging
    $table->decimal('quantity', 10, 4)->default(1); // visible uniquement si per_unit
    $table->string('currency', 3)->default('EUR');
    $table->timestamps();
});

// Modele SellingPrice
class SellingPrice extends Model
{
    protected $fillable = ['article_id', 'price_ttc', 'sale_type', 'quantity', 'currency'];
    protected $casts = ['price_ttc' => 'decimal:2', 'sale_type' => SaleType::class, 'quantity' => 'decimal:4'];
    
    public function article(): BelongsTo { return $this->belongsTo(Article::class); }
    public function getSaleTypeLabelAttribute(): string { /* ... */ }
    public function getFormattedPriceAttribute(): string { /* ... */ }
}
```

**2. Enum SaleType:**
```php
namespace App\Enums;
enum SaleType: string { case PER_UNIT = 'per_unit'; case PER_PACKAGING = 'per_packaging'; }
```

**3. Article Model - Nouvelle Relation:**
```php
public function sellingPrice(): HasOne { return $this->hasOne(SellingPrice::class); }
public function getCurrentSellingPriceAttribute(): ?SellingPrice { return $this->sellingPrice()->latest()->first(); }
```

**4. ArticleController - Methode updateSellingPrice:**
```php
public function updateSellingPrice(UpdateSellingPriceRequest $request, Article $article): RedirectResponse
{
    $validated = $request->validated();
    if (!isset($validated['quantity']) || $validated['quantity'] === null) {
        $validated['quantity'] = ($validated['sale_type'] === 'per_unit') ? 1 : 1;
    }
    
    $sellingPrice = $article->sellingPrice()->first();
    if ($sellingPrice) {
        $sellingPrice->update($validated);
    } else {
        $article->sellingPrice()->create($validated);
    }
    
    return redirect()->route('articles.show', $article)->with('success', 'Prix de vente mis a jour !');
}
```

**5. Vue articles/show.blade.php - Section Prix de Vente:**
- Formulaire avec Prix TTC, Type de vente (per_unit/per_packaging), Quantite (cachee si per_packaging)
- Affichage du prix de vente actuel
- Calcul automatique du ratio par fournisseur

**6. Calcul du Ratio de Vente:**
```php
// Formules:
// - Si vente par unite: Ratio = (prix_achat_unite * quantite_vendue * TVA) / prix_vente_ttc
// - Si vente par conditionnement: Ratio = (prix_achat_conditionnement * TVA) / prix_vente_ttc
// Marge = (1 - ratio) * 100

// Affichage avec coloration:
- Vert si marge >= 20%
- Orange si marge >= 10%
- Rouge si marge < 10%
```

**7. Route:**
```php
Route::post('articles/{article}/update-selling-price', [ArticleController::class, 'updateSellingPrice'])->name('articles.update-selling-price');
```

**Exemples:**
1. Article: Pavé de Rumsteak, Prix d'achat = 19€/kg (per_unit), TVA = 10%, Prix de vente = 25€/kg, Quantite = 1
   - Ratio = (19 * 1 * 1.10) / 25 = 83.6% => Marge = 16.4%
   - Affichage: 83.6% (orange)

2. Article: Farine, Prix d'achat = 0.50€/kg (per_unit), TVA = 10%, Prix de vente = 10€/sac, Quantite = 5kg, Type = per_unit
   - Prix achat total = 0.50 * 5 = 2.50€
   - Ratio = (2.50 * 1.10) / 10 = 27.5% => Marge = 72.5%
   - Affichage: 27.5% (vert)

**Avantages:**
- Calcul automatique du ratio de vente par fournisseur
- Visualisation rapide de la rentabilite
- Flexibilite pour vendre par unite ou par conditionnement
- Integration complete avec le systeme de prix d'achat existant

**Migrations:**
- 2026_06_18_230358_create_selling_prices_table.php

---

---

## 📊 **Statistiques du Projet**

| Metrique | Valeur |
|----------|--------|
| **Total Fichiers** | 203+ |
| **Lignes de Code** | 19,000+ |
| **Fichiers PHP** | 180 |
| **Modeles** | 10 |
| **Enums** | 3 |
| **Controllers API** | 8 |
| **Controllers Web** | 27 |
| **API Resources** | 8 |
| **Request Classes** | 20 |
| **Middleware** | 1 |
| **Migrations** | 16 |
| **Vues Blade** | 56 |
| **Composants Livewire** | 1 |
| **Routes** | 91+ |
| **Commits** | 16 |

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
php artisan serve --host=0.0.0.0 --port=8000
```

---

## 📚 **Structure des Fichiers**

```
mercuriale-app/
├── app/
│   ├── Enums/
│   │   ├── PriceType.php
│   │   ├── SaleType.php
│   │   └── UserRole.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/ (8 controllers)
│   │   │   ├── Auth/ (8 controllers)
│   │   │   └── Web/ (8 controllers)
│   │   ├── Middleware/
│   │   │   └── AdminMiddleware.php
│   │   ├── Requests/ (18 request classes)
│   │   └── Resources/ (8 API resources)
│   │   └── Requests/ (20 request classes)
│   ├── Livewire/
│   │   └── ArticleTable.php
│   └── Models/ (10 models)
├── database/
│   ├── migrations/ (16 migrations)
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
│   │   ├── auth/ (6)
│   │   ├── profile/ (4)
│   │   ├── livewire/ (1)
│   │   ├── components/ (12)
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
- ✅ Systeme d'authentification avec roles (admin/user)
- ✅ API REST complete avec Sanctum
- ✅ Gestion CRUD pour toutes les entites
- ✅ Relations entre modeles
- ✅ Validation des donnees
- ✅ Historique des prix automatique
- ✅ Calcul automatique des prix par unite et par conditionnement
- ✅ Gestion des prix de vente par article (SellingPrice)
- ✅ Calcul automatique du ratio de vente (prix d'achat * TVA / prix de vente TTC)
- ✅ Seeders pour les donnees de test

### **Frontend**
- ✅ Interface utilisateur complete
- ✅ Design responsive avec Tailwind CSS
- ✅ Interactivite avec Alpine.js
- ✅ Composants Livewire
- ✅ Dark mode support
- ✅ Tableaux de donnees avec pagination
- ✅ Formulaires avec validation visuelle
- ✅ Section prix de vente avec toggle quantite conditionnelle
- ✅ Affichage du ratio de vente par fournisseur avec coloration

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

**Derniere mise a jour:** 19 juin 2026  
**Version:** 1.6.1  
**Auteur:** Mistral Vibe (avec co-auteur)  
**Repository:** ssh://git@laboiteauxlettres.com:5196/mnt/srv/git/stock.git  
**Commits:** 16
