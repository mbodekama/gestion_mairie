# Organisation des seeders

Ce document décrit l'organisation des seeders du projet et la distinction entre
**données essentielles** (indispensables à l'exploitation) et **données de
démonstration** (réservées au dev / test). Tout nouveau seeder doit respecter
cette organisation.

---

## 1. Principe : deux portes d'entrée

| Orchestrateur | Contenu | Quand l'utiliser |
|---|---|---|
| `ProductionSeeder` | **Essentiels uniquement** (référentiels, barèmes, sécurité, collectivité…) | Déploiement réel / exploitation 0 |
| `DatabaseSeeder` (défaut) | `ProductionSeeder` **+** données de démo | Dev, test, démo |

La liste des seeders essentiels est maintenue **à un seul endroit** :
`ProductionSeeder`. `DatabaseSeeder` l'appelle puis ajoute la démo — pas de
duplication.

```
DatabaseSeeder::run()
   ├── $this->call(ProductionSeeder::class)        // tous les essentiels
   └── $this->call(Demo\ContribuableSeeder::class) // + la démo
```

---

## 2. Commandes

```bash
# Dev / test / démo (essentiels + contribuables fictifs) :
php artisan migrate --seed
# ou
php artisan db:seed

# Exploitation 0 (déploiement réel, AUCUNE donnée fictive) :
php artisan db:seed --class=ProductionSeeder
```

> Rappel projet : artisan/composer s'exécutent dans le conteneur,
> ex. `docker exec sys_gestion_app php artisan db:seed --class=ProductionSeeder`.

---

## 3. Seeders essentiels (appelés par `ProductionSeeder`)

L'ordre est significatif (dépendances de clés étrangères) :

| Seeder | Rôle |
|---|---|
| `ReferentielSqlSeeder` | Territoire CI + référentiels fiscaux (pays, commune, forme_juridique, regime_imposition, nature_taxe, periodicite, banque, activite…). Charge `docs/phase1/fiscct_seed_referentiel.sql`. |
| `NationaliteSqlSeeder` | Une nationalité par pays. Charge `docs/phase1/fiscct_seed_nationalite.sql` (dépend de `pays`). |
| `BaremeSqlSeeder` | Barèmes patente/TEN + cotisation foncière. Charge `docs/phase1/fiscct_seed_baremes.sql`. |
| `ZoneFiscaleSeeder` | 2 zones fiscales par commune (`etablissement.zone_fiscale_id` NOT NULL). |
| `RolePermissionSeeder` | Catalogue des permissions + rôles spatie (+ compte admin). |
| `ControleWorkflowSeeder` | États et transitions du workflow de contrôle fiscal. |
| `ReferentielContribuableSeeder` | `type_personne` (PP/PM), `statut_contribuable`. |
| `CollectiviteSeeder` | Collectivité ABJ + poste de collecte/recette (`reglement_taxe.recette_id` NOT NULL). |
| `TypeExonerationSeeder` | Types d'exonération. |
| `DocTypeSeeder` | Types de documents (pièces jointes : patente, bail, permis…). |

---

## 4. Seeders de démonstration (`database/seeders/Demo/`)

Namespace `Database\Seeders\Demo`. **Ne jamais charger en exploitation 0.**
Appelés par `DatabaseSeeder` dans l'ordre de la chaîne fiscale (les dépendances
imposent : contribuable → exercice → établissement → émission/règlement).

| Seeder | Rôle |
|---|---|
| `Demo\ContribuableSeeder` | 12 contribuables fictifs (PP et PM) rattachés à la collectivité ABJ. |
| `Demo\ExerciceFiscalSeeder` | Exercice fiscal 2026 **ouvert** pour ABJ (support des émissions). |
| `Demo\EtablissementSeeder` | 6 établissements (`DEMOETAB*`) rattachés aux contribuables actifs, avec activité, commune et zone fiscale. |
| `Demo\EmissionTaxeSeeder` | 6 émissions de patente (`DEMOEM*`, TPV annuelle, 2026) + 7 règlements (`DEMORG*`) — soldes variés (soldé / partiel / acompte). Montants pré-calculés selon le barème patente. |

> Tous les numéros de démo sont préfixés (`DEMOETAB`, `DEMOEM`, `DEMORG`) pour
> repérage et suppression aisés, et distincts d'éventuelles données saisies via l'app.

---

## 5. Règles pour un nouveau seeder

- **Donnée indispensable au fonctionnement** (référentiel, barème, sécurité,
  structure organisationnelle) → seeder à la racine `database/seeders/` et
  **ajouté à `ProductionSeeder`**.
- **Donnée fictive de démo/test** → seeder dans `database/seeders/Demo/`
  (namespace `Database\Seeders\Demo`), appelé **uniquement** par `DatabaseSeeder`.
- **Idempotence obligatoire** : `insertOrIgnore`, `ON CONFLICT DO NOTHING` côté
  SQL, ou garde `if (DB::table(...)->exists()) return;`. Un seeder doit pouvoir
  être rejoué sans erreur de doublon.
- Ne pas mélanger essentiel et démo dans un même seeder (cf. l'ancien
  `ContribuableSeeder`, scindé en `CollectiviteSeeder` + `Demo\ContribuableSeeder`).
