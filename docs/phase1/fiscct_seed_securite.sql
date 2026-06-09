-- =====================================================================
--  Données d'amorçage : SÉCURITÉ (permissions par action + rôles métier)
--  Base : fiscctcidb_v2 (PostgreSQL)
--  Modèle de droits : PAR ACTION (granularité fine)
--  Code permission = <MODULE>_<ACTION>
-- =====================================================================

-- ---------------------------------------------------------------------
--  1. CATALOGUE DES PERMISSIONS
-- ---------------------------------------------------------------------
INSERT INTO permission (code, libelle, module) VALUES
-- Contribuables
('CONTRIB_CONSULTER','Consulter les contribuables','Contribuables'),
('CONTRIB_CREER','Créer un contribuable','Contribuables'),
('CONTRIB_MODIFIER','Modifier un contribuable','Contribuables'),
('CONTRIB_SUPPRIMER','Supprimer/radier un contribuable','Contribuables'),
-- Établissements
('ETAB_CONSULTER','Consulter les établissements','Établissements'),
('ETAB_CREER','Créer un établissement','Établissements'),
('ETAB_MODIFIER','Modifier un établissement','Établissements'),
('ETAB_SUPPRIMER','Supprimer un établissement','Établissements'),
-- Dirigeants
('DIRIG_CONSULTER','Consulter les dirigeants','Dirigeants'),
('DIRIG_CREER','Créer un dirigeant','Dirigeants'),
('DIRIG_MODIFIER','Modifier un dirigeant','Dirigeants'),
('DIRIG_SUPPRIMER','Supprimer un dirigeant','Dirigeants'),
-- Référentiel activités
('ACTIVITE_CONSULTER','Consulter le référentiel des activités','Référentiels'),
('ACTIVITE_GERER','Gérer le référentiel des activités','Référentiels'),
-- Paramétrage fiscal (natures, barèmes, périodicités)
('PARAMFISC_CONSULTER','Consulter le paramétrage fiscal','Paramétrage fiscal'),
('PARAMFISC_GERER','Gérer natures, barèmes et périodicités','Paramétrage fiscal'),
-- Exercices fiscaux
('EXERCICE_CONSULTER','Consulter les exercices fiscaux','Paramétrage fiscal'),
('EXERCICE_OUVRIR','Ouvrir un exercice fiscal','Paramétrage fiscal'),
('EXERCICE_CLOTURER','Clôturer un exercice fiscal','Paramétrage fiscal'),
-- Émission des taxes
('EMISSION_CONSULTER','Consulter les émissions de taxe','Émission'),
('EMISSION_CREER','Créer une émission de taxe','Émission'),
('EMISSION_MODIFIER','Modifier une émission de taxe','Émission'),
('EMISSION_SUPPRIMER','Supprimer une émission de taxe','Émission'),
('EMISSION_LIQUIDER','Liquider une émission de taxe','Émission'),
('EMISSION_VALIDER','Valider une émission de taxe','Émission'),
-- Taxe foncière
('TF_CONSULTER','Consulter les cotisations foncières','Taxe foncière'),
('TF_CREER','Créer une cotisation foncière','Taxe foncière'),
('TF_MODIFIER','Modifier une cotisation foncière','Taxe foncière'),
('TF_LIQUIDER','Liquider une cotisation foncière','Taxe foncière'),
-- Recouvrement / règlements
('RECOUVR_CONSULTER','Consulter les règlements','Recouvrement'),
('RECOUVR_ENCAISSER','Enregistrer un encaissement','Recouvrement'),
('RECOUVR_VALIDER','Valider un règlement','Recouvrement'),
('RECOUVR_ANNULER','Annuler un règlement','Recouvrement'),
-- Dossiers
('DOSSIER_CONSULTER','Consulter les dossiers','Dossiers'),
('DOSSIER_CREER','Créer un dossier','Dossiers'),
('DOSSIER_MODIFIER','Modifier un dossier','Dossiers'),
('DOSSIER_TRANSFERER','Transférer un dossier entre services','Dossiers'),
('DOSSIER_ARCHIVER','Archiver un dossier','Dossiers'),
-- Convocations
('CONVOC_CONSULTER','Consulter les convocations','Convocations'),
('CONVOC_CREER','Créer une convocation','Convocations'),
('CONVOC_MODIFIER','Modifier une convocation','Convocations'),
('CONVOC_IMPRIMER','Imprimer une convocation','Convocations'),
-- Exonérations
('EXO_CONSULTER','Consulter les exonérations','Exonérations'),
('EXO_CREER','Créer une exonération','Exonérations'),
('EXO_MODIFIER','Modifier une exonération','Exonérations'),
('EXO_SUPPRIMER','Supprimer une exonération','Exonérations'),
-- Contrôle / sanctions
('CONTROLE_CONSULTER','Consulter les sanctions fiscales','Contrôle'),
('CONTROLE_GERER','Gérer le référentiel des sanctions','Contrôle'),
('CONTROLE_SANCTIONNER','Appliquer une sanction fiscale','Contrôle'),
-- Référentiel territorial
('TERRITOIRE_CONSULTER','Consulter le référentiel territorial','Référentiels'),
('TERRITOIRE_GERER','Gérer le référentiel territorial','Référentiels'),
-- Collectivités
('COLLECTIVITE_CONSULTER','Consulter les collectivités','Administration'),
('COLLECTIVITE_GERER','Gérer les collectivités','Administration'),
-- Agents
('AGENT_CONSULTER','Consulter les agents','Administration'),
('AGENT_CREER','Créer un agent','Administration'),
('AGENT_MODIFIER','Modifier un agent','Administration'),
('AGENT_SUPPRIMER','Supprimer un agent','Administration'),
-- Sécurité / utilisateurs
('SECURITE_CONSULTER','Consulter les comptes utilisateurs','Sécurité'),
('SECURITE_GERER_UTILISATEUR','Créer/modifier/désactiver un utilisateur','Sécurité'),
('SECURITE_GERER_ROLE','Gérer les rôles et permissions','Sécurité'),
('SECURITE_RESET_MDP','Réinitialiser un mot de passe','Sécurité'),
-- Pilotage / objectifs
('PILOTAGE_CONSULTER','Consulter les objectifs','Pilotage'),
('PILOTAGE_GERER','Définir/réviser les objectifs','Pilotage'),
-- Éditions / états
('EDITION_GENERER','Générer les états et listes','Éditions'),
('EDITION_EXPORTER','Exporter les données','Éditions'),
-- Audit
('AUDIT_CONSULTER','Consulter le journal d''audit','Sécurité');

-- ---------------------------------------------------------------------
--  2. RÔLES MÉTIER
-- ---------------------------------------------------------------------
INSERT INTO role (code, libelle, description) VALUES
('ADMIN',         'Administrateur système',        'Accès complet, y compris sécurité et paramétrage'),
('ADMIN_FISC',    'Responsable fiscal',            'Pilotage du métier fiscal sans la sécurité système'),
('AGENT_RECENS',  'Agent de recensement',          'Recensement contribuables et établissements'),
('AGENT_LIQUID',  'Agent de liquidation',          'Émission et liquidation des taxes'),
('CAISSIER',      'Agent de recouvrement',         'Encaissement des règlements'),
('GEST_DOSSIER',  'Gestionnaire de dossiers',      'Suivi et circulation des dossiers'),
('CONTROLEUR',    'Contrôleur fiscal',             'Convocations, contrôle, exonérations, sanctions'),
('CONSULT',       'Consultation seule',            'Lecture seule sur l''ensemble des modules');

-- ---------------------------------------------------------------------
--  3. ATTRIBUTION DES PERMISSIONS AUX RÔLES
-- ---------------------------------------------------------------------

-- 3.1 ADMIN : toutes les permissions
INSERT INTO role_permission (role_id, permission_id)
SELECT r.id, p.id FROM role r CROSS JOIN permission p WHERE r.code = 'ADMIN';

-- 3.2 CONSULT : toutes les permissions *_CONSULTER
INSERT INTO role_permission (role_id, permission_id)
SELECT r.id, p.id FROM role r JOIN permission p ON p.code LIKE '%\_CONSULTER'
WHERE r.code = 'CONSULT';

-- 3.3 ADMIN_FISC : tout le métier fiscal (exclut Sécurité et gestion des agents)
INSERT INTO role_permission (role_id, permission_id)
SELECT r.id, p.id FROM role r JOIN permission p
  ON p.module IN ('Contribuables','Établissements','Dirigeants','Référentiels',
                  'Paramétrage fiscal','Émission','Taxe foncière','Recouvrement',
                  'Dossiers','Convocations','Exonérations','Contrôle','Pilotage','Éditions')
WHERE r.code = 'ADMIN_FISC';

-- 3.4 AGENT_RECENS
INSERT INTO role_permission (role_id, permission_id)
SELECT r.id, p.id FROM role r JOIN permission p ON p.code IN (
  'CONTRIB_CONSULTER','CONTRIB_CREER','CONTRIB_MODIFIER',
  'ETAB_CONSULTER','ETAB_CREER','ETAB_MODIFIER',
  'DIRIG_CONSULTER','DIRIG_CREER','DIRIG_MODIFIER',
  'ACTIVITE_CONSULTER','TERRITOIRE_CONSULTER',
  'DOSSIER_CONSULTER','EDITION_GENERER')
WHERE r.code = 'AGENT_RECENS';

-- 3.5 AGENT_LIQUID
INSERT INTO role_permission (role_id, permission_id)
SELECT r.id, p.id FROM role r JOIN permission p ON p.code IN (
  'CONTRIB_CONSULTER','ETAB_CONSULTER',
  'PARAMFISC_CONSULTER','EXERCICE_CONSULTER','ACTIVITE_CONSULTER',
  'EMISSION_CONSULTER','EMISSION_CREER','EMISSION_MODIFIER','EMISSION_LIQUIDER','EMISSION_VALIDER',
  'TF_CONSULTER','TF_CREER','TF_MODIFIER','TF_LIQUIDER',
  'EXO_CONSULTER','EDITION_GENERER')
WHERE r.code = 'AGENT_LIQUID';

-- 3.6 CAISSIER
INSERT INTO role_permission (role_id, permission_id)
SELECT r.id, p.id FROM role r JOIN permission p ON p.code IN (
  'CONTRIB_CONSULTER','ETAB_CONSULTER',
  'EMISSION_CONSULTER','TF_CONSULTER',
  'RECOUVR_CONSULTER','RECOUVR_ENCAISSER','RECOUVR_VALIDER',
  'EDITION_GENERER')
WHERE r.code = 'CAISSIER';

-- 3.7 GEST_DOSSIER
INSERT INTO role_permission (role_id, permission_id)
SELECT r.id, p.id FROM role r JOIN permission p ON p.code IN (
  'ETAB_CONSULTER','CONTRIB_CONSULTER',
  'DOSSIER_CONSULTER','DOSSIER_CREER','DOSSIER_MODIFIER','DOSSIER_TRANSFERER','DOSSIER_ARCHIVER',
  'EDITION_GENERER')
WHERE r.code = 'GEST_DOSSIER';

-- 3.8 CONTROLEUR
INSERT INTO role_permission (role_id, permission_id)
SELECT r.id, p.id FROM role r JOIN permission p ON p.code IN (
  'CONTRIB_CONSULTER','ETAB_CONSULTER','EMISSION_CONSULTER','RECOUVR_CONSULTER',
  'CONVOC_CONSULTER','CONVOC_CREER','CONVOC_MODIFIER','CONVOC_IMPRIMER',
  'CONTROLE_CONSULTER','CONTROLE_SANCTIONNER',
  'EXO_CONSULTER','EXO_CREER','EXO_MODIFIER',
  'EDITION_GENERER')
WHERE r.code = 'CONTROLEUR';

-- =====================================================================
--  Note : un utilisateur peut cumuler plusieurs rôles
--  (table utilisateur_role). La portée par collectivité reste assurée
--  par utilisateur.collectivite_id + Row Level Security.
-- =====================================================================
