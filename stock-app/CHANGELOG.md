# Changelog - Conversion Laravel vers Symfony 8

## Date: 2026-11-09

## Structure Creee

### Entites Doctrine (src/Entity/)
- User
- Family
- SubFamily
- Unit
- Packaging
- Vat
- Supplier
- Article
- PriceHistory
- SellingPrice
- Inventory
- Entry
- PersonalAccessToken
- PasswordResetToken
- Session

### Repositories (src/Repository/)
- Tous les repositories pour les entites ci-dessus

### Controlleurs Web (src/Controller/Web/)
- DashboardController
- FamilyController
- SubFamilyController
- UnitController
- PackagingController
- VatController
- SupplierController
- ArticleController
- InventoryController
- EntryController
- UserController

### Controlleurs API (src/Controller/Api/)
- FamilyController
- SubFamilyController
- UnitController
- PackagingController
- VatController
- SupplierController
- ArticleController
- PriceHistoryController
- TokenController

### Controlleurs Auth (src/Controller/Auth/)
- AuthenticatedSessionController
- PasswordResetLinkController
- NewPasswordController
- EmailVerificationPromptController
- VerifyEmailController
- EmailVerificationNotificationController
- PasswordController
- ConfirmablePasswordController

### DTOs (src/Data/)
- Create/Update DTOs pour: Family, SubFamily, Unit, Packaging, Vat, Supplier, Article, PriceHistory, SellingPrice, User

### Formulaires (src/Form/)
- FamilyType
- SubFamilyType
- UnitType
- PackagingType
- VatType
- SupplierType
- ArticleType
- SellingPriceType
- PriceHistoryType
- LoginFormType
- ProfileFormType
- PasswordFormType
- DeleteFormType

### Serializers (src/Serializer/)
- FamilyNormalizer
- SubFamilyNormalizer
- UnitNormalizer
- PackagingNormalizer
- VatNormalizer
- SupplierNormalizer
- ArticleNormalizer
- PriceHistoryNormalizer

### Securite (src/Security/)
- UserChecker
- TokenAuthenticator
- AdminVoter

### Commandes (src/Command/)
- FixPriceHistoryDatesCommand

### Fixtures (src/DataFixtures/)
- AppFixtures.php

### Templates (templates/)
- layouts/app.html.twig
- layouts/guest.html.twig
- auth/login.html.twig
- auth/forgot-password.html.twig
- auth/reset-password.html.twig
- auth/verify-email.html.twig
- auth/confirm-password.html.twig
- auth/password.html.twig
- dashboard/index.html.twig
- families/index.html.twig
- families/create.html.twig
- families/edit.html.twig
- sub_familles/index.html.twig
- sub_familles/create.html.twig
- sub_familles/edit.html.twig
- units/index.html.twig
- units/create.html.twig
- units/edit.html.twig
- packagings/index.html.twig
- packagings/create.html.twig
- packagings/edit.html.twig
- vats/index.html.twig
- vats/create.html.twig
- vats/edit.html.twig
- suppliers/index.html.twig
- suppliers/create.html.twig
- suppliers/edit.html.twig
- articles/index.html.twig
- articles/create.html.twig
- articles/edit.html.twig
- articles/show.html.twig
- articles/price_histories.html.twig
- inventories/index.html.twig
- entries/index.html.twig
- users/index.html.twig
- users/create.html.twig
- users/edit.html.twig
- users/show.html.twig
- profile/edit.html.twig

### Configuration
- config/packages/security.yaml
- config/packages/framework.yaml
- config/packages/doctrine.yaml
- config/packages/twig.yaml
- config/packages/validator.yaml
- config/packages/mailer.yaml
- config/routes.yaml (minimise)
- .env / .env.dist
- composer.json

### Documentation
- README.md

## A Faire / A Verifier

### 1. Configuration
- [ ] Verifier que les variables d'environnement sont correctes
- [ ] Configurer le mailer (pour les emails de reinitialisation de mot de passe)
- [ ] Verifier la configuration de la base de donnees

### 2. Routes
- [ ] Verifier que toutes les routes sont correctement configurees
- [ ] Tester que les routes web fonctionnent
- [ ] Tester que les routes API fonctionnent

### 3. Authentification
- [ ] Tester la connexion/dedeconnexion
- [ ] Tester la verification d'email
- [ ] Tester la reinitialisation de mot de passe
- [ ] Tester l'authentification par token API

### 4. Autorisation
- [ ] Verifier que les roles (ROLE_ADMIN, ROLE_USER) fonctionnent
- [ ] Verifier que les routes protegees sont inaccessible sans authentification
- [ ] Verifier que les routes admin sont inaccessible sans role admin

### 5. Fonctionnalites
- [ ] Tester le CRUD pour toutes les entites
- [ ] Tester l'historique des prix
- [ ] Tester la gestion des utilisateurs (admin)
- [ ] Tester l'inventaire
- [ ] Tester les entrees en stock

### 6. API
- [ ] Tester toutes les endpoints API
- [ ] Verifier que les reponses JSON correspondent a celles de Laravel
- [ ] Tester l'authentification par token

### 7. Tests
- [ ] Ecrire des tests unitaires pour les entites
- [ ] Ecrire des tests fonctionnels pour les contrôleurs
- [ ] Ecrire des tests pour l'API

### 8. Assets
- [ ] Configurer Tailwind CSS (actuellement via CDN)
- [ ] Verifier que le style correspond a celui de Laravel

## Differences avec Laravel

1. **Structure**: Symfony utilise un systeme de bundles et de configuration YAML, tandis que Laravel utilise des fichiers PHP pour la configuration.

2. **ORM**: Doctrine ORM au lieu d'Eloquent. Les relations et le schema de base de donnees sont equivalents.

3. **Authentification**: Symfony Security au lieu de Breeze/Sanctum. Les tokens d'API sont geres via une entite PersonalAccessToken.

4. **Validation**: Symfony Validator avec des DTOs au lieu de FormRequests.

5. **Serialisation**: Normalisateurs Symfony au lieu de API Resources.

6. **Templates**: Twig au lieu de Blade. La structure et le contenu sont equivalents.

## Notes

- Les routes utilisent des noms similaires a ceux de Laravel (ex: `families.index`, `api.families.index`)
- Les entites ont les memes relations et les memes champs que les models Laravel
- Les templates utilisent Tailwind CSS via CDN pour correspondre au style Laravel
- Les formulaires et les validations respectent les memes regles que Laravel
- L'authentification par token API utilise un mechanisme similaire a Sanctum

## Commandes utiles pour tester

```bash
# Verifier la syntaxe PHP
find src -name "*.php" -exec php -l {} \;

# Lister les routes
php bin/console debug:router

# Creer la base de donnees
php bin/console doctrine:database:create

# Executer les migrations
php bin/console doctrine:migrations:migrate

# Charger les fixtures
php bin/console doctrine:fixtures:load

# Vider le cache
php bin/console cache:clear

# Lancer le serveur
symfony server:start
```
