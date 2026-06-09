-- =====================================================================
--  BARÈMES DE TAXE - fiscctcidb_v2 (PostgreSQL)
--  À exécuter APRÈS fiscct_seed_referentiel.sql
--  Valeurs ILLUSTRATIVES - à caler sur la réglementation en vigueur.
--  Les FK sont résolues par code (indépendant des id techniques).
-- =====================================================================

-- ---------------------------------------------------------------------
--  PATENTE (TPV) : barème proportionnel par tranche de chiffre d'affaires
--  Périodicité annuelle ; taux appliqué au CA annuel.
--  ca_borne_sup = 0 signifie « tranche ouverte » (au-delà).
-- ---------------------------------------------------------------------
INSERT INTO bareme_taxe (nature_taxe_id, categorie_activite_id, periodicite_id,
                         ca_borne_inf, ca_borne_sup, taux)
VALUES
((SELECT id FROM nature_taxe WHERE code='TPV'), NULL,
 (SELECT id FROM periodicite WHERE code='ANN'),         0,    5000000, 0.5000),
((SELECT id FROM nature_taxe WHERE code='TPV'), NULL,
 (SELECT id FROM periodicite WHERE code='ANN'),   5000001,   30000000, 0.4000),
((SELECT id FROM nature_taxe WHERE code='TPV'), NULL,
 (SELECT id FROM periodicite WHERE code='ANN'),  30000001,  100000000, 0.3000),
((SELECT id FROM nature_taxe WHERE code='TPV'), NULL,
 (SELECT id FROM periodicite WHERE code='ANN'), 100000001,          0, 0.2500);

-- ---------------------------------------------------------------------
--  TAXE D'ENLÈVEMENT DES ORDURES (TEN) : barème proportionnel léger
-- ---------------------------------------------------------------------
INSERT INTO bareme_taxe (nature_taxe_id, categorie_activite_id, periodicite_id,
                         ca_borne_inf, ca_borne_sup, taux)
VALUES
((SELECT id FROM nature_taxe WHERE code='TEN'), NULL,
 (SELECT id FROM periodicite WHERE code='ANN'), 0, 0, 0.0500);

-- ---------------------------------------------------------------------
--  CATÉGORIES DE COTISATION FONCIÈRE
-- ---------------------------------------------------------------------
INSERT INTO categorie_cotisation_fonciere (code, libelle) VALUES
('HAB','Locaux à usage d''habitation'),
('COM','Locaux à usage commercial'),
('IND','Locaux à usage industriel');

-- ---------------------------------------------------------------------
--  TAXE FONCIÈRE (TFP) : cotisations forfaitaires par zone et par tranche
--  zone1 = Abidjan (urbain) ; zone2 = intérieur du pays
-- ---------------------------------------------------------------------
INSERT INTO bareme_cotisation_fonciere (nature_taxe_id, activite_id, periodicite_id,
                         ca_borne_inf, ca_borne_sup, montant_zone1, montant_zone2, forfaitaire)
VALUES
((SELECT id FROM nature_taxe WHERE code='TFP'), NULL,
 (SELECT id FROM periodicite WHERE code='ANN'),         0,   10000000,  60000,  30000, true),
((SELECT id FROM nature_taxe WHERE code='TFP'), NULL,
 (SELECT id FROM periodicite WHERE code='ANN'),  10000001,   50000000, 120000,  60000, true),
((SELECT id FROM nature_taxe WHERE code='TFP'), NULL,
 (SELECT id FROM periodicite WHERE code='ANN'),  50000001,          0, 250000, 120000, true);

-- =====================================================================
--  FIN DES BARÈMES
-- =====================================================================
