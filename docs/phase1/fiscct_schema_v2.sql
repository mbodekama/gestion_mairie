-- =====================================================================
--  Système de Gestion de la Fiscalité des Collectivités Territoriales
--  NOUVELLE BASE DE DONNÉES - Schéma cible v2
--  SGBD : PostgreSQL 16+   |  Encodage : UTF-8   |  Casse : snake_case
--  Refonte de fiscctcidb (MySQL/WinDev) - 13/05/2021
-- =====================================================================
--
--  PRINCIPES DE CONCEPTION
--   1. Clé technique « id » (bigint identity) sur chaque table ; le code
--      métier d'origine est conservé en colonne UNIQUE (compatibilité).
--   2. Montants en NUMERIC (jamais double) -> aucune erreur d'arrondi.
--   3. Intégrité référentielle systématique (toutes les FK déclarées).
--   4. Multi-collectivité : colonne collectivite_id sur le transactionnel
--      + Row Level Security activable.
--   5. Traçabilité native : created_at / updated_at / created_by /
--      updated_by + table d'audit générique (remplace *_audit et gpw*).
--   6. Sécurité = RBAC propre (utilisateur / role / permission) en
--      remplacement du module WinDev « gpw* ».
--   7. Suppression logique (actif / supprime_le) sur les entités métier.
-- =====================================================================

CREATE DATABASE fiscctcidb_v2 WITH ENCODING 'UTF8' LC_COLLATE 'fr_FR.UTF-8' LC_CTYPE 'fr_FR.UTF-8';
\connect fiscctcidb_v2

CREATE EXTENSION IF NOT EXISTS pgcrypto;      -- hachage mots de passe / UUID
CREATE EXTENSION IF NOT EXISTS unaccent;       -- recherche sans accents

-- ---------------------------------------------------------------------
--  Fonction utilitaire : mise à jour automatique de updated_at
-- ---------------------------------------------------------------------
CREATE OR REPLACE FUNCTION trg_set_updated_at() RETURNS trigger AS $$
BEGIN
  NEW.updated_at := now();
  RETURN NEW;
END; $$ LANGUAGE plpgsql;

-- =====================================================================
--  1. RÉFÉRENTIEL GÉOGRAPHIQUE ET TERRITORIAL
-- =====================================================================

CREATE TABLE pays (
  id           bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code         varchar(3)  NOT NULL UNIQUE,
  libelle      varchar(255) NOT NULL,
  code_iso2    char(2),
  code_iso3    char(3),
  actif        boolean NOT NULL DEFAULT true
);

CREATE TABLE nationalite (
  id           bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code         varchar(3) NOT NULL UNIQUE,
  libelle      varchar(255) NOT NULL,
  pays_id      bigint REFERENCES pays(id)
);

CREATE TABLE district (
  id      bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code    varchar(3) NOT NULL UNIQUE,
  libelle varchar(255) NOT NULL
);

CREATE TABLE region (
  id          bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code        varchar(4) NOT NULL UNIQUE,
  libelle     varchar(255) NOT NULL,
  district_id bigint NOT NULL REFERENCES district(id)
);

CREATE TABLE departement (
  id        bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code      varchar(3) NOT NULL UNIQUE,
  libelle   varchar(255) NOT NULL,
  region_id bigint NOT NULL REFERENCES region(id)
);

CREATE TABLE sous_prefecture (
  id             bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code           varchar(3) NOT NULL UNIQUE,
  libelle        varchar(255) NOT NULL,
  departement_id bigint NOT NULL REFERENCES departement(id)
);

CREATE TABLE commune (
  id                  bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code                varchar(3) NOT NULL UNIQUE,
  libelle             varchar(255) NOT NULL,
  sous_prefecture_id  bigint REFERENCES sous_prefecture(id),
  population          integer
);

CREATE TABLE quartier (
  id         bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code       varchar(8) NOT NULL UNIQUE,
  libelle    varchar(255) NOT NULL,
  commune_id bigint NOT NULL REFERENCES commune(id)
);

-- Fusion de rue / avenue / boulevard via un type de voie
CREATE TABLE voie (
  id          bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code        varchar(8) NOT NULL UNIQUE,
  libelle     varchar(255) NOT NULL,
  type_voie   varchar(12) NOT NULL CHECK (type_voie IN ('RUE','AVENUE','BOULEVARD','AUTRE')),
  quartier_id bigint REFERENCES quartier(id)
);

CREATE TABLE zone_fiscale (
  id         bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code       varchar(4) NOT NULL UNIQUE,
  libelle    varchar(255) NOT NULL,
  commune_id bigint REFERENCES commune(id)
);

-- =====================================================================
--  2. COLLECTIVITÉS, RECETTES ET ORGANISATION INTERNE
-- =====================================================================

CREATE TABLE type_collectivite (
  id      bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code    varchar(3) NOT NULL UNIQUE,
  libelle varchar(128) NOT NULL                       -- Mairie, District, Région...
);

CREATE TABLE recette (
  id             bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code           varchar(3) NOT NULL UNIQUE,
  libelle        varchar(128) NOT NULL,
  departement_id bigint REFERENCES departement(id),
  boite_postale  varchar(64),
  telephone      varchar(64)
);

CREATE TABLE collectivite (
  id                  bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code                varchar(3) NOT NULL UNIQUE,
  libelle             varchar(128) NOT NULL,
  type_collectivite_id bigint NOT NULL REFERENCES type_collectivite(id),
  recette_id          bigint REFERENCES recette(id),
  district_id         bigint REFERENCES district(id),
  region_id           bigint REFERENCES region(id),
  departement_id      bigint REFERENCES departement(id),
  commune_id          bigint REFERENCES commune(id),
  adresse             varchar(255),
  boite_postale       varchar(32),
  telephone1          varchar(32),
  telephone2          varchar(32),
  cellulaire1         varchar(32),
  cellulaire2         varchar(32),
  fax                 varchar(32),
  email               varchar(128),
  logo_uri            varchar(512),                    -- externalisé (plus de blob)
  active              boolean NOT NULL DEFAULT true,
  created_at          timestamptz NOT NULL DEFAULT now(),
  updated_at          timestamptz NOT NULL DEFAULT now()
);
CREATE TRIGGER tg_collectivite BEFORE UPDATE ON collectivite FOR EACH ROW EXECUTE FUNCTION trg_set_updated_at();

CREATE TABLE departement_service (
  id                   bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code                 varchar(3) NOT NULL UNIQUE,
  libelle              varchar(128) NOT NULL,
  sigle                varchar(64),
  type_collectivite_id bigint REFERENCES type_collectivite(id)
);

CREATE TABLE service (
  id                     bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code                   varchar(6) NOT NULL UNIQUE,
  libelle                varchar(128) NOT NULL,
  sigle                  varchar(64),
  collectivite_id        bigint REFERENCES collectivite(id),
  departement_service_id bigint REFERENCES departement_service(id)
);

-- Organisation (rattachement des dirigeants)
CREATE TABLE organisation (
  id                   bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code                 varchar(3) NOT NULL UNIQUE,
  libelle              varchar(255) NOT NULL,
  type_collectivite_id bigint REFERENCES type_collectivite(id)
);

-- =====================================================================
--  3. AGENTS, UTILISATEURS ET SÉCURITÉ (RBAC) - remplace gpw*
-- =====================================================================

CREATE TABLE grade_agent (
  id      bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code    varchar(2) NOT NULL UNIQUE,
  libelle varchar(64) NOT NULL
);

CREATE TABLE fonction_agent (
  id      bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code    varchar(2) NOT NULL UNIQUE,
  libelle varchar(255) NOT NULL
);

CREATE TABLE agent (
  id                bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  matricule         varchar(32) NOT NULL UNIQUE,
  nom               varchar(64),
  prenoms           varchar(128),
  fonction_agent_id bigint REFERENCES fonction_agent(id),
  grade_agent_id    bigint REFERENCES grade_agent(id),
  service_id        bigint REFERENCES service(id),
  collectivite_id   bigint NOT NULL REFERENCES collectivite(id),
  superieur_id      bigint REFERENCES agent(id),         -- hiérarchie
  observation       varchar(255),
  actif             boolean NOT NULL DEFAULT true,
  created_at        timestamptz NOT NULL DEFAULT now(),
  updated_at        timestamptz NOT NULL DEFAULT now()
);
CREATE TRIGGER tg_agent BEFORE UPDATE ON agent FOR EACH ROW EXECUTE FUNCTION trg_set_updated_at();

CREATE TABLE utilisateur (
  id              bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  login           varchar(64) NOT NULL UNIQUE,
  email           varchar(128) UNIQUE,
  mot_de_passe    varchar(255) NOT NULL,                 -- hash bcrypt/argon2
  nom             varchar(128),
  prenoms         varchar(255),
  agent_id        bigint REFERENCES agent(id),
  collectivite_id bigint NOT NULL REFERENCES collectivite(id),
  statut          varchar(12) NOT NULL DEFAULT 'ACTIF'
                  CHECK (statut IN ('ACTIF','SUSPENDU','EXPIRE','VERROUILLE')),
  mfa_active      boolean NOT NULL DEFAULT false,
  date_creation   date NOT NULL DEFAULT current_date,
  date_expiration date,
  derniere_connexion timestamptz,
  created_at      timestamptz NOT NULL DEFAULT now(),
  updated_at      timestamptz NOT NULL DEFAULT now()
);
CREATE TRIGGER tg_utilisateur BEFORE UPDATE ON utilisateur FOR EACH ROW EXECUTE FUNCTION trg_set_updated_at();

CREATE TABLE role (
  id          bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code        varchar(32) NOT NULL UNIQUE,
  libelle     varchar(128) NOT NULL,
  description varchar(255)
);

CREATE TABLE permission (
  id      bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code    varchar(64) NOT NULL UNIQUE,            -- ex: CONTRIB_CREER, TAXE_EMETTRE
  libelle varchar(128) NOT NULL,
  module  varchar(64)                              -- regroupement fonctionnel
);

CREATE TABLE role_permission (
  role_id       bigint NOT NULL REFERENCES role(id) ON DELETE CASCADE,
  permission_id bigint NOT NULL REFERENCES permission(id) ON DELETE CASCADE,
  PRIMARY KEY (role_id, permission_id)
);

CREATE TABLE utilisateur_role (
  utilisateur_id bigint NOT NULL REFERENCES utilisateur(id) ON DELETE CASCADE,
  role_id        bigint NOT NULL REFERENCES role(id) ON DELETE CASCADE,
  PRIMARY KEY (utilisateur_id, role_id)
);

-- Journal de connexion (remplace gpwhistoriqueconnexion / gpwlogconnexion)
CREATE TABLE journal_connexion (
  id             bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  utilisateur_id bigint REFERENCES utilisateur(id),
  login          varchar(64),
  application    varchar(128),
  succes         boolean NOT NULL DEFAULT true,
  adresse_ip     inet,
  user_agent     varchar(255),
  horodatage     timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX ix_journal_connexion_user ON journal_connexion(utilisateur_id, horodatage);

-- Audit générique (remplace etab_taxe_emis_audit et étend à toutes les tables)
CREATE TABLE audit_log (
  id             bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  table_cible    varchar(64) NOT NULL,
  cle_ligne      varchar(64) NOT NULL,
  action         varchar(8) NOT NULL CHECK (action IN ('INSERT','UPDATE','DELETE')),
  donnees_avant  jsonb,
  donnees_apres  jsonb,
  utilisateur_id bigint REFERENCES utilisateur(id),
  horodatage     timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX ix_audit_log_cible ON audit_log(table_cible, cle_ligne);

-- =====================================================================
--  4. CONTRIBUABLES, DIRIGEANTS ET COORDONNÉES BANCAIRES
-- =====================================================================

CREATE TABLE forme_juridique (
  id            bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code          varchar(3) NOT NULL UNIQUE,
  nom_court     varchar(32),
  libelle       varchar(128) NOT NULL
);

CREATE TABLE regime_imposition (
  id            bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code          varchar(3) NOT NULL UNIQUE,
  libelle_court varchar(16),
  libelle       varchar(255) NOT NULL,
  ca_borne_inf  numeric(18,2) NOT NULL DEFAULT 0,
  ca_borne_sup  numeric(18,2) NOT NULL DEFAULT 0
);

CREATE TABLE banque (
  id            bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code          varchar(3) NOT NULL UNIQUE,
  libelle_court varchar(16),
  libelle       varchar(255) NOT NULL
);

-- Personne physique ET morale dans une seule table, discriminée par type_personne
CREATE TABLE contribuable (
  id                  bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  numero_identifiant  varchar(12) NOT NULL UNIQUE,         -- ex num_id_contb
  numero_compte       varchar(12) NOT NULL UNIQUE,         -- ex num_cc
  collectivite_id     bigint NOT NULL REFERENCES collectivite(id),
  type_personne       varchar(2) NOT NULL CHECK (type_personne IN ('PP','PM')),
  -- État civil (personne physique)
  nom                 varchar(64),
  prenoms             varchar(128),
  sexe                char(1) CHECK (sexe IN ('M','F')),
  date_naissance      date,
  lieu_naissance      varchar(64),
  nationalite_id      bigint REFERENCES nationalite(id),
  numero_piece        varchar(64),
  nature_piece        varchar(20),
  nom_pere            varchar(64),
  prenoms_pere        varchar(128),
  nom_mere            varchar(64),
  prenoms_mere        varchar(128),
  -- Personne morale
  raison_sociale      varchar(128),
  sigle               varchar(64),
  denomination_commerciale varchar(255),
  forme_juridique_id  bigint REFERENCES forme_juridique(id),
  registre_commerce   varchar(32),
  date_registre_commerce date,
  ville_registre_commerce varchar(64),
  nombre_associes     integer,
  capital_social      numeric(18,2),
  -- Régime / contacts
  regime_imposition_id bigint REFERENCES regime_imposition(id),
  boite_postale       varchar(255),
  telephone           varchar(32),
  cellulaire          varchar(32),
  fax                 varchar(16),
  email               varchar(255),
  photo_uri           varchar(512),                        -- externalisé
  statut              varchar(12) NOT NULL DEFAULT 'ACTIF'
                      CHECK (statut IN ('ACTIF','RADIE','SUSPENDU')),
  -- Cohérence PP/PM
  CONSTRAINT ck_contrib_pp CHECK (type_personne <> 'PP' OR nom IS NOT NULL),
  CONSTRAINT ck_contrib_pm CHECK (type_personne <> 'PM' OR raison_sociale IS NOT NULL),
  created_by          bigint,
  updated_by          bigint,
  created_at          timestamptz NOT NULL DEFAULT now(),
  updated_at          timestamptz NOT NULL DEFAULT now(),
  supprime_le         timestamptz
);
CREATE TRIGGER tg_contribuable BEFORE UPDATE ON contribuable FOR EACH ROW EXECUTE FUNCTION trg_set_updated_at();
CREATE INDEX ix_contrib_nom ON contribuable(nom, prenoms);
CREATE INDEX ix_contrib_rs  ON contribuable(raison_sociale);
CREATE INDEX ix_contrib_coll ON contribuable(collectivite_id);

CREATE TABLE coordonnee_bancaire (
  id              bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  contribuable_id bigint NOT NULL REFERENCES contribuable(id) ON DELETE CASCADE,
  banque_id       bigint REFERENCES banque(id),
  code_guichet    varchar(16),
  numero_compte   varchar(34),
  cle_rib         varchar(8),
  nom_agence      varchar(128)
);

CREATE TABLE qualite_dirigeant (
  id            bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code          integer NOT NULL UNIQUE,
  libelle_court varchar(16),
  libelle       varchar(128) NOT NULL
);

CREATE TABLE dirigeant (
  id                  bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code                varchar(6) NOT NULL UNIQUE,
  collectivite_id     bigint NOT NULL REFERENCES collectivite(id),
  organisation_id     bigint REFERENCES organisation(id),
  qualite_dirigeant_id bigint REFERENCES qualite_dirigeant(id),
  nom                 varchar(64),
  prenoms             varchar(128),
  adresse             varchar(128),
  telephone           varchar(16),
  cellulaire          varchar(16),
  email               varchar(255),
  date_debut          date,
  date_fin            date,
  actif               boolean NOT NULL DEFAULT true,
  created_at          timestamptz NOT NULL DEFAULT now(),
  updated_at          timestamptz NOT NULL DEFAULT now()
);
CREATE TRIGGER tg_dirigeant BEFORE UPDATE ON dirigeant FOR EACH ROW EXECUTE FUNCTION trg_set_updated_at();

-- =====================================================================
--  5. ACTIVITÉS ÉCONOMIQUES ET ÉTABLISSEMENTS
-- =====================================================================

CREATE TABLE secteur_activite (
  id      bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code    varchar(3) NOT NULL UNIQUE,
  libelle varchar(128) NOT NULL
);

CREATE TABLE categorie_activite (
  id      bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code    varchar(3) NOT NULL UNIQUE,
  libelle varchar(255) NOT NULL
);

CREATE TABLE activite (
  id                   bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code                 varchar(5) NOT NULL UNIQUE,
  libelle              varchar(1000) NOT NULL,
  secteur_activite_id  bigint NOT NULL REFERENCES secteur_activite(id),
  categorie_activite_id bigint NOT NULL REFERENCES categorie_activite(id)
);

CREATE TABLE etablissement (
  id                  bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  numero              varchar(10) NOT NULL UNIQUE,
  contribuable_id     bigint NOT NULL REFERENCES contribuable(id),
  collectivite_id     bigint NOT NULL REFERENCES collectivite(id),
  activite_id         bigint NOT NULL REFERENCES activite(id),
  denomination        varchar(255),
  type_etablissement  varchar(12) NOT NULL CHECK (type_etablissement IN ('PRINCIPAL','SECONDAIRE')),
  -- Localisation
  commune_id          bigint NOT NULL REFERENCES commune(id),
  quartier_id         bigint REFERENCES quartier(id),
  voie_id             bigint REFERENCES voie(id),
  zone_fiscale_id     bigint NOT NULL REFERENCES zone_fiscale(id),
  adresse             varchar(64),
  lot_ilot            varchar(10),
  section_parcelle    varchar(12),
  surface             numeric(11,2) DEFAULT 0,
  -- Contacts
  boite_postale       varchar(32),
  telephone           varchar(32),
  fax                 varchar(32),
  email               varchar(128),
  -- Cycle de vie
  date_debut_activite date NOT NULL,
  date_cessation      date,
  date_transfert      date,
  date_sommeil        date,
  statut              varchar(12) NOT NULL DEFAULT 'ACTIF'
                      CHECK (statut IN ('ACTIF','CESSE','TRANSFERE','SOMMEIL')),
  created_by          bigint,
  updated_by          bigint,
  created_at          timestamptz NOT NULL DEFAULT now(),
  updated_at          timestamptz NOT NULL DEFAULT now(),
  supprime_le         timestamptz
);
CREATE TRIGGER tg_etablissement BEFORE UPDATE ON etablissement FOR EACH ROW EXECUTE FUNCTION trg_set_updated_at();
CREATE INDEX ix_etab_contrib ON etablissement(contribuable_id);
CREATE INDEX ix_etab_coll    ON etablissement(collectivite_id);
CREATE INDEX ix_etab_activite ON etablissement(activite_id);

-- =====================================================================
--  6. PARAMÉTRAGE FISCAL
-- =====================================================================

CREATE TABLE domaine_taxe (
  id            bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code          varchar(3) NOT NULL UNIQUE,
  libelle_court varchar(16),
  libelle       varchar(128) NOT NULL
);

CREATE TABLE categorie_impot_taxe (
  id      bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code    varchar(3) NOT NULL UNIQUE,
  libelle varchar(256) NOT NULL
);

CREATE TABLE periodicite (
  id            bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code          varchar(3) NOT NULL UNIQUE,
  libelle_court varchar(16),
  libelle       varchar(128) NOT NULL,
  nb_mois       smallint                                  -- 1, 3, 6, 12...
);

CREATE TABLE nature_taxe (
  id                    bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code                  varchar(3) NOT NULL UNIQUE,
  libelle_court         varchar(16),
  libelle               varchar(255) NOT NULL,
  domaine_taxe_id       bigint NOT NULL REFERENCES domaine_taxe(id),
  categorie_impot_taxe_id bigint NOT NULL REFERENCES categorie_impot_taxe(id)
);

-- Barèmes par tranche de CA et activité (ex param_taxe)
CREATE TABLE bareme_taxe (
  id                   bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  nature_taxe_id       bigint NOT NULL REFERENCES nature_taxe(id),
  categorie_activite_id bigint REFERENCES categorie_activite(id),
  periodicite_id       bigint NOT NULL REFERENCES periodicite(id),
  ca_borne_inf         numeric(18,2) NOT NULL DEFAULT 0,
  ca_borne_sup         numeric(18,2) NOT NULL DEFAULT 0,
  taux                 numeric(10,4) NOT NULL DEFAULT 0,
  CONSTRAINT ck_bareme_bornes CHECK (ca_borne_sup = 0 OR ca_borne_sup >= ca_borne_inf)
);

CREATE TABLE exercice_fiscal (
  id              bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  annee           smallint NOT NULL,
  collectivite_id bigint NOT NULL REFERENCES collectivite(id),
  date_debut      date NOT NULL,
  date_fin        date NOT NULL,
  cloture         boolean NOT NULL DEFAULT false,
  UNIQUE (annee, collectivite_id)
);

-- Taxe foncière : catégories, barèmes par zone
CREATE TABLE categorie_cotisation_fonciere (
  id      bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code    varchar(3) NOT NULL UNIQUE,
  libelle varchar(128) NOT NULL
);

CREATE TABLE bareme_cotisation_fonciere (
  id              bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  nature_taxe_id  bigint NOT NULL REFERENCES nature_taxe(id),
  activite_id     bigint REFERENCES activite(id),
  periodicite_id  bigint NOT NULL REFERENCES periodicite(id),
  ca_borne_inf    numeric(18,2) DEFAULT 0,
  ca_borne_sup    numeric(18,2) DEFAULT 0,
  montant_zone1   numeric(15,2) DEFAULT 0,
  montant_zone2   numeric(15,2) DEFAULT 0,
  forfaitaire     boolean NOT NULL DEFAULT false
);

-- Obligations déclaratives du contribuable
CREATE TABLE obligation (
  id              bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  contribuable_id bigint NOT NULL REFERENCES contribuable(id) ON DELETE CASCADE,
  collectivite_id bigint NOT NULL REFERENCES collectivite(id),
  nature_taxe_id  bigint NOT NULL REFERENCES nature_taxe(id),
  periodicite_id  bigint REFERENCES periodicite(id),
  created_by      bigint,
  created_at      timestamptz NOT NULL DEFAULT now()
);

-- =====================================================================
--  7. ÉMISSION ET RECOUVREMENT DES TAXES
-- =====================================================================

-- Référentiels de paiement (remplacent les codes en dur mode_reglt / type_regl)
CREATE TABLE mode_reglement (
  id      bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code    varchar(3) NOT NULL UNIQUE,
  libelle varchar(64) NOT NULL                            -- Espèces, Chèque, Virement...
);

CREATE TABLE type_reglement (
  id      bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code    varchar(3) NOT NULL UNIQUE,
  libelle varchar(64) NOT NULL                            -- Total, Partiel, Acompte...
);

-- Émission de taxe par établissement (ex etab_taxe_emis)
CREATE TABLE emission_taxe (
  id                 bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  numero_emission    varchar(15) NOT NULL UNIQUE,
  numero_fiche       varchar(15),
  numero_article     varchar(18) NOT NULL,
  etablissement_id   bigint NOT NULL REFERENCES etablissement(id),
  collectivite_id    bigint NOT NULL REFERENCES collectivite(id),
  dossier_id         bigint,                              -- FK ajoutée plus bas
  nature_taxe_id     bigint NOT NULL REFERENCES nature_taxe(id),
  periodicite_id     bigint NOT NULL REFERENCES periodicite(id),
  exercice_fiscal_id bigint NOT NULL REFERENCES exercice_fiscal(id),
  ca_annuel          numeric(18,2),
  montant_annuel     numeric(15,2) DEFAULT 0,
  montant_periode    numeric(15,2) DEFAULT 0,
  nb_mois_prorata    smallint,
  montant_prorata    numeric(15,2) DEFAULT 0,
  date_declaration   date,
  date_liquidation   date,
  created_by         bigint,
  updated_by         bigint,
  created_at         timestamptz NOT NULL DEFAULT now(),
  updated_at         timestamptz NOT NULL DEFAULT now()
);
CREATE TRIGGER tg_emission_taxe BEFORE UPDATE ON emission_taxe FOR EACH ROW EXECUTE FUNCTION trg_set_updated_at();
CREATE INDEX ix_emis_etab ON emission_taxe(etablissement_id);
CREATE INDEX ix_emis_exer ON emission_taxe(exercice_fiscal_id);

-- Émission de cotisation foncière (ex etab_cotis)
CREATE TABLE emission_cotisation_fonciere (
  id                 bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  numero_fiche       varchar(15),
  numero_article     varchar(18) NOT NULL,
  etablissement_id   bigint NOT NULL REFERENCES etablissement(id),
  collectivite_id    bigint NOT NULL REFERENCES collectivite(id),
  dossier_id         bigint,
  nature_taxe_id     bigint NOT NULL REFERENCES nature_taxe(id),
  bareme_cotisation_id bigint REFERENCES bareme_cotisation_fonciere(id),
  periodicite_id     bigint NOT NULL REFERENCES periodicite(id),
  exercice_fiscal_id bigint NOT NULL REFERENCES exercice_fiscal(id),
  ca_annuel          numeric(18,2),
  montant            numeric(15,2),
  montant_periode    numeric(15,2) DEFAULT 0,
  nb_mois_prorata    smallint,
  montant_prorata    numeric(15,2),
  date_declaration   date,
  date_liquidation   date,
  created_by         bigint,
  created_at         timestamptz NOT NULL DEFAULT now()
);

-- Règlements de taxe (ex regl_taxe) - unifie aussi regl_tf via colonne origine
CREATE TABLE reglement_taxe (
  id                 bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  numero_reglement   varchar(12) NOT NULL UNIQUE,
  emission_taxe_id   bigint REFERENCES emission_taxe(id),
  emission_cotisation_id bigint REFERENCES emission_cotisation_fonciere(id),
  collectivite_id    bigint NOT NULL REFERENCES collectivite(id),
  recette_id         bigint NOT NULL REFERENCES recette(id),
  exercice_fiscal_id bigint NOT NULL REFERENCES exercice_fiscal(id),
  date_reglement     date,
  montant            numeric(15,2) NOT NULL DEFAULT 0,
  montant_impute     numeric(15,2) NOT NULL DEFAULT 0,
  mode_reglement_id  bigint NOT NULL REFERENCES mode_reglement(id),
  type_reglement_id  bigint NOT NULL REFERENCES type_reglement(id),
  numero_cheque      varchar(64),
  banque_id          bigint REFERENCES banque(id),
  numero_quittance   varchar(64),
  mois_impute        smallint,
  created_by         bigint,
  created_at         timestamptz NOT NULL DEFAULT now(),
  -- Un règlement vise une émission de taxe OU une cotisation foncière
  CONSTRAINT ck_regl_cible CHECK (
    (emission_taxe_id IS NOT NULL) <> (emission_cotisation_id IS NOT NULL))
);
CREATE INDEX ix_regl_emis ON reglement_taxe(emission_taxe_id);
CREATE INDEX ix_regl_date ON reglement_taxe(date_reglement);

-- =====================================================================
--  8. DOSSIERS ADMINISTRATIFS
-- =====================================================================

CREATE TABLE famille_etat_dossier (
  id      bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code    integer NOT NULL UNIQUE,
  libelle varchar(128) NOT NULL
);

CREATE TABLE categorie_etat_dossier (
  id                    bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code                  integer NOT NULL UNIQUE,
  libelle               varchar(128) NOT NULL,
  famille_etat_dossier_id bigint REFERENCES famille_etat_dossier(id)
);

CREATE TABLE dossier (
  id                     bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  numero                 varchar(8) NOT NULL UNIQUE,
  etablissement_id       bigint NOT NULL REFERENCES etablissement(id),
  collectivite_id        bigint NOT NULL REFERENCES collectivite(id),
  date_creation          date,
  motif_entree           varchar(255),
  date_retour            date,
  date_sortie            date,
  motif_sortie           varchar(255),
  agent_retrait_id       bigint REFERENCES agent(id),
  service_origine_id     bigint REFERENCES service(id),
  service_destination_id bigint REFERENCES service(id),
  famille_etat_dossier_id  bigint REFERENCES famille_etat_dossier(id),
  categorie_etat_dossier_id bigint REFERENCES categorie_etat_dossier(id),
  archive                boolean NOT NULL DEFAULT false,
  created_by             bigint,
  created_at             timestamptz NOT NULL DEFAULT now(),
  updated_at             timestamptz NOT NULL DEFAULT now()
);
CREATE TRIGGER tg_dossier BEFORE UPDATE ON dossier FOR EACH ROW EXECUTE FUNCTION trg_set_updated_at();

-- FK différées vers dossier (référencé par les émissions)
ALTER TABLE emission_taxe
  ADD CONSTRAINT fk_emis_dossier FOREIGN KEY (dossier_id) REFERENCES dossier(id);
ALTER TABLE emission_cotisation_fonciere
  ADD CONSTRAINT fk_emiscot_dossier FOREIGN KEY (dossier_id) REFERENCES dossier(id);

-- Historique des mouvements de dossier (ex histo_dossier)
CREATE TABLE historique_dossier (
  id                     bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  dossier_id             bigint NOT NULL REFERENCES dossier(id) ON DELETE CASCADE,
  date_mouvement         date NOT NULL DEFAULT current_date,
  motif                  varchar(255),
  service_origine_id     bigint REFERENCES service(id),
  service_destination_id bigint REFERENCES service(id),
  agent_id               bigint REFERENCES agent(id),
  archive                boolean NOT NULL DEFAULT false,
  created_by             bigint,
  created_at             timestamptz NOT NULL DEFAULT now()
);

-- =====================================================================
--  9. CONVOCATIONS, CONTRÔLE ET EXONÉRATIONS
-- =====================================================================

CREATE TABLE convocation (
  id              bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  numero          varchar(10) NOT NULL UNIQUE,
  etablissement_id bigint NOT NULL REFERENCES etablissement(id),
  collectivite_id bigint NOT NULL REFERENCES collectivite(id),
  service_id      bigint NOT NULL REFERENCES service(id),
  agent_id        bigint NOT NULL REFERENCES agent(id),
  annee           smallint NOT NULL,
  motif           varchar(255),
  date_convocation date,
  delai_reponse   integer,
  date_limite     date,
  date_reponse    date,
  heure_reponse   time,
  periode_due_debut date,
  periode_due_fin date,
  nb_mois_du      integer,
  nb_jours_du     integer,
  montant_du      numeric(15,2) DEFAULT 0,
  created_by      bigint,
  created_at      timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE sanction_fiscale (
  id      bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code    varchar(4) NOT NULL UNIQUE,
  libelle varchar(128) NOT NULL                            -- nature de l'infraction
);

CREATE TABLE type_exoneration (
  id      bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  code    varchar(3) NOT NULL UNIQUE,
  libelle varchar(128) NOT NULL
);

CREATE TABLE exoneration (
  id                bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  numero            varchar(32) NOT NULL UNIQUE,
  contribuable_id   bigint NOT NULL REFERENCES contribuable(id),
  collectivite_id   bigint NOT NULL REFERENCES collectivite(id),
  type_exoneration_id bigint NOT NULL REFERENCES type_exoneration(id),
  reference_decret  varchar(32),
  date_decret       date,
  zone              varchar(2),
  date_debut        date,
  date_fin          date,
  created_by        bigint,
  created_at        timestamptz NOT NULL DEFAULT now()
);

-- Lignes de détail d'exonération (ex ligne_exo_part)
CREATE TABLE ligne_exoneration (
  id              bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  exoneration_id  bigint NOT NULL REFERENCES exoneration(id) ON DELETE CASCADE,
  nature_taxe_id  bigint NOT NULL REFERENCES nature_taxe(id),
  annee_application smallint NOT NULL,
  taux            numeric(5,2),                            -- % d'exonération
  created_by      bigint,
  created_at      timestamptz NOT NULL DEFAULT now()
);

-- =====================================================================
--  10. PILOTAGE ET PARAMÉTRAGE TRANSVERSE
-- =====================================================================

CREATE TABLE objectif (
  id              bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  collectivite_id bigint NOT NULL REFERENCES collectivite(id),
  annee           smallint NOT NULL,
  montant         numeric(18,2) NOT NULL DEFAULT 0,
  montant_revise  numeric(18,2),
  created_by      bigint,
  created_at      timestamptz NOT NULL DEFAULT now(),
  UNIQUE (collectivite_id, annee)
);

-- Paramétrage applicatif (remplace entete_etat, config_fen, modul_appli WinDev)
CREATE TABLE parametre_application (
  id              bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  collectivite_id bigint REFERENCES collectivite(id),       -- NULL = global
  cle             varchar(64) NOT NULL,
  valeur          text,
  description     varchar(255),
  UNIQUE (collectivite_id, cle)
);

-- =====================================================================
--  11. CONTRAINTES D'AUDIT (created_by / updated_by -> utilisateur)
-- =====================================================================
ALTER TABLE contribuable    ADD CONSTRAINT fk_contrib_cb FOREIGN KEY (created_by) REFERENCES utilisateur(id);
ALTER TABLE contribuable    ADD CONSTRAINT fk_contrib_ub FOREIGN KEY (updated_by) REFERENCES utilisateur(id);
ALTER TABLE etablissement   ADD CONSTRAINT fk_etab_cb    FOREIGN KEY (created_by) REFERENCES utilisateur(id);
ALTER TABLE emission_taxe   ADD CONSTRAINT fk_emis_cb    FOREIGN KEY (created_by) REFERENCES utilisateur(id);
ALTER TABLE reglement_taxe  ADD CONSTRAINT fk_regl_cb    FOREIGN KEY (created_by) REFERENCES utilisateur(id);
ALTER TABLE dossier         ADD CONSTRAINT fk_doss_cb    FOREIGN KEY (created_by) REFERENCES utilisateur(id);

-- =====================================================================
--  12. SÉCURITÉ AU NIVEAU LIGNE (multi-collectivité) - exemple
-- =====================================================================
-- ALTER TABLE contribuable ENABLE ROW LEVEL SECURITY;
-- CREATE POLICY p_contrib_coll ON contribuable
--   USING (collectivite_id = current_setting('app.collectivite_id')::bigint);

-- =====================================================================
--  FIN DU SCHÉMA
-- =====================================================================
