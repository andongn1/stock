# Prompt Mistral Vibe CLI — Corrections stock-app (Symfony 8.1)

## Contexte

`stock-app/` (Symfony 8.1) est la conversion de `mercuriale-app/` (Laravel). Deux audits
ont identifié 9 bugs. Corrige-les TOUS, un par un, dans cet ordre. Ne réécris pas le
reste du projet : ce qui existe est bon.

**Important** : le bug n°1 fait planter TOUTES les pages (le partial de navigation est
inclus partout). Tant qu'il n'est pas corrigé, aucune autre vérification n'est possible.
C'est probablement pour cela que les tests précédents n'ont rien détecté : la première
page rendue crashe.

## Bugs à corriger

### 1. BLOQUEUR TOTAL — `app.request.route` n'existe pas en Symfony (crash de toutes les pages)

Erreur constatée au lancement du serveur :

```
Twig\Error\RuntimeError:
Neither the property "route" nor one of the methods "route()", "getroute()"...
exist... in class "Symfony\Component\HttpFoundation\Request"
in "partials/navigation.html.twig" at line 15
```

`templates/partials/navigation.html.twig` utilise `app.request.route` (7 occurrences,
lignes 15, 20, 25, 30, 35, 40, 46) pour surligner le lien actif. C'est une habitude
Laravel (`request()->route()->getName()`) : en Symfony, l'objet `Request` n'a pas de
propriété `route`.

**Correction** — remplacer partout :

```twig
{{ app.request.route == 'dashboard' ? ... }}
```

par :

```twig
{{ app.request.attributes.get('_route') == 'dashboard' ? ... }}
```

(ou `app.request.get('_route')`). Puis vérifier dans TOUT le dossier `templates/` qu'il
ne reste aucun autre `app.request.route` ou autre construction Laravel inexistante
(`auth()`, `session('...')`, `old('...')`, etc.). Charger `/login` : la page doit
s'afficher sans erreur.

### 2. NOUVEAU BUG — POST /api/login est cassé

`src/Security/TokenAuthenticator.php::supports()` renvoie `true` pour tout chemin
`/api/*`, y compris `/api/login`. Un client qui appelle `/api/login` avec email+password
reçoit donc un 401 "Token not provided" AVANT que le contrôleur ne s'exécute.

**Correction** :

```php
public function supports(Request $request): ?bool
{
    return str_starts_with($request->getPathInfo(), '/api/')
        && $request->headers->get('Authorization') !== null;
}
```

Ainsi, une requête sans en-tête Authorization (ex : `/api/login`) passe en anonyme
jusqu'au contrôleur, comme en Laravel Sanctum. Vérifie ensuite avec curl :
- `POST /api/login` sans Bearer → 200 + token
- `GET /api/articles` avec `Authorization: Bearer <token>` → 200
- `GET /api/articles` sans token → 401

### 3. Routes sub-familles : crash Twig

Les templates `templates/sub_families/*.html.twig` appellent `path('sub_families.index')`,
`path('sub_families.create')`, `path('sub_families.store')` mais le contrôleur définit
`sub-familles.*` (avec tiret). Harmonise : renomme les routes du contrôleur en
`sub_families.*` OU corrige les templates en `sub-familles.*`. Une seule convention,
appliquée partout (templates + contrôleurs). Teste ensuite que `/sub-familles`
s'affiche sans erreur.

### 4. Enregistrement (register) manquant

Laravel Breeze fournit `GET /register` et `POST /register` (`RegisteredUserController`).
Il n'existe ni route ni contrôleur Symfony, alors que
`templates/auth/register.html.twig` existe et poste vers `path('register')` → erreur
"route inconnue". Crée un contrôleur d'enregistrement :
- `GET /register` : rend le template existant
- `POST /register` : valide (name requis, email requis + unique sur User, mot de passe
  confirmé min 8), hashe le mot de passe, persiste, connecte l'utilisateur, redirige
  vers `/dashboard` — mêmes règles et messages que
  `mercuriale-app/app/Http/Controllers/Auth/RegisteredUserController.php`.

### 5. Profile : implémenter update et destroy

`src/Controller/Web/ProfileController.php` : `update()` et `destroy()` sont des stubs
TODO. Reprends la logique de
`mercuriale-app/app/Http/Controllers/ProfileController.php` :
- `PATCH /profile` : met à jour name/email (email unique, message d'erreur si email déjà
  utilisé, re-login si l'email change), flash success, redirect vers `profile.edit`
- `DELETE /profile` : déconnecte, supprime le compte, redirige vers la page d'accueil

### 6. Flux d'authentification : implémenter les stubs TODO

Ces contrôleurs contiennent `// TODO: Implement` — implémente-les en reprenant la logique
Laravel (fichiers équivalents dans `mercuriale-app/app/Http/Controllers/Auth/`) :
- `PasswordResetLinkController` (create/store) : génère un token dans
  `password_reset_tokens`, envoie l'email (Mailer), mêmes messages de statut
- `NewPasswordController` (create/store) : valide le token, réinitialise le mot de passe,
  supprime le token
- `ConfirmablePasswordController` (show/store) : confirme le mot de passe courant
  (session `auth.password_confirmed_at`), comme Breeze
- `PasswordController` (update) : changement de mot de passe depuis le profil
- `VerifyEmailController` + `EmailVerificationPromptController` +
  `EmailVerificationNotificationController` : vérification email complète (signed URL,
  marque `email_verified_at`)

### 7. Remember me inactif

Le formulaire de login a `_remember_me` mais le firewall `main` de
`config/packages/security.yaml` n'a pas de config `remember_me`. Ajoute :

```yaml
            remember_me:
                secret: '%env(APP_SECRET)%'
                lifetime: 604800
                always_remember_me: false
```

et vérifie que la case à cocher fonctionne.

### 8. Tests

Il n'existe que `tests/bootstrap.php`. Crée une suite de tests (KernelTestCase /
WebTestCase) couvrant au minimum : page de login accessible, login avec identifiants
fixtures, redirection `/` vers `/login` si non connecté, rendu de la navigation sur
chaque page (régression du bug n°1), CRUD famille en admin, `/api/login` +
`/api/articles` avec token, accès `/users` refusé sans ROLE_ADMIN. Reprends les cas de
`mercuriale-app/tests/`. Tous les tests doivent passer avec `php bin/phpunit`.

### 9. Sécurité : credentials commités

`stock-app/.env` est committé dans git avec de vrais identifiants MySQL. Ne le corrige
PAS toi-même côté base (l'utilisateur doit d'abord changer son mot de passe MySQL), mais :
- `git rm --cached stock-app/.env`
- rétablis un `.env` avec uniquement des valeurs par défaut commentées, comme le
  squelette Symfony
- documente dans le README qu'il faut créer `.env.local` (gitignoré) avec `DATABASE_URL`
- préviens explicitement l'utilisateur dans ton rapport final qu'il doit changer son mot
  de passe MySQL car l'ancien reste dans l'historique git

## Vérification finale (tout doit passer, dans cet ordre)

1. `composer install` puis `php bin/console about` — OK
2. `php bin/console cache:clear` puis charger `/login` dans le navigateur : la page
   s'affiche sans erreur Twig (régression du bug n°1)
3. `php bin/console debug:router` — routes `register`, `sub_families`/`sub-familles`
   cohérentes avec les templates ; aucune référence de template vers une route
   inexistante (grep croisé `path('...')` dans `templates/` vs `name: '...'` dans
   `src/Controller/`)
4. Migrations + fixtures : `php bin/console doctrine:database:create`,
   `php bin/console doctrine:migrations:migrate`,
   `php bin/console doctrine:fixtures:load`
5. `php bin/phpunit` — tous les tests verts
6. Smoke test HTTP complet : `/login`, `/register`, `/dashboard` (connecté),
   `/sub-familles`, `/families`, `/articles`, `/profile` (update), `/api/login` +
   `/api/articles` avec Bearer, `/users` refusé en non-admin
7. Mets à jour `REVIEW_FOR_MISTRAL.md` : ne coche que ce qui est réellement vérifié,
   avec la commande exacte utilisée pour chaque vérification.

## Règles

- Ne modifie jamais `mercuriale-app/`.
- Ne crée pas la base de données de l'utilisateur : indique-lui seulement les commandes.
- Si une correction en casse une autre, reviens en arrière et reprends.
- Si tu bloques plus de deux fois sur un point, arrête-toi et rapporte le problème exact
  avec le message d'erreur complet.
