# Système de Gestion de la Fiscalité des Collectivités Territoriales

> Document de synthèse fonctionnelle, établi à partir de l'analyse du modèle de données
> (`fiscctcidb_fichier_base_13052021.sql`) et des interfaces des projets WinDev
> `sys_fisc_coll_territoriale` (ERP-FIDELO, exécutable Windows natif) et
> `RtfciFinanceCollTerritoriale` (déclinaison Java multiplateforme).

## 1. Présentation générale

L'application est un **ERP de fiscalité locale** destiné aux collectivités territoriales
(mairies, districts…), probablement en Côte d'Ivoire au vu du référentiel géographique
embarqué (198 communes, 473 sous-préfectures, 108 départements, 33 régions, 14 districts).

Elle permet de **recenser les contribuables et leurs établissements, calculer et émettre
les taxes locales, suivre leur recouvrement, gérer les dossiers administratifs et le
contrôle fiscal**, le tout appuyé sur un référentiel territorial détaillé et un module
de sécurité/traçabilité des utilisateurs.

Architecture applicative observée (fenêtres MDI) : trois piliers —
**Gestion Fiscale** (`FEN_MDI_GESTFISC`), **Gestion des Communes / référentiel territorial**
(`FEN_MDI_GESTCOMM`) et **Gestion Financière & Comptable** (`FEN_MDI_GESTFINCOMPTA`).

---

## 2. Modules et fonctionnalités

### 2.1 Gestion des contribuables et de leurs établissements
Recensement et suivi des contribuables (personnes physiques ou morales) et des
établissements qu'ils exploitent.

- Fiche contribuable personne physique / société (état civil, pièce d'identité, filiation,
  contacts, photo) — tables `contb`, `contb_banq`
- Fiche dirigeant et qualité du dirigeant — tables `dirigeant`, `qlte_dirig`
- Fiche établissement : localisation précise (quartier, rue, avenue, boulevard, zone),
  activité exercée, surface, dates de début/cessation/transfert/sommeil — table `etablis`
- Référentiel des activités économiques : secteurs, catégories, nature — tables
  `activite`, `secteur_activite`, `categ_activite`
- Recherches multicritères (nom, prénom, raison sociale, sigle, numéro de compte
  contribuable…)

### 2.2 Paramétrage de la fiscalité
Configuration des règles de calcul des taxes appliquées par les collectivités.

- Natures, catégories et domaines d'impôts/taxes — tables `nature_taxe`,
  `categ_impot_taxe`, `domaine_taxe`, `regime_imp`, `periodicite`
- Barèmes et tranches de taxation par activité et chiffre d'affaires — table `param_taxe`
- Taxe foncière : cotisations par zone et par tranche, catégories de cotisation —
  tables `cotisat_tf`, `categ_cotis_tf`, `zone_cotis`, `regl_tf`
- Exercices fiscaux (ouverture/clôture) — table `exer`

### 2.3 Émission et recouvrement des taxes
Calcul, émission et encaissement des taxes dues par chaque établissement.

- Émission des taxes par établissement, avec piste d'audit des modifications —
  tables `etab_taxe_emis`, `etab_taxe_emis_audit`
- Règlements/encaissements : montant, mode de paiement, chèque, banque, quittance,
  imputation — table `regl_taxe`
- Informations bancaires des contribuables et référentiel bancaire — tables
  `contb_banq`, `banque`
- Recettes / centres de collecte — table `recette`

### 2.4 Gestion des dossiers administratifs
Suivi du cycle de vie d'un dossier fiscal entre services.

- Fiche dossier : dates de création/retrait/sortie, motifs, service d'origine et de
  destination, état d'archivage — table `dossier`
- Historique des mouvements de dossier — table `histo_dossier`
- Classification par famille et catégorie d'état de dossier — tables
  `famille_etat_dossier`, `categ_etat_dossier`
- Vue arborescente des dossiers, recherches par famille/catégorie

### 2.5 Convocations
Génération et suivi des convocations adressées aux contribuables.

- Fiche, impression et historique des convocations — table `convocation`

### 2.6 Sanctions fiscales et exonérations
Gestion du contrôle fiscal et des régimes dérogatoires.

- Référentiel des infractions et sanctions fiscales — table `sanction_fiscale`
- Exonérations partielles : types d'exonération et lignes de détail — tables
  `exo_part`, `type_exo`, `ligne_exo_part`

### 2.7 Référentiel géographique et territorial
Découpage administratif servant à localiser contribuables, établissements et
collectivités.

- Hiérarchie complète : pays → région → district → département → sous-préfecture →
  commune → quartier → rue / avenue / boulevard → zone — tables `pays`, `nation`,
  `region`, `district`, `dept`, `sous_pref`, `comm`, `commdb`, `quart`, `rue`,
  `avenue`, `bvd`, `zone`, `ville`
- Gestion des collectivités territoriales elles-mêmes et de leur typologie —
  tables `collectiv`, `type_coll`

### 2.8 Administration, sécurité et traçabilité des utilisateurs
Gestion des agents, des droits d'accès et de la traçabilité des connexions
(module **GPW**).

- Fiches agents : fonction, grade, service, collectivité, supérieur hiérarchique —
  tables `agent`, `fonction_agt`, `grade_agt`, `service`, `dept_service`
- Comptes utilisateurs et groupes d'utilisateurs — tables `util`, `grp_util`
- Authentification (écran de connexion)
- Configuration et association des profils utilisateurs (module GPW), historique
  et journal de connexion — tables `gpwconfiguration`, `gpwconfigurationelement`,
  `gpwelement`, `gpwutilisateurconfiguration`, `gpwhistoriqueconnexion`,
  `gpwlogconnexion`

### 2.9 Outils et fonctions transverses
- Calendrier intégré, annuaire/popup téléphonique
- Envoi d'emails et paramétrage SMTP — table `entete_etat`, `config_fen`
- Suivi d'objectifs et obligations — tables `objectif`, `obligations`
- Édition de listes et fiches détaillées pour la quasi-totalité des entités
  (contribuables, agents, taxes, dossiers, convocations, exonérations, communes,
  pays…) au format état/rapport WinDev

---

## 3. Correspondance fonctions ↔ modèle de données

| Domaine fonctionnel | Tables principales |
|---|---|
| Contribuables & établissements | `contb`, `contb_banq`, `dirigeant`, `qlte_dirig`, `etablis`, `activite`, `secteur_activite`, `categ_activite` |
| Paramétrage fiscal | `nature_taxe`, `categ_impot_taxe`, `domaine_taxe`, `regime_imp`, `periodicite`, `param_taxe`, `cotisat_tf`, `categ_cotis_tf`, `zone_cotis`, `regl_tf`, `exer` |
| Émission & recouvrement | `etab_taxe_emis`, `etab_taxe_emis_audit`, `regl_taxe`, `banque`, `recette` |
| Dossiers administratifs | `dossier`, `histo_dossier`, `famille_etat_dossier`, `categ_etat_dossier` |
| Convocations | `convocation` |
| Contrôle & exonérations | `sanction_fiscale`, `exo_part`, `type_exo`, `ligne_exo_part` |
| Référentiel territorial | `pays`, `nation`, `region`, `district`, `dept`, `sous_pref`, `comm`, `commdb`, `quart`, `rue`, `avenue`, `bvd`, `zone`, `ville`, `collectiv`, `type_coll` |
| Agents & sécurité | `agent`, `fonction_agt`, `grade_agt`, `service`, `dept_service`, `util`, `grp_util`, `gpwconfiguration*`, `gpwelement`, `gpwutilisateurconfiguration`, `gpwhistoriqueconnexion`, `gpwlogconnexion` |
| Transverse / paramétrage UI | `entete_etat`, `config_fen`, `modul_appli`, `objectif`, `obligations`, `form_jdq` |

---

## 4. Technologies

- **Développement** : WinDev / WLangage (PC SOFT)
- **Base de données** : MySQL (schéma `fiscctcidb`, encodage `utf8`)
- **Déploiement** :
  - `sys_fisc_coll_territoriale` → exécutable Windows natif (`.exe` + DLL runtime WinDev, installeur `.MSI`)
  - `RtfciFinanceCollTerritoriale` → application Java multiplateforme (`.jar` + `.jnlp`, librairies natives Linux/Windows/macOS)
