# Proposition de nouvelle base de données

## Système de Gestion de la Fiscalité des Collectivités Territoriales

Ce document décrit la base cible (`fiscctcidb_v2`) proposée en remplacement du schéma hérité `fiscctcidb` (MySQL/WinDev, 2021). Il accompagne le fichier DDL `fiscct_schema_v2.sql`.

---

## 1. Pourquoi une refonte

Le schéma d'origine remplit sa fonction mais présente des faiblesses structurelles qui freinent la fiabilité fiscale, l'évolutivité et la maintenance. La nouvelle base les corrige sans rien perdre du périmètre fonctionnel décrit dans `FONCTIONNALITES_APPLICATION.md`.

| Problème dans l'ancienne base | Risque | Correction proposée |
|---|---|---|
| Montants stockés en `double` (`mont_taxe_an`, `mont_regl_taxe`, `capital_social`…) | Erreurs d'arrondi sur les calculs de taxe et de recouvrement | Type `NUMERIC` partout (15,2 pour les montants, 18,2 pour les CA) |
| Clés primaires = codes métier en `varchar` (`num_id_contb`, `code_coll`…) | Codes figés, jointures lourdes, refonte difficile | Clé technique `id bigint` + code métier conservé en colonne `UNIQUE` |
| Encodage MySQL `utf8` (utf8mb3, UTF-8 incomplet) | Caractères perdus | UTF-8 complet (PostgreSQL natif) |
| Intégrité référentielle partielle (beaucoup de tables sans FK) | Données orphelines, incohérences | Toutes les clés étrangères déclarées |
| Photos et logos en `longblob` dans la base | Base volumineuse, sauvegardes lentes | Externalisation : colonnes `photo_uri` / `logo_uri` (stockage objet) |
| Module de sécurité spécifique WinDev (`gpw*`) | Verrouillage technologique, pas de gestion fine des droits | RBAC standard : `utilisateur` / `role` / `permission` |
| Audit ad hoc (`etab_taxe_emis_audit` seulement) | Traçabilité partielle | `audit_log` générique (JSONB) + colonnes `created_at/by`, `updated_at/by` |
| `rue`, `avenue`, `bvd` en trois tables quasi identiques | Redondance | Table unique `voie` avec un `type_voie` |
| Pas de suppression logique | Perte d'historique fiscal | `supprime_le` / `statut` sur les entités sensibles |

---

## 2. Choix techniques

- **SGBD cible : PostgreSQL 16+.** Recommandé pour l'intégrité transactionnelle, le type `NUMERIC` exact, le partitionnement par exercice fiscal, le JSONB (audit) et la sécurité au niveau ligne (RLS) pour le multi-collectivité. Une variante MySQL 8 / MariaDB reste possible si l'on souhaite limiter le changement d'infrastructure.
- **Nomenclature en français, en toutes lettres** (`contribuable`, `etablissement`, `emission_taxe`…) : le système et les équipes sont francophones, on évite les abréviations cryptiques (`contb`, `etablis`, `regl_tf`) tout en restant dans le domaine métier.
- **Convention uniforme** : table au singulier, `snake_case`, PK technique `id`, code métier dans `code` ou `numero`, dates de saisie via les colonnes d'audit.
- **Multi-collectivité** : colonne `collectivite_id` sur toutes les tables transactionnelles, isolation activable par Row Level Security.

---

## 3. Organisation du modèle (10 domaines)

1. **Référentiel territorial** — `pays`, `nationalite`, `district`, `region`, `departement`, `sous_prefecture`, `commune`, `quartier`, `voie`, `zone_fiscale` : hiérarchie complète avec FK en cascade descendante.
2. **Collectivités & organisation** — `type_collectivite`, `recette`, `collectivite`, `departement_service`, `service`, `organisation`.
3. **Agents & sécurité (RBAC)** — `grade_agent`, `fonction_agent`, `agent`, `utilisateur`, `role`, `permission`, `role_permission`, `utilisateur_role`, `journal_connexion`, `audit_log`.
4. **Contribuables & dirigeants** — `contribuable` (personne physique + morale unifiées), `coordonnee_bancaire`, `banque`, `forme_juridique`, `regime_imposition`, `qualite_dirigeant`, `dirigeant`.
5. **Activités & établissements** — `secteur_activite`, `categorie_activite`, `activite`, `etablissement`.
6. **Paramétrage fiscal** — `domaine_taxe`, `categorie_impot_taxe`, `periodicite`, `nature_taxe`, `bareme_taxe`, `exercice_fiscal`, `categorie_cotisation_fonciere`, `bareme_cotisation_fonciere`, `obligation`.
7. **Émission & recouvrement** — `mode_reglement`, `type_reglement`, `emission_taxe`, `emission_cotisation_fonciere`, `reglement_taxe`.
8. **Dossiers administratifs** — `famille_etat_dossier`, `categorie_etat_dossier`, `dossier`, `historique_dossier`.
9. **Convocations, contrôle & exonérations** — `convocation`, `sanction_fiscale`, `type_exoneration`, `exoneration`, `ligne_exoneration`.
10. **Pilotage & paramétrage transverse** — `objectif`, `parametre_application`.

---

## 4. Principales décisions de modélisation

- **Contribuable unifié.** Personne physique et personne morale dans une seule table `contribuable`, discriminées par `type_personne` (`PP`/`PM`), avec des contraintes `CHECK` garantissant les champs obligatoires de chaque type (nom pour une PP, raison sociale pour une PM). Cela simplifie les recherches multicritères et les jointures.

- **Fusion `rue`/`avenue`/`bvd`** en une table `voie` avec `type_voie`, rattachée au `quartier`.

- **Règlement polymorphe maîtrisé.** `reglement_taxe` peut viser soit une `emission_taxe`, soit une `emission_cotisation_fonciere` (taxe foncière), avec une contrainte `CHECK` imposant exactement l'une des deux cibles — l'ancienne table `regl_tf` distincte est ainsi absorbée proprement.

- **Sécurité repensée.** Le module WinDev `gpw*` (configuration, éléments, historique) est remplacé par un modèle rôle/permission classique, plus un `journal_connexion` propre. Les mots de passe sont stockés hachés (`pgcrypto`).

- **Audit généralisé.** Une table `audit_log` (avant/après en JSONB) trace toute modification, complétée par `created_at/by` et `updated_at/by` (avec trigger automatique sur `updated_at`).

---

## 5. Correspondance ancien → nouveau (extrait)

| Table d'origine | Devient | Remarque |
|---|---|---|
| `contb` | `contribuable` | PP + PM unifiées, `photo_contb` → `photo_uri` |
| `etablis` | `etablissement` | localisation via FK propres |
| `rue` / `avenue` / `bvd` | `voie` | fusionnées |
| `param_taxe` | `bareme_taxe` | montants en `numeric` |
| `cotisat_tf` | `bareme_cotisation_fonciere` | |
| `etab_taxe_emis` (+ `_audit`) | `emission_taxe` (+ `audit_log`) | audit généralisé |
| `etab_cotis` | `emission_cotisation_fonciere` | |
| `regl_taxe` + `regl_tf` | `reglement_taxe` | unifiées |
| `exo_part` / `ligne_exo_part` | `exoneration` / `ligne_exoneration` | |
| `grp_util` / `gpw*` | `role` / `permission` / `journal_connexion` | RBAC |
| `entete_etat`, `config_fen`, `modul_appli` | `parametre_application` | config générique |

---

## 6. Étapes de migration recommandées

1. Créer `fiscctcidb_v2` à partir du DDL fourni.
2. Charger les référentiels (territoire, activités, natures de taxe, formes juridiques) en générant les `id` techniques et en conservant les anciens codes dans `code`.
3. Migrer les contribuables et établissements via des tables de correspondance (`ancien_code → nouvel id`).
4. Migrer le transactionnel (émissions, règlements, dossiers, exonérations) en résolvant les FK par les tables de correspondance.
5. Externaliser les blobs (`photo_contb`, `logo`) vers le stockage objet et renseigner les URI.
6. Recréer les comptes dans le modèle RBAC, réinitialiser les mots de passe (hash).
7. Contrôles de cohérence : totaux émis/recouvrés par exercice avant/après migration.

---

## 7. Points à confirmer avec vous

- **SGBD définitif** : PostgreSQL (recommandé) ou maintien sur MySQL 8 / MariaDB ? Je peux fournir la variante MySQL du DDL.
- **Stockage des pièces jointes** : système de fichiers, S3/MinIO, ou conservation en base ?
- **Granularité des permissions** souhaitée (par module, par action, par collectivité ?).
- Faut-il **conserver à l'identique les anciens codes** (compatibilité éditions/états) ou peut-on les régénérer ?
