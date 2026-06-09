# Cahier des charges

## Système de Gestion de la Fiscalité des Collectivités Territoriales (refonte)

Version 2.0 — Phase 1 : Conception — Cible : **backoffice web interne**

---

## 1. Contexte et objectifs

L'application existante (ERP-FIDELO / RtfciFinanceCollTerritoriale, développée en WinDev et Java sur base MySQL) assure la gestion de la fiscalité locale des collectivités territoriales de Côte d'Ivoire. Le projet vise à **refondre cette solution** en un **backoffice web interne** moderne, maintenable et sécurisé, **destiné à la mairie et à ses agents** (pas de portail public), en repartant d'une base de données assainie (PostgreSQL) et d'une pile PHP / Laravel 13.

Objectifs :

- Fiabiliser les calculs fiscaux (suppression des erreurs d'arrondi, intégrité référentielle complète).
- Offrir une interface web accessible depuis un navigateur, sans installation poste par poste.
- Renforcer la sécurité (authentification, droits par action, traçabilité).
- Préparer l'évolutivité (mono-mairie aujourd'hui, multi-collectivité possible demain).

---

## 2. Périmètre fonctionnel

Backoffice couvrant les domaines suivants (repris et modernisés depuis l'existant) :

1. **Contribuables et établissements** — recensement des personnes physiques et morales, de leurs dirigeants et de leurs établissements.
2. **Référentiel des activités** — secteurs, catégories, activités économiques.
3. **Paramétrage fiscal** — natures, domaines et catégories de taxe, barèmes par tranche de CA, taxe foncière par zone, périodicités, exercices fiscaux.
4. **Émission et recouvrement** — calcul et émission des taxes, encaissement des règlements (espèces, chèque, virement, mobile money), quittances.
5. **Dossiers administratifs** — cycle de vie et circulation des dossiers entre services.
6. **Convocations** — génération et suivi.
7. **Contrôle et exonérations** — sanctions fiscales, exonérations partielles.
8. **Référentiel territorial** — découpage administratif de la Côte d'Ivoire.
9. **Administration et sécurité** — agents, utilisateurs, rôles, permissions, journal de connexion, audit.
10. **Pilotage** — objectifs de recouvrement, états et éditions (PDF).

**Hors périmètre** : aucun **front office / portail contribuable** en libre-service ; comptabilité générale SYSCOHADA complète ; paiement en ligne intégré. L'outil est strictement à usage **interne des agents**.

---

## 3. Acteurs et profils

| Profil | Rôle principal |
|---|---|
| Administrateur système | Paramétrage, sécurité, comptes |
| Responsable fiscal | Pilotage du métier fiscal |
| Agent de recensement | Saisie contribuables / établissements |
| Agent de liquidation | Émission et liquidation des taxes |
| Agent de recouvrement | Encaissement des règlements |
| Gestionnaire de dossiers | Circulation et archivage |
| Contrôleur fiscal | Convocations, contrôle, exonérations |
| Consultation | Lecture seule |

Tous les acteurs sont des **agents internes** de la mairie.

---

## 4. Exigences fonctionnelles clés

- Recherche multicritère des contribuables (nom, raison sociale, sigle, numéro de compte).
- Calcul automatique des taxes selon le barème applicable (tranche de CA, zone, périodicité) et gestion du prorata temporis.
- Émission rattachée à un exercice fiscal ouvert ; blocage des opérations sur un exercice clôturé.
- Recouvrement avec règlements totaux, partiels et acomptes ; calcul automatique du solde dû.
- Génération de documents officiels imprimables (quittances, avis, convocations, rôles) au format PDF.
- Traçabilité de toute création / modification / suppression d'une donnée sensible.

---

## 5. Exigences non-fonctionnelles

- **Sécurité** : authentification obligatoire, mots de passe hachés, droits par action (RBAC), journal des connexions, audit des données, protection CSRF.
- **Intégrité comptable** : montants en décimal exact (jamais en virgule flottante), conformément aux usages comptables (SYSCOHADA).
- **Performance** : réponse < 2 s pour les écrans de consultation courants ; recherches indexées ; mise en cache des référentiels.
- **Disponibilité** : sauvegardes quotidiennes automatisées, plan de restauration.
- **Ergonomie** : interface en français, responsive (utilisable sur tablette), cohérente.
- **Traçabilité** : horodatage et auteur sur les données métier.
- **Portabilité** : application web standard, indépendante du poste client.
- **Maintenabilité** : code documenté, nomenclature métier en français, migrations de base versionnées.

---

## 6. Contraintes

- **Pile technique imposée** : PHP 8.4 (min. 8.3) / **Laravel 13**, backoffice web monolithique (Filament / Livewire / Blade), **sans front office**.
- **SGBD imposé** : PostgreSQL 16.
- **Stockage des pièces jointes** : système de fichiers (chemins référencés en base).
- **Reprise de données** : migration depuis l'ancienne base MySQL `fiscctcidb` (territoire, contribuables, établissements, historique fiscal).
- **Réglementaire** : conformité à la fiscalité locale ivoirienne ; barèmes paramétrables.
- **Langue** : français.

---

## 7. Livrables et phases

- **Phase 1 — Conception** : cahier des charges, architecture, spécifications, choix techniques, `CLAUDE.md`, schéma de base de données et jeux de données.
- **Phase 2 — Développement** : application Laravel (modèles, services, écrans Filament/Livewire), migration des données, éditions PDF.
- **Phase 3 — Recette et déploiement** : tests, formation des agents, mise en production.

---

## 8. Critères d'acceptation (extrait)

- Une émission de taxe se calcule correctement à partir du barème et se règle (total / partiel) avec un solde juste — *validé par le scénario de test fourni (solde dû = 28 000 sur l'exemple).*
- Aucun montant n'est stocké en type flottant, ni calculé sur un float.
- Toute action sensible est tracée et attribuable à un utilisateur.
- Un utilisateur n'accède qu'aux actions autorisées par ses rôles.
