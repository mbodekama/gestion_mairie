# CLAUDE.md

Guide du projet pour Claude Code. Lis ce fichier avant toute tâche.

## Présentation

Refonte d'un **backoffice web interne de gestion de la fiscalité des collectivités territoriales** (Côte d'Ivoire). Outil **destiné à la mairie et à ses agents** : recensement des contribuables et établissements, paramétrage et émission des taxes locales, recouvrement, dossiers, contrôle fiscal. **Pas de front office public.** Langue du domaine et du code métier : **français**.

## Pile technique

- **PHP 8.4** (minimum 8.3), **Laravel 13** (monolithe MVC rendu côté serveur).
- Interface backoffice : **Filament 3** (sur Livewire) ; écrans métier spécifiques en **Blade + Livewire**.
- ORM : **Eloquent**.
- RBAC : **spatie/laravel-permission**.
- Auth : sessions Laravel (pas de JWT — application interne).
- PDF : **barryvdh/laravel-dompdf** (quittances, avis, convocations, rôles).
- Base : **PostgreSQL 16**. Assets : **Vite**. Serveur : **Nginx + PHP-FPM**.

## Structure du dépôt (cible Laravel)

```
CLAUDE.md              ← CE FICHIER, À LA RACINE du dépôt (lu automatiquement par Claude Code)
composer.json
/app
  /Models              Modèles Eloquent (un par table, nom de table déclaré)
  /Services            Logique métier (calcul taxe, liquidation, recouvrement)
  /Http/Controllers    Controllers
  /Http/Requests       Validation des saisies
  /Livewire            Composants Livewire des écrans métier
  /Filament            Resources Filament (CRUD)
  /Policies            Contrôle d'accès
/database
  /migrations          Schéma (versionné)
  /seeders             Référentiels, barèmes, sécurité, démo
/db                    Scripts SQL de référence d'origine (schéma, seeds)
/docs                  Cahier des charges, architecture (les autres documents de conception)
/resources/views       Vues Blade
```

> Important : `CLAUDE.md` reste **à la racine du projet**, jamais dans un sous-dossier — c'est l'emplacement où Claude Code le lit par défaut.

## Base de données — règles impératives

- Schéma cible `fiscctcidb_v2` (PostgreSQL). Scripts de référence dans `/db` :
  `fiscct_schema_v2.sql`, `fiscct_seed_referentiel.sql`, `fiscct_seed_baremes.sql`,
  `fiscct_seed_securite.sql`, `fiscct_demo_test_emission.sql`.
- **Montants TOUJOURS en `NUMERIC`** côté base, **cast `decimal`** côté Eloquent. **Jamais de calcul monétaire sur un float PHP** : utiliser `bcmath` ou `brick/money`.
- Tables au **singulier, en français, snake_case** (`emission_taxe`, `montant_annuel`). Comme Eloquent attend par défaut un pluriel anglais, **déclarer `protected $table`** (ou l'attribut `#[Table(...)]` de Laravel 13) sur chaque modèle.
- Chaque table a une clé technique `id` ; le code métier va dans `code`/`numero` (`UNIQUE`).
- Toute table métier porte `created_at`, `updated_at` et, si pertinent, `created_by`, `updated_by`.
- Toute évolution de schéma = **nouvelle migration**. Ne jamais modifier une migration déjà appliquée.

## Conventions de code

- Logique métier dans des **classes Service**, jamais dans les vues, composants Livewire ou Filament Resources.
- Validation systématique des entrées via **Form Requests**.
- Respecter les conventions Laravel (nommage, structure), code et commentaires métier en français.
- Référentiels et données de démarrage via **seeders** dédiés.

## Sécurité — à respecter systématiquement

- Aucune route métier accessible sans authentification.
- Autorisation **par action** via spatie : vérifier la permission (`EMISSION_LIQUIDER`, `RECOUVR_ENCAISSER`…) sur chaque écran/endpoint sensible (middleware `can:` ou Policies).
- Le catalogue de permissions et les rôles définis en Phase 1 (`fiscct_seed_securite.sql`) sont la référence ; les tables RBAC sont alignées sur les conventions de spatie.
- Mots de passe hachés (jamais en clair, jamais loggués). Protection CSRF Laravel active.
- Respecter la portée par `collectivite_id` (mono-mairie aujourd'hui, multi-collectivité prévu).
- Tracer les créations/modifications/suppressions sensibles dans l'audit.

## Règles métier clés

- Une émission est rattachée à un **exercice fiscal ouvert** ; interdire toute opération sur un exercice `cloture = true`.
- Le montant d'une taxe se calcule selon le barème applicable (tranche de CA, périodicité, zone pour le foncier) avec prorata temporis si nécessaire.
- Un règlement vise **soit** une émission de taxe, **soit** une cotisation foncière (jamais les deux) ; solde dû = montant émis − somme des règlements.

## Commandes (Laravel)

- Installer : `composer install && npm install && npm run build`
- Configurer : copier `.env`, renseigner la connexion PostgreSQL, `php artisan key:generate`
- Migrer + amorcer : `php artisan migrate --seed`
- Lancer en dev : `php artisan serve` (ou via Nginx/PHP-FPM)
- Tests : `php artisan test`
- Caches : `php artisan optimize:clear`

## Glossaire métier (français)

- **Contribuable** : personne physique (PP) ou morale (PM) imposable.
- **Établissement** : unité d'exploitation rattachée à un contribuable.
- **Émission** : calcul et mise en recouvrement d'une taxe.
- **Liquidation** : détermination du montant exact dû.
- **Recouvrement / règlement** : encaissement effectif.
- **Patente** : principale taxe sur l'activité (proportionnelle au CA).
- **Taxe foncière (TF)** : cotisation sur le patrimoine bâti, barème par zone.
- **Exercice fiscal** : année budgétaire ; ouvert ou clôturé.
- **Collectivité** : entité territoriale (mairie, district…) gérant la fiscalité.

## À ne pas faire

- Ne pas stocker un montant en flottant, ni calculer un montant avec des floats PHP.
- Ne pas utiliser un code métier comme clé primaire.
- Ne pas modifier une migration déjà livrée.
- Ne pas contourner la vérification des permissions.
- Ne pas réintroduire la nomenclature abrégée de l'ancienne base (`contb`, `regl_tf`…).
- Ne pas construire de front office public : l'outil est un backoffice interne.
