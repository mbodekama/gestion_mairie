-- =====================================================================
--  SCÉNARIO DE DÉMONSTRATION - test d'une émission de bout en bout
--  fiscctcidb_v2 (PostgreSQL)
--  À exécuter APRÈS : schema -> referentiel -> baremes -> securite
--  Données préfixées DEMO_ pour pouvoir les supprimer facilement.
-- =====================================================================

-- ---------------------------------------------------------------------
--  1. UNE COLLECTIVITÉ ET SA RECETTE
-- ---------------------------------------------------------------------
INSERT INTO recette (code, libelle, departement_id)
VALUES ('R01', 'Recette des Impôts - Abidjan',
        (SELECT id FROM departement WHERE code='001'));

INSERT INTO collectivite (code, libelle, type_collectivite_id, recette_id, commune_id,
                          adresse, telephone1, email)
VALUES ('C01', 'Commune de Cocody (DEMO)',
        (SELECT id FROM type_collectivite WHERE libelle ILIKE '%COMMUNE%' LIMIT 1),
        (SELECT id FROM recette WHERE code='R01'),
        (SELECT id FROM commune WHERE libelle='Cocody' LIMIT 1),
        'Boulevard de France, Cocody', '+225 27 22 44 00 00', 'mairie.cocody@demo.ci');

-- ---------------------------------------------------------------------
--  2. EXERCICE FISCAL 2026 OUVERT
-- ---------------------------------------------------------------------
INSERT INTO exercice_fiscal (annee, collectivite_id, date_debut, date_fin, cloture)
VALUES (2026, (SELECT id FROM collectivite WHERE code='C01'),
        DATE '2026-01-01', DATE '2026-12-31', false);

-- ---------------------------------------------------------------------
--  3. ZONE FISCALE ET ACTIVITÉ
-- ---------------------------------------------------------------------
INSERT INTO zone_fiscale (code, libelle, commune_id)
VALUES ('Z1', 'Zone urbaine Abidjan', (SELECT id FROM commune WHERE libelle='Cocody' LIMIT 1));

INSERT INTO activite (code, libelle, secteur_activite_id, categorie_activite_id)
VALUES ('T0001', 'Commerce général de détail',
        (SELECT id FROM secteur_activite WHERE code='TER'),
        (SELECT id FROM categorie_activite WHERE code='COM'));

-- ---------------------------------------------------------------------
--  4. UN CONTRIBUABLE (personne physique) ET SON ÉTABLISSEMENT
-- ---------------------------------------------------------------------
INSERT INTO contribuable (numero_identifiant, numero_compte, collectivite_id, type_personne,
                          nom, prenoms, sexe, telephone, email, statut)
VALUES ('DEMO00000001', 'DEMOCC01',
        (SELECT id FROM collectivite WHERE code='C01'), 'PP',
        'KOUASSI', 'Yao Patrice', 'M', '+225 07 07 07 07 07', 'patrice.kouassi@demo.ci', 'ACTIF');

INSERT INTO etablissement (numero, contribuable_id, collectivite_id, activite_id,
                           denomination, type_etablissement, commune_id, zone_fiscale_id,
                           adresse, surface, date_debut_activite, statut)
VALUES ('DEMOETAB01',
        (SELECT id FROM contribuable WHERE numero_identifiant='DEMO00000001'),
        (SELECT id FROM collectivite WHERE code='C01'),
        (SELECT id FROM activite WHERE code='T0001'),
        'Boutique Patrice', 'PRINCIPAL',
        (SELECT id FROM commune WHERE libelle='Cocody' LIMIT 1),
        (SELECT id FROM zone_fiscale WHERE code='Z1'),
        'Rue des Jardins, Cocody', 45.00, DATE '2026-02-01', 'ACTIF');

-- ---------------------------------------------------------------------
--  5. UN DOSSIER FISCAL
-- ---------------------------------------------------------------------
INSERT INTO dossier (numero, etablissement_id, collectivite_id, date_creation, motif_entree)
VALUES ('DEMODS01',
        (SELECT id FROM etablissement WHERE numero='DEMOETAB01'),
        (SELECT id FROM collectivite WHERE code='C01'),
        DATE '2026-02-05', 'Recensement initial');

-- ---------------------------------------------------------------------
--  6. ÉMISSION DE LA PATENTE (TPV) POUR 2026
--     CA annuel = 12 000 000 -> tranche 5M-30M -> taux 0,40 %
--     Montant annuel = 12 000 000 * 0,40 % = 48 000
-- ---------------------------------------------------------------------
INSERT INTO emission_taxe (numero_emission, numero_article, etablissement_id, collectivite_id,
                           dossier_id, nature_taxe_id, periodicite_id, exercice_fiscal_id,
                           ca_annuel, montant_annuel, montant_periode, date_declaration, date_liquidation)
VALUES ('DEMOEM00000001', 'ART-2026-000001',
        (SELECT id FROM etablissement WHERE numero='DEMOETAB01'),
        (SELECT id FROM collectivite WHERE code='C01'),
        (SELECT id FROM dossier WHERE numero='DEMODS01'),
        (SELECT id FROM nature_taxe WHERE code='TPV'),
        (SELECT id FROM periodicite WHERE code='ANN'),
        (SELECT ef.id FROM exercice_fiscal ef
            JOIN collectivite c ON c.id = ef.collectivite_id
            WHERE ef.annee=2026 AND c.code='C01'),
        12000000, 48000, 48000, DATE '2026-03-01', DATE '2026-03-02');

-- ---------------------------------------------------------------------
--  7. UN RÈGLEMENT PARTIEL (acompte de 20 000 sur 48 000)
-- ---------------------------------------------------------------------
INSERT INTO reglement_taxe (numero_reglement, emission_taxe_id, collectivite_id, recette_id,
                            exercice_fiscal_id, date_reglement, montant, montant_impute,
                            mode_reglement_id, type_reglement_id, numero_quittance)
VALUES ('DEMORG000001',
        (SELECT id FROM emission_taxe WHERE numero_emission='DEMOEM00000001'),
        (SELECT id FROM collectivite WHERE code='C01'),
        (SELECT id FROM recette WHERE code='R01'),
        (SELECT ef.id FROM exercice_fiscal ef
            JOIN collectivite c ON c.id = ef.collectivite_id
            WHERE ef.annee=2026 AND c.code='C01'),
        DATE '2026-03-10', 20000, 20000,
        (SELECT id FROM mode_reglement WHERE code='MOB'),     -- Mobile Money
        (SELECT id FROM type_reglement WHERE code='ACO'),     -- Acompte
        'QUIT-2026-000001');

-- =====================================================================
--  8. VÉRIFICATION DE BOUT EN BOUT
--     Contribuable -> établissement -> émission -> règlements -> solde
-- =====================================================================
SELECT  c.numero_identifiant,
        c.nom || ' ' || COALESCE(c.prenoms,'')        AS contribuable,
        e.numero                                       AS etablissement,
        nt.libelle                                     AS taxe,
        em.ca_annuel,
        em.montant_annuel                              AS montant_emis,
        COALESCE(SUM(r.montant), 0)                    AS total_regle,
        em.montant_annuel - COALESCE(SUM(r.montant),0) AS solde_du
FROM    emission_taxe em
JOIN    etablissement e   ON e.id  = em.etablissement_id
JOIN    contribuable  c   ON c.id  = e.contribuable_id
JOIN    nature_taxe   nt  ON nt.id = em.nature_taxe_id
LEFT JOIN reglement_taxe r ON r.emission_taxe_id = em.id
WHERE   em.numero_emission = 'DEMOEM00000001'
GROUP BY c.numero_identifiant, c.nom, c.prenoms, e.numero, nt.libelle,
         em.ca_annuel, em.montant_annuel;

-- Résultat attendu :
--   contribuable = KOUASSI Yao Patrice | montant_emis = 48000
--   total_regle = 20000 | solde_du = 28000
-- =====================================================================
