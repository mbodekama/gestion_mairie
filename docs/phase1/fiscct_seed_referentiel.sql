-- =====================================================================
--  Jeu de données de référence - fiscctcidb_v2 (PostgreSQL)
--  Territoire CI extrait de l'ancienne base + référentiels fiscaux
--  À exécuter APRÈS fiscct_schema_v2.sql
-- =====================================================================

-- PAYS
INSERT INTO pays (code, libelle, code_iso2) VALUES ('AFS', 'Afrique du sud', 'ZA');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('AGO', 'Angola', 'AO');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('AIX', 'Anguilla', 'AI');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('ALB', 'Albanie', 'AL');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('ALG', 'Algérie', 'AG');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('AND', 'Andorre', 'AD');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('ANT', 'Antilles néerlandaises', 'AN');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('ARE', 'Emirats arabes unis', 'AE');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('ARG', 'Argentine', 'AR');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('ARM', 'Arménie', 'AM');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('ATB', 'Antarctique', 'AQ');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('ATG', 'Antigua-et-Barbuda', 'AG');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('AUS', 'Australie', 'AU');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('AUT', 'Autriche', 'AT');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('AZE', 'Azerbaïdjan', 'AZ');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('BDI', 'Burundi', 'BI');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('BEL', 'Belgique', 'BE');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('BEN', 'Benin', 'BJ');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('BFA', 'Burkina Faso', 'BF');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('BGD', 'Bangladesh', 'BD');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('BGR', 'Bulgarie', 'BG');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('BHR', 'Bahreïn', 'BH');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('BHS', 'Bahamas', 'BS');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('BIH', 'Bosnie-Herzégovine', 'BA');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('BLR', 'Belarus', 'BL');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('BLZ', 'Belize', 'BZ');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('BMU', 'Bermudes', 'BM');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('BOL', 'Bolivie', 'BO');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('BRA', 'Brésil', 'BR');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('BRB', 'Barbade', 'BB');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('BRN', 'Brunei Darussalam', 'BN');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('BTN', 'Bhoutan', 'BT');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('BVT', 'Bouvet, ile', 'BV');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('BWA', 'Botswana', 'BW');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('CAF', 'Centrafricaine, République', 'CF');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('CAN', 'Canada', 'CA');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('CCK', 'Cocos (Keeling), iles', 'CC');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('CHE', 'Suisse', 'CH');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('CHL', 'Chili', 'CL');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('CHN', 'Chine', 'CN');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('CIV', 'Cote d''Ivoire', 'CI');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('CMR', 'Cameroun', 'CM');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('COD', 'Congo, République démocratique', 'CD');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('COG', 'Congo', 'CG');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('COK', 'Cook, iles', 'CK');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('COL', 'Colombie', 'CO');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('COM', 'Comores', 'KM');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('CPV', 'Cap-Vert', 'CV');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('CRI', 'Costa Rica', 'CR');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('CUB', 'Cuba', 'CU');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('CXR', 'Christmas, ile', 'CX');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('CYM', 'Caïmanes, iles', 'KY');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('CYP', 'Chypre', 'CY');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('CZE', 'Tchèque, République', 'CZ');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('DEU', 'Allemagne', 'DE');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('DJI', 'Djibouti', 'DJ');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('DMA', 'Dominique', 'DM');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('DNK', 'Danemark', 'DK');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('DOM', 'Dominicaine, République', 'DO');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('ECU', 'Equateur', 'EC');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('EGY', 'Egypte', 'EG');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('ERI', 'Erythrée', 'ER');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('ESH', 'Sahara occidental', 'EH');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('ESP', 'Espagne', 'ES');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('EST', 'Estonie', 'EE');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('ETH', 'Ethiopie', 'ET');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('FIN', 'Finlande', 'FI');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('FJI', 'Fidji', 'FJ');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('FLK', 'Falkland, iles (Malvinas)', 'FK');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('FRA', 'France', 'FR');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('FRO', 'Féroé, iles', 'FO');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('GAB', 'Gabon', 'GA');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('GBR', 'Royaume-Uni', 'GB');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('GEO', 'Géorgie', 'GE');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('GHA', 'Ghana', 'GH');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('GIB', 'Gibraltar', 'GI');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('GIN', 'Guinée', 'GN');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('GLP', 'Guadeloupe', 'GP');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('GMB', 'Gambie', 'GM');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('GNB', 'Guinée-Bissau', 'GW');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('GNQ', 'Guinée équatoriale', 'GQ');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('GRC', 'Grèce', 'GR');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('GRD', 'Grenade', 'GD');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('GRL', 'Groenland', 'GL');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('GTM', 'Guatemala', 'GT');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('GUF', 'Guyane française', 'GF');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('GUM', 'Guam', 'GU');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('GUY', 'Guyana', 'GY');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('HKG', 'Hong-Kong', 'HK');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('HMD', 'Heard, ile et McDonald, iles', 'HM');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('HND', 'Honduras', 'HN');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('HRV', 'Croatie', 'HR');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('HTI', 'Haïti', 'HT');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('HUN', 'Hongrie', 'HU');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('IDN', 'Indonésie', 'ID');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('INC', 'Inconnu', 'IN');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('IND', 'Inde', 'IN');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('IOT', 'Océan indien', 'IO');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('IRL', 'Irlande', 'IE');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('IRN', 'Iran', 'IR');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('TKM', 'Turkménistan', 'TM');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('TMP', 'Timor oriental', 'TP');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('TON', 'Tonga', 'TO');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('TTO', 'Trinité-et-Tobago', 'TT');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('TUN', 'Tunisie', 'TN');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('TUR', 'Turquie', 'TR');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('TUV', 'Tuvalu', 'TV');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('TWN', 'Taiwan, Taipei chinoise', 'TW');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('TZA', 'Tanzanie, République-unie de', 'TZ');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('UGA', 'Ouganda', 'UG');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('UKR', 'Ukraine', 'UA');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('URY', 'Uruguay', 'UY');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('USA', 'Etats-Unis', 'US');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('UZB', 'Ouzbékistan', 'UZ');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('VAT', 'Saint-Siège (Etat de la cite du Vatican)', 'VA');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('VCT', 'Saint-Vincent-et-les grenadine', 'VC');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('VEN', 'Venezuela', 'VE');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('VNM', 'Viet nam', 'VN');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('VUT', 'Vanuatu', 'VU');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('WLF', 'Wallis et futuna', 'WF');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('WSM', 'Samoa', 'WS');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('YEM', 'Yémen', 'YE');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('YUG', 'Yougoslavie', 'YU');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('ZMB', 'Zambie', 'ZM');
INSERT INTO pays (code, libelle, code_iso2) VALUES ('ZWE', 'Zimbabwe', 'ZW');

-- DISTRICTS
INSERT INTO district (code, libelle) VALUES ('001', 'District autonome d''Abidjan');
INSERT INTO district (code, libelle) VALUES ('002', 'District autonome de Yamoussoukro');
INSERT INTO district (code, libelle) VALUES ('003', 'District du Bas Sassandra');
INSERT INTO district (code, libelle) VALUES ('004', 'District de la Comoé');
INSERT INTO district (code, libelle) VALUES ('005', 'District de la Vallée du Bandama');
INSERT INTO district (code, libelle) VALUES ('006', 'District des Lacs');
INSERT INTO district (code, libelle) VALUES ('007', 'District des Lagunes');
INSERT INTO district (code, libelle) VALUES ('008', 'District des Montagnes');
INSERT INTO district (code, libelle) VALUES ('009', 'District des Savanes');
INSERT INTO district (code, libelle) VALUES ('010', 'District du Denguélé');
INSERT INTO district (code, libelle) VALUES ('011', 'District du Gôh Djiboua');
INSERT INTO district (code, libelle) VALUES ('012', 'District du Sassandra-Marahoué');
INSERT INTO district (code, libelle) VALUES ('013', 'District du Woroba');
INSERT INTO district (code, libelle) VALUES ('014', 'District du Zanzan');

-- RÉGIONS
INSERT INTO region (code, libelle, district_id) VALUES ('000', 'Abidjan', (SELECT id FROM district WHERE code = '001'));
INSERT INTO region (code, libelle, district_id) VALUES ('001', 'Agnéby-Tiassa', (SELECT id FROM district WHERE code = '007'));
INSERT INTO region (code, libelle, district_id) VALUES ('002', 'Bélier', (SELECT id FROM district WHERE code = '006'));
INSERT INTO region (code, libelle, district_id) VALUES ('003', 'Bagoué', (SELECT id FROM district WHERE code = '009'));
INSERT INTO region (code, libelle, district_id) VALUES ('004', 'Bafing', (SELECT id FROM district WHERE code = '013'));
INSERT INTO region (code, libelle, district_id) VALUES ('005', 'Béré', (SELECT id FROM district WHERE code = '013'));
INSERT INTO region (code, libelle, district_id) VALUES ('006', 'Bounkani', (SELECT id FROM district WHERE code = '014'));
INSERT INTO region (code, libelle, district_id) VALUES ('007', 'Cavally', (SELECT id FROM district WHERE code = '008'));
INSERT INTO region (code, libelle, district_id) VALUES ('008', 'Folon', (SELECT id FROM district WHERE code = '010'));
INSERT INTO region (code, libelle, district_id) VALUES ('009', 'Gôh', (SELECT id FROM district WHERE code = '011'));
INSERT INTO region (code, libelle, district_id) VALUES ('00B', 'Yamoussoukro', (SELECT id FROM district WHERE code = '002'));
INSERT INTO region (code, libelle, district_id) VALUES ('010', 'Gbêkê', (SELECT id FROM district WHERE code = '005'));
INSERT INTO region (code, libelle, district_id) VALUES ('011', 'Gbôklé', (SELECT id FROM district WHERE code = '003'));
INSERT INTO region (code, libelle, district_id) VALUES ('012', 'Gontougo', (SELECT id FROM district WHERE code = '014'));
INSERT INTO region (code, libelle, district_id) VALUES ('013', 'Grands-Ponts', (SELECT id FROM district WHERE code = '007'));
INSERT INTO region (code, libelle, district_id) VALUES ('014', 'Guémon', (SELECT id FROM district WHERE code = '008'));
INSERT INTO region (code, libelle, district_id) VALUES ('015', 'Hambol', (SELECT id FROM district WHERE code = '005'));
INSERT INTO region (code, libelle, district_id) VALUES ('016', 'Haut-Sassandra', (SELECT id FROM district WHERE code = '012'));
INSERT INTO region (code, libelle, district_id) VALUES ('017', 'Iffou', (SELECT id FROM district WHERE code = '006'));
INSERT INTO region (code, libelle, district_id) VALUES ('018', 'Indénié Djuablin', (SELECT id FROM district WHERE code = '004'));
INSERT INTO region (code, libelle, district_id) VALUES ('019', 'Kabadougou', (SELECT id FROM district WHERE code = '010'));
INSERT INTO region (code, libelle, district_id) VALUES ('020', 'Lôh-Djiboua', (SELECT id FROM district WHERE code = '011'));
INSERT INTO region (code, libelle, district_id) VALUES ('021', 'La Mé', (SELECT id FROM district WHERE code = '007'));
INSERT INTO region (code, libelle, district_id) VALUES ('022', 'Marahoué', (SELECT id FROM district WHERE code = '012'));
INSERT INTO region (code, libelle, district_id) VALUES ('023', 'Moronou', (SELECT id FROM district WHERE code = '006'));
INSERT INTO region (code, libelle, district_id) VALUES ('024', 'Nawa', (SELECT id FROM district WHERE code = '003'));
INSERT INTO region (code, libelle, district_id) VALUES ('025', 'N''Zi', (SELECT id FROM district WHERE code = '006'));
INSERT INTO region (code, libelle, district_id) VALUES ('026', 'Poro', (SELECT id FROM district WHERE code = '009'));
INSERT INTO region (code, libelle, district_id) VALUES ('027', 'San Pedro', (SELECT id FROM district WHERE code = '003'));
INSERT INTO region (code, libelle, district_id) VALUES ('028', 'Sud-Comoé', (SELECT id FROM district WHERE code = '004'));
INSERT INTO region (code, libelle, district_id) VALUES ('029', 'Tchologo', (SELECT id FROM district WHERE code = '009'));
INSERT INTO region (code, libelle, district_id) VALUES ('030', 'Tonkpi', (SELECT id FROM district WHERE code = '008'));
INSERT INTO region (code, libelle, district_id) VALUES ('031', 'Worodougou', (SELECT id FROM district WHERE code = '013'));

-- DÉPARTEMENTS
INSERT INTO departement (code, libelle, region_id) VALUES ('001', 'Abidjan', (SELECT id FROM region WHERE code = '000'));
INSERT INTO departement (code, libelle, region_id) VALUES ('002', 'Attiégouakro', (SELECT id FROM region WHERE code = '00B'));
INSERT INTO departement (code, libelle, region_id) VALUES ('003', 'Yamoussoukro', (SELECT id FROM region WHERE code = '00B'));
INSERT INTO departement (code, libelle, region_id) VALUES ('004', 'Fresco', (SELECT id FROM region WHERE code = '011'));
INSERT INTO departement (code, libelle, region_id) VALUES ('005', 'Sassandra', (SELECT id FROM region WHERE code = '011'));
INSERT INTO departement (code, libelle, region_id) VALUES ('006', 'Buyo', (SELECT id FROM region WHERE code = '024'));
INSERT INTO departement (code, libelle, region_id) VALUES ('007', 'Guéyo', (SELECT id FROM region WHERE code = '024'));
INSERT INTO departement (code, libelle, region_id) VALUES ('008', 'Méagui', (SELECT id FROM region WHERE code = '024'));
INSERT INTO departement (code, libelle, region_id) VALUES ('009', 'Soubré', (SELECT id FROM region WHERE code = '024'));
INSERT INTO departement (code, libelle, region_id) VALUES ('010', 'San Pedro', (SELECT id FROM region WHERE code = '027'));
INSERT INTO departement (code, libelle, region_id) VALUES ('011', 'Tabou', (SELECT id FROM region WHERE code = '027'));
INSERT INTO departement (code, libelle, region_id) VALUES ('012', 'Abengourou', (SELECT id FROM region WHERE code = '018'));
INSERT INTO departement (code, libelle, region_id) VALUES ('013', 'Agnibilékrou', (SELECT id FROM region WHERE code = '018'));
INSERT INTO departement (code, libelle, region_id) VALUES ('014', 'Bettié', (SELECT id FROM region WHERE code = '018'));
INSERT INTO departement (code, libelle, region_id) VALUES ('015', 'Aboisso', (SELECT id FROM region WHERE code = '028'));
INSERT INTO departement (code, libelle, region_id) VALUES ('016', 'Adiaké', (SELECT id FROM region WHERE code = '028'));
INSERT INTO departement (code, libelle, region_id) VALUES ('017', 'Grand-Bassam', (SELECT id FROM region WHERE code = '028'));
INSERT INTO departement (code, libelle, region_id) VALUES ('018', 'Tiapoum', (SELECT id FROM region WHERE code = '028'));
INSERT INTO departement (code, libelle, region_id) VALUES ('019', 'Kaniasso', (SELECT id FROM region WHERE code = '008'));
INSERT INTO departement (code, libelle, region_id) VALUES ('020', 'Minignan', (SELECT id FROM region WHERE code = '008'));
INSERT INTO departement (code, libelle, region_id) VALUES ('021', 'Gbéléban', (SELECT id FROM region WHERE code = '019'));
INSERT INTO departement (code, libelle, region_id) VALUES ('022', 'Madinani', (SELECT id FROM region WHERE code = '019'));
INSERT INTO departement (code, libelle, region_id) VALUES ('023', 'Odienné', (SELECT id FROM region WHERE code = '019'));
INSERT INTO departement (code, libelle, region_id) VALUES ('024', 'Samatiguila', (SELECT id FROM region WHERE code = '019'));
INSERT INTO departement (code, libelle, region_id) VALUES ('025', 'Séguélon', (SELECT id FROM region WHERE code = '019'));
INSERT INTO departement (code, libelle, region_id) VALUES ('026', 'Gagnoa', (SELECT id FROM region WHERE code = '009'));
INSERT INTO departement (code, libelle, region_id) VALUES ('027', 'Oumé', (SELECT id FROM region WHERE code = '009'));
INSERT INTO departement (code, libelle, region_id) VALUES ('028', 'Divo', (SELECT id FROM region WHERE code = '020'));
INSERT INTO departement (code, libelle, region_id) VALUES ('029', 'Guitry', (SELECT id FROM region WHERE code = '020'));
INSERT INTO departement (code, libelle, region_id) VALUES ('030', 'Lakota', (SELECT id FROM region WHERE code = '020'));
INSERT INTO departement (code, libelle, region_id) VALUES ('031', 'Didiévi', (SELECT id FROM region WHERE code = '002'));
INSERT INTO departement (code, libelle, region_id) VALUES ('032', 'Djékanou', (SELECT id FROM region WHERE code = '002'));
INSERT INTO departement (code, libelle, region_id) VALUES ('033', 'Tiébissou', (SELECT id FROM region WHERE code = '002'));
INSERT INTO departement (code, libelle, region_id) VALUES ('034', 'Toumodi', (SELECT id FROM region WHERE code = '002'));
INSERT INTO departement (code, libelle, region_id) VALUES ('035', 'Daoukro', (SELECT id FROM region WHERE code = '017'));
INSERT INTO departement (code, libelle, region_id) VALUES ('036', 'M''Bahiakro', (SELECT id FROM region WHERE code = '017'));
INSERT INTO departement (code, libelle, region_id) VALUES ('037', 'Prikro', (SELECT id FROM region WHERE code = '017'));
INSERT INTO departement (code, libelle, region_id) VALUES ('038', 'Arrah', (SELECT id FROM region WHERE code = '023'));
INSERT INTO departement (code, libelle, region_id) VALUES ('039', 'Bongouanou', (SELECT id FROM region WHERE code = '023'));
INSERT INTO departement (code, libelle, region_id) VALUES ('040', 'M''Batto', (SELECT id FROM region WHERE code = '023'));
INSERT INTO departement (code, libelle, region_id) VALUES ('041', 'Bocanda', (SELECT id FROM region WHERE code = '025'));
INSERT INTO departement (code, libelle, region_id) VALUES ('042', 'Dimbokro', (SELECT id FROM region WHERE code = '025'));
INSERT INTO departement (code, libelle, region_id) VALUES ('043', 'Kouassi-Kouassikro', (SELECT id FROM region WHERE code = '025'));
INSERT INTO departement (code, libelle, region_id) VALUES ('044', 'Agboville', (SELECT id FROM region WHERE code = '001'));
INSERT INTO departement (code, libelle, region_id) VALUES ('045', 'Sikensi', (SELECT id FROM region WHERE code = '001'));
INSERT INTO departement (code, libelle, region_id) VALUES ('046', 'Taabo', (SELECT id FROM region WHERE code = '001'));
INSERT INTO departement (code, libelle, region_id) VALUES ('047', 'Tiassalé', (SELECT id FROM region WHERE code = '001'));
INSERT INTO departement (code, libelle, region_id) VALUES ('048', 'Dabou', (SELECT id FROM region WHERE code = '013'));
INSERT INTO departement (code, libelle, region_id) VALUES ('049', 'Grand-Lahou', (SELECT id FROM region WHERE code = '013'));
INSERT INTO departement (code, libelle, region_id) VALUES ('050', 'Jacqueville', (SELECT id FROM region WHERE code = '013'));
INSERT INTO departement (code, libelle, region_id) VALUES ('051', 'Adzopé', (SELECT id FROM region WHERE code = '021'));
INSERT INTO departement (code, libelle, region_id) VALUES ('052', 'Akoupé', (SELECT id FROM region WHERE code = '021'));
INSERT INTO departement (code, libelle, region_id) VALUES ('053', 'Alépé', (SELECT id FROM region WHERE code = '021'));
INSERT INTO departement (code, libelle, region_id) VALUES ('054', 'Yakasse-Attobrou', (SELECT id FROM region WHERE code = '021'));
INSERT INTO departement (code, libelle, region_id) VALUES ('055', 'Blolequin', (SELECT id FROM region WHERE code = '007'));
INSERT INTO departement (code, libelle, region_id) VALUES ('056', 'Guiglo', (SELECT id FROM region WHERE code = '007'));
INSERT INTO departement (code, libelle, region_id) VALUES ('057', 'Toulepleu', (SELECT id FROM region WHERE code = '007'));
INSERT INTO departement (code, libelle, region_id) VALUES ('058', 'Bangolo', (SELECT id FROM region WHERE code = '014'));
INSERT INTO departement (code, libelle, region_id) VALUES ('059', 'Duekoué', (SELECT id FROM region WHERE code = '014'));
INSERT INTO departement (code, libelle, region_id) VALUES ('060', 'Facobly', (SELECT id FROM region WHERE code = '014'));
INSERT INTO departement (code, libelle, region_id) VALUES ('061', 'Kouibly', (SELECT id FROM region WHERE code = '014'));
INSERT INTO departement (code, libelle, region_id) VALUES ('062', 'Biankouma', (SELECT id FROM region WHERE code = '030'));
INSERT INTO departement (code, libelle, region_id) VALUES ('063', 'Sipilou', (SELECT id FROM region WHERE code = '030'));
INSERT INTO departement (code, libelle, region_id) VALUES ('064', 'Danane', (SELECT id FROM region WHERE code = '030'));
INSERT INTO departement (code, libelle, region_id) VALUES ('065', 'Man', (SELECT id FROM region WHERE code = '030'));
INSERT INTO departement (code, libelle, region_id) VALUES ('066', 'Zouan-hounien', (SELECT id FROM region WHERE code = '030'));
INSERT INTO departement (code, libelle, region_id) VALUES ('067', 'Daloa', (SELECT id FROM region WHERE code = '016'));
INSERT INTO departement (code, libelle, region_id) VALUES ('068', 'Issia', (SELECT id FROM region WHERE code = '016'));
INSERT INTO departement (code, libelle, region_id) VALUES ('069', 'Vavoua', (SELECT id FROM region WHERE code = '016'));
INSERT INTO departement (code, libelle, region_id) VALUES ('070', 'Zoukougbeu', (SELECT id FROM region WHERE code = '016'));
INSERT INTO departement (code, libelle, region_id) VALUES ('071', 'Bouaflé', (SELECT id FROM region WHERE code = '022'));
INSERT INTO departement (code, libelle, region_id) VALUES ('072', 'Sinfra', (SELECT id FROM region WHERE code = '022'));
INSERT INTO departement (code, libelle, region_id) VALUES ('073', 'Zuenoula', (SELECT id FROM region WHERE code = '022'));
INSERT INTO departement (code, libelle, region_id) VALUES ('074', 'Bonon', (SELECT id FROM region WHERE code = '022'));
INSERT INTO departement (code, libelle, region_id) VALUES ('075', 'Boundiali', (SELECT id FROM region WHERE code = '003'));
INSERT INTO departement (code, libelle, region_id) VALUES ('076', 'Kouto', (SELECT id FROM region WHERE code = '003'));
INSERT INTO departement (code, libelle, region_id) VALUES ('077', 'Tengrela', (SELECT id FROM region WHERE code = '003'));
INSERT INTO departement (code, libelle, region_id) VALUES ('078', 'Dikodougou', (SELECT id FROM region WHERE code = '026'));
INSERT INTO departement (code, libelle, region_id) VALUES ('079', 'Korhogo', (SELECT id FROM region WHERE code = '026'));
INSERT INTO departement (code, libelle, region_id) VALUES ('080', 'M''Bengué', (SELECT id FROM region WHERE code = '026'));
INSERT INTO departement (code, libelle, region_id) VALUES ('081', 'Sinématiali', (SELECT id FROM region WHERE code = '026'));
INSERT INTO departement (code, libelle, region_id) VALUES ('082', 'Ferkessédougou', (SELECT id FROM region WHERE code = '029'));
INSERT INTO departement (code, libelle, region_id) VALUES ('083', 'Kong', (SELECT id FROM region WHERE code = '029'));
INSERT INTO departement (code, libelle, region_id) VALUES ('084', 'Ouangolodougou', (SELECT id FROM region WHERE code = '029'));
INSERT INTO departement (code, libelle, region_id) VALUES ('085', 'Béoumi', (SELECT id FROM region WHERE code = '010'));
INSERT INTO departement (code, libelle, region_id) VALUES ('086', 'Botro', (SELECT id FROM region WHERE code = '010'));
INSERT INTO departement (code, libelle, region_id) VALUES ('087', 'Bouaké', (SELECT id FROM region WHERE code = '010'));
INSERT INTO departement (code, libelle, region_id) VALUES ('088', 'Sakassou', (SELECT id FROM region WHERE code = '010'));
INSERT INTO departement (code, libelle, region_id) VALUES ('089', 'Dabakala', (SELECT id FROM region WHERE code = '015'));
INSERT INTO departement (code, libelle, region_id) VALUES ('090', 'Katiola', (SELECT id FROM region WHERE code = '015'));
INSERT INTO departement (code, libelle, region_id) VALUES ('091', 'Niakaramadougou', (SELECT id FROM region WHERE code = '015'));
INSERT INTO departement (code, libelle, region_id) VALUES ('092', 'Koro', (SELECT id FROM region WHERE code = '004'));
INSERT INTO departement (code, libelle, region_id) VALUES ('093', 'Ouaninou', (SELECT id FROM region WHERE code = '004'));
INSERT INTO departement (code, libelle, region_id) VALUES ('094', 'Touba', (SELECT id FROM region WHERE code = '004'));
INSERT INTO departement (code, libelle, region_id) VALUES ('095', 'Dianra', (SELECT id FROM region WHERE code = '005'));
INSERT INTO departement (code, libelle, region_id) VALUES ('096', 'Kounahiri', (SELECT id FROM region WHERE code = '005'));
INSERT INTO departement (code, libelle, region_id) VALUES ('097', 'Mankono', (SELECT id FROM region WHERE code = '005'));
INSERT INTO departement (code, libelle, region_id) VALUES ('098', 'Kani', (SELECT id FROM region WHERE code = '031'));
INSERT INTO departement (code, libelle, region_id) VALUES ('099', 'Séguéla', (SELECT id FROM region WHERE code = '031'));
INSERT INTO departement (code, libelle, region_id) VALUES ('100', 'Bouna', (SELECT id FROM region WHERE code = '006'));
INSERT INTO departement (code, libelle, region_id) VALUES ('101', 'Doropo', (SELECT id FROM region WHERE code = '006'));
INSERT INTO departement (code, libelle, region_id) VALUES ('102', 'Nassian', (SELECT id FROM region WHERE code = '006'));
INSERT INTO departement (code, libelle, region_id) VALUES ('103', 'Téhini', (SELECT id FROM region WHERE code = '006'));
INSERT INTO departement (code, libelle, region_id) VALUES ('104', 'Bondoukou', (SELECT id FROM region WHERE code = '012'));
INSERT INTO departement (code, libelle, region_id) VALUES ('105', 'Koun-fao', (SELECT id FROM region WHERE code = '012'));
INSERT INTO departement (code, libelle, region_id) VALUES ('106', 'Sandégué', (SELECT id FROM region WHERE code = '012'));
INSERT INTO departement (code, libelle, region_id) VALUES ('107', 'Tanda', (SELECT id FROM region WHERE code = '012'));
INSERT INTO departement (code, libelle, region_id) VALUES ('108', 'Transua', (SELECT id FROM region WHERE code = '012'));

-- SOUS-PRÉFECTURES
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('001', 'Abidjan', (SELECT id FROM departement WHERE code = '001'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('002', 'Anyama', (SELECT id FROM departement WHERE code = '001'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('003', 'Bingerville', (SELECT id FROM departement WHERE code = '001'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('004', 'Brofodoumé', (SELECT id FROM departement WHERE code = '001'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('005', 'Songon', (SELECT id FROM departement WHERE code = '001'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('006', 'Attiégouakro', (SELECT id FROM departement WHERE code = '002'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('007', 'Lolobo', (SELECT id FROM departement WHERE code = '002'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('008', 'Kossou', (SELECT id FROM departement WHERE code = '003'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('009', 'Yamoussoukro', (SELECT id FROM departement WHERE code = '003'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('010', 'Aboudé', (SELECT id FROM departement WHERE code = '004'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('011', 'Agboville', (SELECT id FROM departement WHERE code = '004'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('012', 'Ananguié', (SELECT id FROM departement WHERE code = '004'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('013', 'Attobrou', (SELECT id FROM departement WHERE code = '004'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('014', 'Azaguié', (SELECT id FROM departement WHERE code = '004'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('015', 'Céchi', (SELECT id FROM departement WHERE code = '004'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('016', 'Grand-morié', (SELECT id FROM departement WHERE code = '004'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('017', 'Guessiguié', (SELECT id FROM departement WHERE code = '004'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('018', 'Loviguié', (SELECT id FROM departement WHERE code = '004'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('019', 'Oress Krobou', (SELECT id FROM departement WHERE code = '004'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('020', 'Rubino', (SELECT id FROM departement WHERE code = '004'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('021', 'Gomon', (SELECT id FROM departement WHERE code = '045'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('022', 'Sikensi', (SELECT id FROM departement WHERE code = '045'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('023', 'Pacobo', (SELECT id FROM departement WHERE code = '046'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('024', 'Taabo', (SELECT id FROM departement WHERE code = '046'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('025', 'Gbolouville', (SELECT id FROM departement WHERE code = '047'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('026', 'Morokro', (SELECT id FROM departement WHERE code = '047'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('027', 'N’Douci', (SELECT id FROM departement WHERE code = '047'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('028', 'Tiassalé', (SELECT id FROM departement WHERE code = '047'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('029', 'Booko', (SELECT id FROM departement WHERE code = '093'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('030', 'Borotou', (SELECT id FROM departement WHERE code = '093'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('031', 'Koro', (SELECT id FROM departement WHERE code = '093'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('032', 'Mahandougou', (SELECT id FROM departement WHERE code = '093'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('033', 'Niokosso', (SELECT id FROM departement WHERE code = '093'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('034', 'Koonan', (SELECT id FROM departement WHERE code = '094'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('035', 'Ouaninou', (SELECT id FROM departement WHERE code = '094'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('036', 'Saboudougou', (SELECT id FROM departement WHERE code = '094'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('037', 'Foungbesso', (SELECT id FROM departement WHERE code = '095'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('038', 'Guintéguéla', (SELECT id FROM departement WHERE code = '095'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('039', 'Touba', (SELECT id FROM departement WHERE code = '095'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('040', 'Baya', (SELECT id FROM departement WHERE code = '076'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('041', 'Boundiali', (SELECT id FROM departement WHERE code = '076'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('042', 'Ganaoni', (SELECT id FROM departement WHERE code = '076'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('043', 'Kasseré', (SELECT id FROM departement WHERE code = '076'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('044', 'Siempurgo', (SELECT id FROM departement WHERE code = '076'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('045', 'Blességué', (SELECT id FROM departement WHERE code = '077'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('046', 'Gbon', (SELECT id FROM departement WHERE code = '077'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('047', 'Kolia', (SELECT id FROM departement WHERE code = '077'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('048', 'Kouto', (SELECT id FROM departement WHERE code = '077'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('049', 'Sianhala', (SELECT id FROM departement WHERE code = '077'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('050', 'Dèbètè', (SELECT id FROM departement WHERE code = '078'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('051', 'Kanakono', (SELECT id FROM departement WHERE code = '078'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('052', 'Papara', (SELECT id FROM departement WHERE code = '078'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('053', 'Tengrela', (SELECT id FROM departement WHERE code = '078'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('054', 'Boli', (SELECT id FROM departement WHERE code = '031'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('055', 'Didiévi', (SELECT id FROM departement WHERE code = '031'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('056', 'Molonou-blé', (SELECT id FROM departement WHERE code = '031'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('057', 'Raviart', (SELECT id FROM departement WHERE code = '031'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('058', 'Tié-N’Diékro', (SELECT id FROM departement WHERE code = '031'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('059', 'Bonikro', (SELECT id FROM departement WHERE code = '032'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('060', 'Djékanou', (SELECT id FROM departement WHERE code = '032'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('061', 'Lomokankro', (SELECT id FROM departement WHERE code = '033'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('062', 'Molonou', (SELECT id FROM departement WHERE code = '033'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('063', 'Tiébissou', (SELECT id FROM departement WHERE code = '033'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('064', 'Yakpabo-sakassou', (SELECT id FROM departement WHERE code = '033'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('065', 'Angoda', (SELECT id FROM departement WHERE code = '034'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('066', 'Kokumbo', (SELECT id FROM departement WHERE code = '034'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('067', 'Kpouébo', (SELECT id FROM departement WHERE code = '034'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('068', 'Toumodi', (SELECT id FROM departement WHERE code = '034'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('069', 'Dianra', (SELECT id FROM departement WHERE code = '096'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('070', 'Dianra-Village', (SELECT id FROM departement WHERE code = '096'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('071', 'Kongasso', (SELECT id FROM departement WHERE code = '097'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('072', 'Kounahiri', (SELECT id FROM departement WHERE code = '097'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('073', 'Bouandougou', (SELECT id FROM departement WHERE code = '098'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('074', 'Mankono', (SELECT id FROM departement WHERE code = '098'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('075', 'Marhandallah', (SELECT id FROM departement WHERE code = '098'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('076', 'Sarhala', (SELECT id FROM departement WHERE code = '098'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('077', 'Tiéningboué', (SELECT id FROM departement WHERE code = '098'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('078', 'Bouko', (SELECT id FROM departement WHERE code = '101'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('079', 'Bouna', (SELECT id FROM departement WHERE code = '101'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('080', 'Ondéfidouo', (SELECT id FROM departement WHERE code = '101'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('081', 'Youndouo', (SELECT id FROM departement WHERE code = '101'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('082', 'Doropo', (SELECT id FROM departement WHERE code = '102'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('083', 'Niamoué', (SELECT id FROM departement WHERE code = '102'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('084', 'Kotouba', (SELECT id FROM departement WHERE code = '103'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('085', 'Nassian', (SELECT id FROM departement WHERE code = '103'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('086', 'Sominassé', (SELECT id FROM departement WHERE code = '103'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('087', 'Gogo', (SELECT id FROM departement WHERE code = '104'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('088', 'Téhini', (SELECT id FROM departement WHERE code = '104'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('089', 'Tougbo', (SELECT id FROM departement WHERE code = '104'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('090', 'Bloléquin', (SELECT id FROM departement WHERE code = '055'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('091', 'Doké', (SELECT id FROM departement WHERE code = '055'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('092', 'Zéaglo', (SELECT id FROM departement WHERE code = '055'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('093', 'Guiglo', (SELECT id FROM departement WHERE code = '056'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('094', 'Kaadé', (SELECT id FROM departement WHERE code = '056'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('095', 'Tai', (SELECT id FROM departement WHERE code = '057'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('096', 'Zagné', (SELECT id FROM departement WHERE code = '057'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('097', 'Bakoubly', (SELECT id FROM departement WHERE code = '058'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('098', 'Méo', (SELECT id FROM departement WHERE code = '058'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('099', 'Péhé', (SELECT id FROM departement WHERE code = '058'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('100', 'Tiobly', (SELECT id FROM departement WHERE code = '058'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('101', 'Toulepleu', (SELECT id FROM departement WHERE code = '058'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('102', 'Goulia', (SELECT id FROM departement WHERE code = '019'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('103', 'Kaniasso', (SELECT id FROM departement WHERE code = '019'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('104', 'Mahandiana-Sokourani', (SELECT id FROM departement WHERE code = '019'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('105', 'Kimbirila-Nord', (SELECT id FROM departement WHERE code = '020'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('106', 'Minignan', (SELECT id FROM departement WHERE code = '020'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('107', 'Sokoro', (SELECT id FROM departement WHERE code = '020'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('108', 'Tienko', (SELECT id FROM departement WHERE code = '020'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('109', 'Ando-kékrénou', (SELECT id FROM departement WHERE code = '086'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('110', 'Béoumi', (SELECT id FROM departement WHERE code = '086'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('111', 'Bodokro', (SELECT id FROM departement WHERE code = '086'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('112', 'Kondrobo', (SELECT id FROM departement WHERE code = '086'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('113', 'Lolobo', (SELECT id FROM departement WHERE code = '086'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('114', 'Marabadjassa', (SELECT id FROM departement WHERE code = '086'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('115', 'NGuessankro', (SELECT id FROM departement WHERE code = '086'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('116', 'Botro', (SELECT id FROM departement WHERE code = '087'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('117', 'Diabo', (SELECT id FROM departement WHERE code = '087'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('118', 'Languibonou', (SELECT id FROM departement WHERE code = '087'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('119', 'Bouaké', (SELECT id FROM departement WHERE code = '088'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('120', 'Brobo', (SELECT id FROM departement WHERE code = '088'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('121', 'Djebonoua', (SELECT id FROM departement WHERE code = '088'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('122', 'Ayaou-Sran', (SELECT id FROM departement WHERE code = '089'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('123', 'Dibri-Asrikro', (SELECT id FROM departement WHERE code = '089'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('124', 'Sakassou', (SELECT id FROM departement WHERE code = '089'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('125', 'Toumodi-Sakassou', (SELECT id FROM departement WHERE code = '089'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('126', 'Dahiri', (SELECT id FROM departement WHERE code = '004'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('127', 'Fresco', (SELECT id FROM departement WHERE code = '004'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('128', 'Gbagbam', (SELECT id FROM departement WHERE code = '004'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('129', 'Dakpadou', (SELECT id FROM departement WHERE code = '005'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('130', 'Grihiri', (SELECT id FROM departement WHERE code = '005'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('131', 'Lobakuya', (SELECT id FROM departement WHERE code = '005'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('132', 'Médon', (SELECT id FROM departement WHERE code = '005'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('133', 'Sago', (SELECT id FROM departement WHERE code = '005'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('134', 'Sassandra', (SELECT id FROM departement WHERE code = '005'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('135', 'Bayota', (SELECT id FROM departement WHERE code = '026'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('136', 'Dahiépa-Kéhi', (SELECT id FROM departement WHERE code = '026'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('137', 'Dignago', (SELECT id FROM departement WHERE code = '026'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('138', 'Gagnoa', (SELECT id FROM departement WHERE code = '026'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('139', 'Galébré', (SELECT id FROM departement WHERE code = '026'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('140', 'Gnangbodougnoa', (SELECT id FROM departement WHERE code = '026'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('141', 'Guibéroua', (SELECT id FROM departement WHERE code = '026'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('142', 'Ouragahio', (SELECT id FROM departement WHERE code = '026'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('143', 'Sérihio', (SELECT id FROM departement WHERE code = '026'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('144', 'Yopohué', (SELECT id FROM departement WHERE code = '026'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('145', 'Diégonéfla', (SELECT id FROM departement WHERE code = '027'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('146', 'Guépahouo', (SELECT id FROM departement WHERE code = '027'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('147', 'Oumé', (SELECT id FROM departement WHERE code = '027'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('148', 'Tonla', (SELECT id FROM departement WHERE code = '027'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('149', 'Appimandoum', (SELECT id FROM departement WHERE code = '105'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('150', 'Bondo', (SELECT id FROM departement WHERE code = '105'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('151', 'Bondoukou', (SELECT id FROM departement WHERE code = '105'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('152', 'Gouméré', (SELECT id FROM departement WHERE code = '105'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('153', 'Laoudi-Ba', (SELECT id FROM departement WHERE code = '105'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('154', 'Pinda-Boroko', (SELECT id FROM departement WHERE code = '105'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('155', 'Sapli-Sépingo', (SELECT id FROM departement WHERE code = '105'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('156', 'Sorobango', (SELECT id FROM departement WHERE code = '105'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('157', 'Tabagne', (SELECT id FROM departement WHERE code = '105'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('158', 'Tagadi', (SELECT id FROM departement WHERE code = '105'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('159', 'Taoudi', (SELECT id FROM departement WHERE code = '105'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('160', 'Yézimala', (SELECT id FROM departement WHERE code = '105'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('161', 'Boahia', (SELECT id FROM departement WHERE code = '106'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('162', 'Kokomian', (SELECT id FROM departement WHERE code = '106'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('163', 'Kouassi Datékro', (SELECT id FROM departement WHERE code = '106'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('164', 'Koun-Fao', (SELECT id FROM departement WHERE code = '106'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('165', 'Tankéssé', (SELECT id FROM departement WHERE code = '106'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('166', 'Tienkoikro', (SELECT id FROM departement WHERE code = '106'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('167', 'Bandakagni-Tomora', (SELECT id FROM departement WHERE code = '107'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('168', 'Dimandougou', (SELECT id FROM departement WHERE code = '107'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('169', 'Sandégué', (SELECT id FROM departement WHERE code = '107'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('170', 'Yorobodi', (SELECT id FROM departement WHERE code = '107'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('171', 'Amanvi', (SELECT id FROM departement WHERE code = '108'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('172', 'Diamba', (SELECT id FROM departement WHERE code = '108'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('173', 'Tanda', (SELECT id FROM departement WHERE code = '108'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('174', 'Tiédio', (SELECT id FROM departement WHERE code = '108'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('175', 'Assuéfry', (SELECT id FROM departement WHERE code = '108'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('176', 'Kouassia-Niaguini', (SELECT id FROM departement WHERE code = '108'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('177', 'Transua', (SELECT id FROM departement WHERE code = '108'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('178', 'Dabou', (SELECT id FROM departement WHERE code = '048'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('179', 'Lopou', (SELECT id FROM departement WHERE code = '048'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('180', 'Toupah', (SELECT id FROM departement WHERE code = '048'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('181', 'Ahouanou', (SELECT id FROM departement WHERE code = '049'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('182', 'Bacanda', (SELECT id FROM departement WHERE code = '049'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('183', 'Ebounou', (SELECT id FROM departement WHERE code = '049'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('184', 'Grand-Lahou', (SELECT id FROM departement WHERE code = '049'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('185', 'Toukouzou', (SELECT id FROM departement WHERE code = '049'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('186', 'Attoutou', (SELECT id FROM departement WHERE code = '050'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('187', 'Jacqueville', (SELECT id FROM departement WHERE code = '050'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('188', 'Bangolo', (SELECT id FROM departement WHERE code = '059'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('189', 'Béoue-Zibiao', (SELECT id FROM departement WHERE code = '059'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('190', 'Bléniméouin', (SELECT id FROM departement WHERE code = '059'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('191', 'Diéouzon', (SELECT id FROM departement WHERE code = '059'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('192', 'Gohouo-Zagna', (SELECT id FROM departement WHERE code = '059'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('193', 'Guinglo-Tahouaké', (SELECT id FROM departement WHERE code = '059'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('194', 'Kahin-Zarabaon', (SELECT id FROM departement WHERE code = '059'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('195', 'Zéo', (SELECT id FROM departement WHERE code = '059'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('196', 'Zou', (SELECT id FROM departement WHERE code = '059'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('197', 'Bagohouo', (SELECT id FROM departement WHERE code = '060'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('198', 'Duékoué', (SELECT id FROM departement WHERE code = '060'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('199', 'Gbapleu', (SELECT id FROM departement WHERE code = '060'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('200', 'Guéhiébly', (SELECT id FROM departement WHERE code = '060'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('201', 'Guézon', (SELECT id FROM departement WHERE code = '060'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('202', 'Facobly', (SELECT id FROM departement WHERE code = '061'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('203', 'Koua', (SELECT id FROM departement WHERE code = '061'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('204', 'Sémien', (SELECT id FROM departement WHERE code = '061'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('205', 'Tiény-Siably', (SELECT id FROM departement WHERE code = '061'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('206', 'Kouibly', (SELECT id FROM departement WHERE code = '062'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('207', 'Nidrou', (SELECT id FROM departement WHERE code = '062'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('208', 'Ouyably gnondrou', (SELECT id FROM departement WHERE code = '062'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('209', 'Totrodrou', (SELECT id FROM departement WHERE code = '062'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('210', 'Bassawa', (SELECT id FROM departement WHERE code = '090'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('211', 'Boniérédougou', (SELECT id FROM departement WHERE code = '090'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('212', 'Dabakala', (SELECT id FROM departement WHERE code = '090'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('213', 'Foumbolo', (SELECT id FROM departement WHERE code = '090'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('214', 'Niéméné', (SELECT id FROM departement WHERE code = '090'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('215', 'Satama-Sokoro', (SELECT id FROM departement WHERE code = '090'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('216', 'Satama-Sokoura', (SELECT id FROM departement WHERE code = '090'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('217', 'Sokala-Sobara', (SELECT id FROM departement WHERE code = '090'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('218', 'Fronan', (SELECT id FROM departement WHERE code = '091'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('219', 'Katiola', (SELECT id FROM departement WHERE code = '091'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('220', 'Timbé', (SELECT id FROM departement WHERE code = '091'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('221', 'Arikokaha', (SELECT id FROM departement WHERE code = '092'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('222', 'Badikaha', (SELECT id FROM departement WHERE code = '092'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('223', 'Niakaramandougou', (SELECT id FROM departement WHERE code = '092'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('224', 'Niédiékaha', (SELECT id FROM departement WHERE code = '092'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('225', 'Tafiré', (SELECT id FROM departement WHERE code = '092'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('226', 'Tortiya', (SELECT id FROM departement WHERE code = '092'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('227', 'Bédiala', (SELECT id FROM departement WHERE code = '068'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('228', 'Daloa', (SELECT id FROM departement WHERE code = '068'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('229', 'Gadouan', (SELECT id FROM departement WHERE code = '068'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('230', 'Gboguhé', (SELECT id FROM departement WHERE code = '068'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('231', 'Gonaté', (SELECT id FROM departement WHERE code = '068'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('232', 'Zaïbo', (SELECT id FROM departement WHERE code = '068'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('233', 'Boguédia', (SELECT id FROM departement WHERE code = '069'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('234', 'Iboguhé', (SELECT id FROM departement WHERE code = '069'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('235', 'Issia', (SELECT id FROM departement WHERE code = '069'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('236', 'Nahio', (SELECT id FROM departement WHERE code = '069'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('237', 'Namané', (SELECT id FROM departement WHERE code = '069'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('238', 'Saïoua', (SELECT id FROM departement WHERE code = '069'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('239', 'Tapéguia', (SELECT id FROM departement WHERE code = '069'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('240', 'Dania', (SELECT id FROM departement WHERE code = '070'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('241', 'Séitifla', (SELECT id FROM departement WHERE code = '070'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('242', 'Vavoua', (SELECT id FROM departement WHERE code = '070'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('243', 'Grégbeu', (SELECT id FROM departement WHERE code = '071'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('244', 'Guéssabo', (SELECT id FROM departement WHERE code = '071'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('245', 'Zoukougbeu', (SELECT id FROM departement WHERE code = '071'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('246', 'Ananda', (SELECT id FROM departement WHERE code = '035'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('247', 'Daoukro', (SELECT id FROM departement WHERE code = '035'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('248', 'Ettrokro', (SELECT id FROM departement WHERE code = '035'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('249', 'Ouellé', (SELECT id FROM departement WHERE code = '035'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('250', 'Bonguéra', (SELECT id FROM departement WHERE code = '036'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('251', 'MBahiakro', (SELECT id FROM departement WHERE code = '036'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('252', 'Anianou', (SELECT id FROM departement WHERE code = '037'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('253', 'Famienkro', (SELECT id FROM departement WHERE code = '037'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('254', 'Koffi-Amonkro', (SELECT id FROM departement WHERE code = '037'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('255', 'Nafana', (SELECT id FROM departement WHERE code = '037'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('256', 'Prikro', (SELECT id FROM departement WHERE code = '037'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('257', 'Abengourou', (SELECT id FROM departement WHERE code = '012'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('258', 'Amélékia', (SELECT id FROM departement WHERE code = '012'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('259', 'Aniassué', (SELECT id FROM departement WHERE code = '012'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('260', 'Ebilassokro', (SELECT id FROM departement WHERE code = '012'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('261', 'Niablé', (SELECT id FROM departement WHERE code = '012'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('262', 'Yakassé-Féyassé', (SELECT id FROM departement WHERE code = '012'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('263', 'Zaranou', (SELECT id FROM departement WHERE code = '012'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('264', 'Agnibilékrou', (SELECT id FROM departement WHERE code = '013'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('265', 'Akoboissué', (SELECT id FROM departement WHERE code = '013'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('266', 'Damé', (SELECT id FROM departement WHERE code = '013'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('267', 'Duffrébo', (SELECT id FROM departement WHERE code = '013'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('268', 'Tanguelan', (SELECT id FROM departement WHERE code = '013'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('269', 'Bettié', (SELECT id FROM departement WHERE code = '014'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('270', 'Diamarakro', (SELECT id FROM departement WHERE code = '014'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('271', 'Gbéléban', (SELECT id FROM departement WHERE code = '021'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('272', 'Samango', (SELECT id FROM departement WHERE code = '021'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('273', 'Seydougou', (SELECT id FROM departement WHERE code = '021'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('274', 'Fengolo', (SELECT id FROM departement WHERE code = '022'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('275', 'Madinani', (SELECT id FROM departement WHERE code = '022'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('276', 'N’Goloblasso', (SELECT id FROM departement WHERE code = '022'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('277', 'Bako', (SELECT id FROM departement WHERE code = '023'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('278', 'Bougousso', (SELECT id FROM departement WHERE code = '023'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('279', 'Dioulatiédougou', (SELECT id FROM departement WHERE code = '023'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('280', 'Odienné', (SELECT id FROM departement WHERE code = '023'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('281', 'Tiémé', (SELECT id FROM departement WHERE code = '023'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('282', 'Kimirila Sud', (SELECT id FROM departement WHERE code = '024'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('283', 'Samatiguila', (SELECT id FROM departement WHERE code = '024'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('284', 'Séguélon', (SELECT id FROM departement WHERE code = '025'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('285', 'Gbongaha', (SELECT id FROM departement WHERE code = '025'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('286', 'Adzopé', (SELECT id FROM departement WHERE code = '051'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('287', 'Agou', (SELECT id FROM departement WHERE code = '051'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('288', 'Annépé', (SELECT id FROM departement WHERE code = '051'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('289', 'Assikoi', (SELECT id FROM departement WHERE code = '051'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('290', 'Bécédi-Brignan', (SELECT id FROM departement WHERE code = '051'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('291', 'Yakassé-Mé', (SELECT id FROM departement WHERE code = '051'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('292', 'Afféry', (SELECT id FROM departement WHERE code = '052'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('293', 'Akoupé', (SELECT id FROM departement WHERE code = '052'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('294', 'Bécouéfin', (SELECT id FROM departement WHERE code = '052'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('295', 'Aboisso-Comoé', (SELECT id FROM departement WHERE code = '053'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('296', 'Alépé', (SELECT id FROM departement WHERE code = '053'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('297', 'Allosso', (SELECT id FROM departement WHERE code = '053'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('298', 'Danguira', (SELECT id FROM departement WHERE code = '053'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('299', 'Oghlwapo', (SELECT id FROM departement WHERE code = '053'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('300', 'Abongoua', (SELECT id FROM departement WHERE code = '054'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('301', 'Biéby', (SELECT id FROM departement WHERE code = '054'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('302', 'Yakassé-Attobrou', (SELECT id FROM departement WHERE code = '054'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('303', 'Didoko', (SELECT id FROM departement WHERE code = '028'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('304', 'Divo', (SELECT id FROM departement WHERE code = '028'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('305', 'Hiré', (SELECT id FROM departement WHERE code = '028'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('306', 'Nébo', (SELECT id FROM departement WHERE code = '028'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('307', 'Ogoudou', (SELECT id FROM departement WHERE code = '028'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('308', 'Daïro-Didizo', (SELECT id FROM departement WHERE code = '029'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('309', 'Guitry', (SELECT id FROM departement WHERE code = '029'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('310', 'Lauzoua', (SELECT id FROM departement WHERE code = '029'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('311', 'Yocoboué', (SELECT id FROM departement WHERE code = '029'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('312', 'Djidji', (SELECT id FROM departement WHERE code = '030'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('313', 'Gagoré', (SELECT id FROM departement WHERE code = '030'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('314', 'Goudouko', (SELECT id FROM departement WHERE code = '030'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('315', 'Lakota', (SELECT id FROM departement WHERE code = '030'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('316', 'Niambézaria', (SELECT id FROM departement WHERE code = '030'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('317', 'Zikisso', (SELECT id FROM departement WHERE code = '030'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('318', 'Bégbéssou', (SELECT id FROM departement WHERE code = '072'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('319', 'Bonon', (SELECT id FROM departement WHERE code = '072'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('320', 'Bouaflé', (SELECT id FROM departement WHERE code = '072'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('321', 'Ndouffoukankro', (SELECT id FROM departement WHERE code = '072'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('322', 'Pakouabo', (SELECT id FROM departement WHERE code = '072'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('323', 'Tibéita', (SELECT id FROM departement WHERE code = '072'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('324', 'Zaguiéta', (SELECT id FROM departement WHERE code = '072'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('325', 'Bazré', (SELECT id FROM departement WHERE code = '073'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('326', 'Kononfla', (SELECT id FROM departement WHERE code = '073'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('327', 'Kouétinfla', (SELECT id FROM departement WHERE code = '073'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('328', 'Sinfra', (SELECT id FROM departement WHERE code = '073'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('329', 'Gohitafla', (SELECT id FROM departement WHERE code = '074'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('330', 'Iriéfla', (SELECT id FROM departement WHERE code = '074'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('331', 'Kanzra', (SELECT id FROM departement WHERE code = '074'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('332', 'Maminigui', (SELECT id FROM departement WHERE code = '074'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('333', 'Vouéboufla', (SELECT id FROM departement WHERE code = '074'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('334', 'Zuénoula', (SELECT id FROM departement WHERE code = '074'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('335', 'Arrah', (SELECT id FROM departement WHERE code = '038'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('336', 'Kotobi', (SELECT id FROM departement WHERE code = '038'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('337', 'Krégbé', (SELECT id FROM departement WHERE code = '038'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('338', 'Andé', (SELECT id FROM departement WHERE code = '039'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('339', 'Assié-Koumassi', (SELECT id FROM departement WHERE code = '039'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('340', 'Bongouanou', (SELECT id FROM departement WHERE code = '039'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('341', 'NGuessankro', (SELECT id FROM departement WHERE code = '039'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('342', 'Anoumaba', (SELECT id FROM departement WHERE code = '040'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('343', 'Assahara', (SELECT id FROM departement WHERE code = '040'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('344', 'MBatto', (SELECT id FROM departement WHERE code = '040'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('345', 'Tiémélékro', (SELECT id FROM departement WHERE code = '040'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('346', 'Buyo', (SELECT id FROM departement WHERE code = '006'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('347', 'Dabouyo', (SELECT id FROM departement WHERE code = '007'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('348', 'Guéyo', (SELECT id FROM departement WHERE code = '007'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('349', 'Méagui', (SELECT id FROM departement WHERE code = '008'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('350', 'Oupoyo', (SELECT id FROM departement WHERE code = '008'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('351', 'Grand-Zattry', (SELECT id FROM departement WHERE code = '009'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('352', 'Lliliyo', (SELECT id FROM departement WHERE code = '009'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('353', 'Mayo', (SELECT id FROM departement WHERE code = '009'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('354', 'Okrouyo', (SELECT id FROM departement WHERE code = '009'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('355', 'Soubré', (SELECT id FROM departement WHERE code = '009'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('356', 'Bengassou', (SELECT id FROM departement WHERE code = '041'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('357', 'Bocanda', (SELECT id FROM departement WHERE code = '041'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('358', 'Kouadioblékro', (SELECT id FROM departement WHERE code = '041'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('359', 'NZecrezessou', (SELECT id FROM departement WHERE code = '041'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('360', 'Abigui', (SELECT id FROM departement WHERE code = '042'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('361', 'Dimbokro', (SELECT id FROM departement WHERE code = '042'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('362', 'Djangokro', (SELECT id FROM departement WHERE code = '042'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('363', 'Nofou', (SELECT id FROM departement WHERE code = '042'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('364', 'Kouassi-Kouassikro', (SELECT id FROM departement WHERE code = '043'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('365', 'Mékro', (SELECT id FROM departement WHERE code = '043'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('366', 'Boron', (SELECT id FROM departement WHERE code = '079'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('367', 'Dikodougou', (SELECT id FROM departement WHERE code = '079'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('368', 'Guiembe', (SELECT id FROM departement WHERE code = '079'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('369', 'Dassoungboho', (SELECT id FROM departement WHERE code = '080'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('370', 'Kanoroba', (SELECT id FROM departement WHERE code = '080'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('371', 'Karakoro', (SELECT id FROM departement WHERE code = '080'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('372', 'Kiémou', (SELECT id FROM departement WHERE code = '080'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('373', 'Kombolokoura', (SELECT id FROM departement WHERE code = '080'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('374', 'Komborodougou', (SELECT id FROM departement WHERE code = '080'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('375', 'Koni', (SELECT id FROM departement WHERE code = '080'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('376', 'Korhogo', (SELECT id FROM departement WHERE code = '080'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('377', 'Lataha', (SELECT id FROM departement WHERE code = '080'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('378', 'Nafoun', (SELECT id FROM departement WHERE code = '080'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('379', 'Napié', (SELECT id FROM departement WHERE code = '080'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('380', 'NGanon', (SELECT id FROM departement WHERE code = '080'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('381', 'Niofoin', (SELECT id FROM departement WHERE code = '080'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('382', 'Sirasso', (SELECT id FROM departement WHERE code = '080'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('383', 'Sohouo', (SELECT id FROM departement WHERE code = '080'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('384', 'Tioroniaradougou', (SELECT id FROM departement WHERE code = '080'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('385', 'Bougou', (SELECT id FROM departement WHERE code = '081'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('386', 'Katiali', (SELECT id FROM departement WHERE code = '081'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('387', 'Katogo', (SELECT id FROM departement WHERE code = '081'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('388', 'MBengué', (SELECT id FROM departement WHERE code = '081'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('389', 'Bahouakaha', (SELECT id FROM departement WHERE code = '082'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('390', 'Kagbolodougou', (SELECT id FROM departement WHERE code = '082'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('391', 'Sétiogo', (SELECT id FROM departement WHERE code = '082'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('392', 'Sinématiali', (SELECT id FROM departement WHERE code = '082'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('393', 'Doba', (SELECT id FROM departement WHERE code = '010'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('394', 'Dogbo', (SELECT id FROM departement WHERE code = '010'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('395', 'Gabiadji', (SELECT id FROM departement WHERE code = '010'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('396', 'Grand-Béréby', (SELECT id FROM departement WHERE code = '010'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('397', 'San-Pedro', (SELECT id FROM departement WHERE code = '010'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('398', 'Djouroutou', (SELECT id FROM departement WHERE code = '011'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('399', 'Grabo', (SELECT id FROM departement WHERE code = '011'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('400', 'Olodio', (SELECT id FROM departement WHERE code = '011'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('401', 'Tabou', (SELECT id FROM departement WHERE code = '011'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('402', 'Aboisso', (SELECT id FROM departement WHERE code = '015'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('403', 'Adaou', (SELECT id FROM departement WHERE code = '015'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('404', 'Adjouan', (SELECT id FROM departement WHERE code = '015'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('405', 'Ayamé', (SELECT id FROM departement WHERE code = '015'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('406', 'Bianouan', (SELECT id FROM departement WHERE code = '015'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('407', 'Kouakro', (SELECT id FROM departement WHERE code = '015'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('408', 'Maféré', (SELECT id FROM departement WHERE code = '015'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('409', 'Yaou', (SELECT id FROM departement WHERE code = '015'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('410', 'Adiaké', (SELECT id FROM departement WHERE code = '016'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('411', 'Assinie-Mafia', (SELECT id FROM departement WHERE code = '016'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('412', 'Etuéboué', (SELECT id FROM departement WHERE code = '016'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('413', 'Bongo', (SELECT id FROM departement WHERE code = '017'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('414', 'Bonoua', (SELECT id FROM departement WHERE code = '017'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('415', 'Grand-Bassam', (SELECT id FROM departement WHERE code = '017'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('416', 'Noé', (SELECT id FROM departement WHERE code = '019'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('417', 'Nouamou', (SELECT id FROM departement WHERE code = '019'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('418', 'Tiapoum', (SELECT id FROM departement WHERE code = '019'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('419', 'Ferkessédougou', (SELECT id FROM departement WHERE code = '083'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('420', 'Koumbala', (SELECT id FROM departement WHERE code = '083'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('421', 'Togoniéré', (SELECT id FROM departement WHERE code = '083'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('422', 'Bilimono', (SELECT id FROM departement WHERE code = '084'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('423', 'Kong', (SELECT id FROM departement WHERE code = '084'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('424', 'Nafana', (SELECT id FROM departement WHERE code = '084'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('425', 'Sikolo', (SELECT id FROM departement WHERE code = '084'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('426', 'Diawala', (SELECT id FROM departement WHERE code = '085'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('427', 'Kaouara', (SELECT id FROM departement WHERE code = '085'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('428', 'Niellé', (SELECT id FROM departement WHERE code = '085'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('429', 'Ouangolodougou', (SELECT id FROM departement WHERE code = '085'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('430', 'Toumoukoro', (SELECT id FROM departement WHERE code = '085'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('431', 'Biankouma', (SELECT id FROM departement WHERE code = '063'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('432', 'Blapleu', (SELECT id FROM departement WHERE code = '063'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('433', 'Gbangbégouiné', (SELECT id FROM departement WHERE code = '063'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('434', 'Gbonné', (SELECT id FROM departement WHERE code = '063'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('435', 'Gouiné', (SELECT id FROM departement WHERE code = '063'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('436', 'Kpata', (SELECT id FROM departement WHERE code = '063'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('437', 'Santa', (SELECT id FROM departement WHERE code = '063'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('438', 'Daleu', (SELECT id FROM departement WHERE code = '064'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('439', 'Danané', (SELECT id FROM departement WHERE code = '064'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('440', 'Gbon-Houyé', (SELECT id FROM departement WHERE code = '064'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('441', 'Kouan-Houlé', (SELECT id FROM departement WHERE code = '064'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('442', 'Mahapleu', (SELECT id FROM departement WHERE code = '064'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('443', 'Séileu', (SELECT id FROM departement WHERE code = '064'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('444', 'Zonneu', (SELECT id FROM departement WHERE code = '064'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('445', 'Bogouiné', (SELECT id FROM departement WHERE code = '065'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('446', 'Gbangbégouiné-Yati', (SELECT id FROM departement WHERE code = '065'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('447', 'Logoualé', (SELECT id FROM departement WHERE code = '065'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('448', 'Man', (SELECT id FROM departement WHERE code = '065'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('449', 'Podiagouiné', (SELECT id FROM departement WHERE code = '065'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('450', 'Sandougou-Soba', (SELECT id FROM departement WHERE code = '065'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('451', 'Sangouiné', (SELECT id FROM departement WHERE code = '065'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('452', 'Yapleu', (SELECT id FROM departement WHERE code = '065'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('453', 'Zagoué', (SELECT id FROM departement WHERE code = '065'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('454', 'Ziogouiné', (SELECT id FROM departement WHERE code = '065'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('455', 'Sipilou', (SELECT id FROM departement WHERE code = '066'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('456', 'Yorodougou', (SELECT id FROM departement WHERE code = '066'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('457', 'Banneu', (SELECT id FROM departement WHERE code = '067'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('458', 'Bin-Houyé', (SELECT id FROM departement WHERE code = '067'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('459', 'Goulaleu', (SELECT id FROM departement WHERE code = '067'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('460', 'Téapleu', (SELECT id FROM departement WHERE code = '067'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('461', 'Zouan-Hounien', (SELECT id FROM departement WHERE code = '067'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('462', 'Djibrosso', (SELECT id FROM departement WHERE code = '099'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('463', 'Fadiadougou', (SELECT id FROM departement WHERE code = '099'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('464', 'Kani', (SELECT id FROM departement WHERE code = '099'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('465', 'Morondo', (SELECT id FROM departement WHERE code = '099'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('466', 'Bobi', (SELECT id FROM departement WHERE code = '100'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('467', 'Diarrabana', (SELECT id FROM departement WHERE code = '100'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('468', 'Dualla', (SELECT id FROM departement WHERE code = '100'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('469', 'Kamalo', (SELECT id FROM departement WHERE code = '100'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('470', 'Massala', (SELECT id FROM departement WHERE code = '100'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('471', 'Séguéla', (SELECT id FROM departement WHERE code = '100'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('472', 'Sifié', (SELECT id FROM departement WHERE code = '100'));
INSERT INTO sous_prefecture (code, libelle, departement_id) VALUES ('473', 'Worofla', (SELECT id FROM departement WHERE code = '100'));

-- COMMUNES
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('001', 'Abengourou', (SELECT id FROM sous_prefecture WHERE code = '257'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('002', 'Abobo', (SELECT id FROM sous_prefecture WHERE code = '001'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('003', 'Aboisso', (SELECT id FROM sous_prefecture WHERE code = '402'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('004', 'Adiaké', (SELECT id FROM sous_prefecture WHERE code = '410'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('005', 'Adjamé', (SELECT id FROM sous_prefecture WHERE code = '001'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('006', 'Adzopé', (SELECT id FROM sous_prefecture WHERE code = '286'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('007', 'Afféry', (SELECT id FROM sous_prefecture WHERE code = '292'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('008', 'Agboville', (SELECT id FROM sous_prefecture WHERE code = '011'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('009', 'Agnibilékrou', (SELECT id FROM sous_prefecture WHERE code = '264'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('010', 'Agou', (SELECT id FROM sous_prefecture WHERE code = '287'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('011', 'Akoupé', (SELECT id FROM sous_prefecture WHERE code = '293'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('012', 'Alépé', (SELECT id FROM sous_prefecture WHERE code = '296'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('013', 'Anoumaba', (SELECT id FROM sous_prefecture WHERE code = '342'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('014', 'Anyama', (SELECT id FROM sous_prefecture WHERE code = '002'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('015', 'Arrah', (SELECT id FROM sous_prefecture WHERE code = '335'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('016', 'Assuéfry', (SELECT id FROM sous_prefecture WHERE code = '175'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('017', 'Attecoubé', (SELECT id FROM sous_prefecture WHERE code = '001'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('018', 'Ayamé', (SELECT id FROM sous_prefecture WHERE code = '405'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('019', 'Azaguié', (SELECT id FROM sous_prefecture WHERE code = '014'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('020', 'Bako', (SELECT id FROM sous_prefecture WHERE code = '277'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('021', 'Bangolo', (SELECT id FROM sous_prefecture WHERE code = '188'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('022', 'Bassawa', (SELECT id FROM sous_prefecture WHERE code = '210'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('023', 'Bédiala', (SELECT id FROM sous_prefecture WHERE code = '227'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('024', 'Béoumi', (SELECT id FROM sous_prefecture WHERE code = '110'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('025', 'Bettié', (SELECT id FROM sous_prefecture WHERE code = '269'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('026', 'Biankouma', (SELECT id FROM sous_prefecture WHERE code = '431'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('027', 'Bingerville', (SELECT id FROM sous_prefecture WHERE code = '003'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('028', 'Bin-Houyé', (SELECT id FROM sous_prefecture WHERE code = '458'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('029', 'Bloléquin', (SELECT id FROM sous_prefecture WHERE code = '090'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('030', 'Bocanda', (SELECT id FROM sous_prefecture WHERE code = '537'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('031', 'Bodokro', (SELECT id FROM sous_prefecture WHERE code = '111'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('032', 'Bondoukou', (SELECT id FROM sous_prefecture WHERE code = '151'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('033', 'Bongouanou', (SELECT id FROM sous_prefecture WHERE code = '340'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('034', 'Boniérédougou', (SELECT id FROM sous_prefecture WHERE code = '211'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('035', 'Bonon', (SELECT id FROM sous_prefecture WHERE code = '319'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('036', 'Bonoua', (SELECT id FROM sous_prefecture WHERE code = '414'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('037', 'Booko', (SELECT id FROM sous_prefecture WHERE code = '029'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('038', 'Borotou', (SELECT id FROM sous_prefecture WHERE code = '030'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('039', 'Botro', (SELECT id FROM sous_prefecture WHERE code = '116'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('040', 'Bouaflé', (SELECT id FROM sous_prefecture WHERE code = '320'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('041', 'Bouaké', (SELECT id FROM sous_prefecture WHERE code = '119'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('042', 'Bouna', (SELECT id FROM sous_prefecture WHERE code = '079'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('043', 'Boundiali', (SELECT id FROM sous_prefecture WHERE code = '041'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('044', 'Brobo', (SELECT id FROM sous_prefecture WHERE code = '120'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('045', 'Buyo', (SELECT id FROM sous_prefecture WHERE code = '346'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('046', 'Cocody', (SELECT id FROM sous_prefecture WHERE code = '001'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('047', 'Dabakala', (SELECT id FROM sous_prefecture WHERE code = '212'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('048', 'Dabou', (SELECT id FROM sous_prefecture WHERE code = '178'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('049', 'Daloa', (SELECT id FROM sous_prefecture WHERE code = '228'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('050', 'Danané', (SELECT id FROM sous_prefecture WHERE code = '439'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('051', 'Daoukro', (SELECT id FROM sous_prefecture WHERE code = '247'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('052', 'Diabo', (SELECT id FROM sous_prefecture WHERE code = '117'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('053', 'Dianra', (SELECT id FROM sous_prefecture WHERE code = '069'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('054', 'Diawala', (SELECT id FROM sous_prefecture WHERE code = '426'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('055', 'Didiévi', (SELECT id FROM sous_prefecture WHERE code = '055'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('056', 'Diégonéfla', (SELECT id FROM sous_prefecture WHERE code = '145'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('057', 'Dikodougou', (SELECT id FROM sous_prefecture WHERE code = '367'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('058', 'Dimbokro', (SELECT id FROM sous_prefecture WHERE code = '361'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('059', 'Dioulatiédougou', (SELECT id FROM sous_prefecture WHERE code = '279'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('060', 'Divo', (SELECT id FROM sous_prefecture WHERE code = '304'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('061', 'Djébonoua', (SELECT id FROM sous_prefecture WHERE code = '121'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('062', 'Djékanou', (SELECT id FROM sous_prefecture WHERE code = '060'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('063', 'Djibrosso', (SELECT id FROM sous_prefecture WHERE code = '462'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('064', 'Doropo', (SELECT id FROM sous_prefecture WHERE code = '082'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('065', 'Dualla', (SELECT id FROM sous_prefecture WHERE code = '468'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('066', 'Duékoué', (SELECT id FROM sous_prefecture WHERE code = '198'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('067', 'Facobly', (SELECT id FROM sous_prefecture WHERE code = '202'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('068', 'Ferkessédougou', (SELECT id FROM sous_prefecture WHERE code = '419'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('069', 'Foumbolo', (SELECT id FROM sous_prefecture WHERE code = '213'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('070', 'Fresco', (SELECT id FROM sous_prefecture WHERE code = '127'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('071', 'Fronan', (SELECT id FROM sous_prefecture WHERE code = '218'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('072', 'Ettrokro', (SELECT id FROM sous_prefecture WHERE code = '248'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('073', 'Etuéboué', (SELECT id FROM sous_prefecture WHERE code = '412'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('074', 'Gagnoa', (SELECT id FROM sous_prefecture WHERE code = '138'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('075', 'Gboguhé', (SELECT id FROM sous_prefecture WHERE code = '230'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('076', 'Gbon', (SELECT id FROM sous_prefecture WHERE code = '046'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('077', 'Gbonné', (SELECT id FROM sous_prefecture WHERE code = '434'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('078', 'Gohitafla', (SELECT id FROM sous_prefecture WHERE code = '329'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('079', 'Goulia', (SELECT id FROM sous_prefecture WHERE code = '102'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('080', 'Grabo', (SELECT id FROM sous_prefecture WHERE code = '399'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('081', 'Grand-Bassam', (SELECT id FROM sous_prefecture WHERE code = '415'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('082', 'Grand-Béréby', (SELECT id FROM sous_prefecture WHERE code = '396'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('083', 'Grand-Lahou', (SELECT id FROM sous_prefecture WHERE code = '184'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('084', 'Grand-Zattry', (SELECT id FROM sous_prefecture WHERE code = '351'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('085', 'Guéyo', (SELECT id FROM sous_prefecture WHERE code = '348'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('086', 'Guibéroua', (SELECT id FROM sous_prefecture WHERE code = '141'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('087', 'Guiembé', (SELECT id FROM sous_prefecture WHERE code = '368'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('088', 'Guiglo', (SELECT id FROM sous_prefecture WHERE code = '093'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('089', 'Guintéguéla', (SELECT id FROM sous_prefecture WHERE code = '038'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('090', 'Guitry', (SELECT id FROM sous_prefecture WHERE code = '309'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('091', 'Hiré', (SELECT id FROM sous_prefecture WHERE code = '305'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('092', 'Issia', (SELECT id FROM sous_prefecture WHERE code = '235'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('093', 'Jacqueville', (SELECT id FROM sous_prefecture WHERE code = '187'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('094', 'Kanakono', (SELECT id FROM sous_prefecture WHERE code = '051'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('095', 'Kani', (SELECT id FROM sous_prefecture WHERE code = '464'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('096', 'Kaniasso', (SELECT id FROM sous_prefecture WHERE code = '103'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('097', 'Karakoro', (SELECT id FROM sous_prefecture WHERE code = '371'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('098', 'Kasséré', (SELECT id FROM sous_prefecture WHERE code = '043'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('099', 'Katiola', (SELECT id FROM sous_prefecture WHERE code = '219'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('100', 'Kokoumbo', (SELECT id FROM sous_prefecture WHERE code = '066'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('101', 'Kolia', (SELECT id FROM sous_prefecture WHERE code = '047'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('102', 'Komborodougou', (SELECT id FROM sous_prefecture WHERE code = '374'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('103', 'Kong', (SELECT id FROM sous_prefecture WHERE code = '423'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('104', 'Kongasso', (SELECT id FROM sous_prefecture WHERE code = '071'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('105', 'Koonan', (SELECT id FROM sous_prefecture WHERE code = '034'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('106', 'Korhogo', (SELECT id FROM sous_prefecture WHERE code = '376'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('107', 'Koro', (SELECT id FROM sous_prefecture WHERE code = '031'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('108', 'Kouassi-Datékro', (SELECT id FROM sous_prefecture WHERE code = '163'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('109', 'Kouassi-Kouassikro', (SELECT id FROM sous_prefecture WHERE code = '364'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('110', 'Kouibly', (SELECT id FROM sous_prefecture WHERE code = '206'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('111', 'Koumassi', (SELECT id FROM sous_prefecture WHERE code = '001'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('112', 'Koumbala', (SELECT id FROM sous_prefecture WHERE code = '420'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('113', 'Kounahiri', (SELECT id FROM sous_prefecture WHERE code = '072'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('114', 'Koun-Fao', (SELECT id FROM sous_prefecture WHERE code = '164'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('115', 'Kouto', (SELECT id FROM sous_prefecture WHERE code = '048'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('116', 'Lakota', (SELECT id FROM sous_prefecture WHERE code = '315'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('117', 'Logoualé', (SELECT id FROM sous_prefecture WHERE code = '447'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('118', 'Madinani', (SELECT id FROM sous_prefecture WHERE code = '275'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('119', 'Maféré', (SELECT id FROM sous_prefecture WHERE code = '408'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('120', 'Man', (SELECT id FROM sous_prefecture WHERE code = '448'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('121', 'Mankono', (SELECT id FROM sous_prefecture WHERE code = '074'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('122', 'Marcory', (SELECT id FROM sous_prefecture WHERE code = '001'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('123', 'Massala', (SELECT id FROM sous_prefecture WHERE code = '470'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('124', 'Mayo', (SELECT id FROM sous_prefecture WHERE code = '353'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('125', 'M''Bahiakro', (SELECT id FROM sous_prefecture WHERE code = '251'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('126', 'M''Batto', (SELECT id FROM sous_prefecture WHERE code = '344'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('127', 'M''Bengué', (SELECT id FROM sous_prefecture WHERE code = '388'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('128', 'Méagui', (SELECT id FROM sous_prefecture WHERE code = '349'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('129', 'Minignan', (SELECT id FROM sous_prefecture WHERE code = '106'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('130', 'Morondo', (SELECT id FROM sous_prefecture WHERE code = '465'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('131', 'Napié', (SELECT id FROM sous_prefecture WHERE code = '379'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('132', 'Nassian', (SELECT id FROM sous_prefecture WHERE code = '085'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('133', 'Niablé', (SELECT id FROM sous_prefecture WHERE code = '261'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('134', 'Niakaramadougou', (SELECT id FROM sous_prefecture WHERE code = '223'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('135', 'Niellé', (SELECT id FROM sous_prefecture WHERE code = '428'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('136', 'Niofoin', (SELECT id FROM sous_prefecture WHERE code = '381'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('137', 'Odienné', (SELECT id FROM sous_prefecture WHERE code = '280'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('138', 'Ouangolodougou', (SELECT id FROM sous_prefecture WHERE code = '429'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('139', 'Ouaninou', (SELECT id FROM sous_prefecture WHERE code = '035'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('140', 'Ouellé', (SELECT id FROM sous_prefecture WHERE code = '249'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('141', 'Oumé', (SELECT id FROM sous_prefecture WHERE code = '147'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('142', 'Ouragahio', (SELECT id FROM sous_prefecture WHERE code = '142'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('143', 'Plateau', (SELECT id FROM sous_prefecture WHERE code = '001'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('144', 'Port-Bouët', (SELECT id FROM sous_prefecture WHERE code = '001'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('145', 'Prikro', (SELECT id FROM sous_prefecture WHERE code = '256'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('146', 'Rubino', (SELECT id FROM sous_prefecture WHERE code = '020'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('147', 'Saioua', (SELECT id FROM sous_prefecture WHERE code = '238'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('148', 'Sakassou', (SELECT id FROM sous_prefecture WHERE code = '124'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('149', 'Samatiguila', (SELECT id FROM sous_prefecture WHERE code = '283'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('150', 'Sandégué', (SELECT id FROM sous_prefecture WHERE code = '169'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('151', 'Sangouiné', (SELECT id FROM sous_prefecture WHERE code = '451'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('152', 'San-Pedro', (SELECT id FROM sous_prefecture WHERE code = '397'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('153', 'Sarhala', (SELECT id FROM sous_prefecture WHERE code = '076'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('154', 'Sassandra', (SELECT id FROM sous_prefecture WHERE code = '134'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('155', 'Satama-Sokoro', (SELECT id FROM sous_prefecture WHERE code = '215'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('156', 'Satama-Sokoura', (SELECT id FROM sous_prefecture WHERE code = '216'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('157', 'Séguéla', (SELECT id FROM sous_prefecture WHERE code = '471'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('158', 'Séguélon', (SELECT id FROM sous_prefecture WHERE code = '284'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('159', 'Seydougou', (SELECT id FROM sous_prefecture WHERE code = '273'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('160', 'Sifié', (SELECT id FROM sous_prefecture WHERE code = '472'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('161', 'Sikensi', (SELECT id FROM sous_prefecture WHERE code = '022'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('162', 'Sinématiali', (SELECT id FROM sous_prefecture WHERE code = '392'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('163', 'Sinfra', (SELECT id FROM sous_prefecture WHERE code = '328'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('164', 'Sipilou', (SELECT id FROM sous_prefecture WHERE code = '455'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('165', 'Sirasso', (SELECT id FROM sous_prefecture WHERE code = '382'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('166', 'Songon', (SELECT id FROM sous_prefecture WHERE code = '005'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('167', 'Soubré', (SELECT id FROM sous_prefecture WHERE code = '355'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('168', 'Taabo', (SELECT id FROM sous_prefecture WHERE code = '024'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('169', 'Tabou', (SELECT id FROM sous_prefecture WHERE code = '401'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('170', 'Tafiré', (SELECT id FROM sous_prefecture WHERE code = '225'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('171', 'Taï', (SELECT id FROM sous_prefecture WHERE code = '095'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('172', 'Tanda', (SELECT id FROM sous_prefecture WHERE code = '173'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('173', 'Téhini', (SELECT id FROM sous_prefecture WHERE code = '088'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('174', 'Tengrela', (SELECT id FROM sous_prefecture WHERE code = '053'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('175', 'Tiapoum', (SELECT id FROM sous_prefecture WHERE code = '418'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('176', 'Tiassalé', (SELECT id FROM sous_prefecture WHERE code = '028'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('177', 'Tiebissou', (SELECT id FROM sous_prefecture WHERE code = '063'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('178', 'Tiémé', (SELECT id FROM sous_prefecture WHERE code = '281'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('179', 'Tiémélékro', (SELECT id FROM sous_prefecture WHERE code = '345'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('180', 'Tié-N’Diékro', (SELECT id FROM sous_prefecture WHERE code = '058'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('181', 'Tiénigboué', (SELECT id FROM sous_prefecture WHERE code = '077'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('182', 'Tienko', (SELECT id FROM sous_prefecture WHERE code = '108'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('183', 'Tioroniaradougou', (SELECT id FROM sous_prefecture WHERE code = '384'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('184', 'Tortiya', (SELECT id FROM sous_prefecture WHERE code = '226'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('185', 'Touba', (SELECT id FROM sous_prefecture WHERE code = '039'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('186', 'Toulepleu', (SELECT id FROM sous_prefecture WHERE code = '101'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('187', 'Toumodi', (SELECT id FROM sous_prefecture WHERE code = '068'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('188', 'Transua', (SELECT id FROM sous_prefecture WHERE code = '177'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('189', 'Treichville', (SELECT id FROM sous_prefecture WHERE code = '001'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('190', 'Vavoua', (SELECT id FROM sous_prefecture WHERE code = '242'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('191', 'Worofla', (SELECT id FROM sous_prefecture WHERE code = '473'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('192', 'Yakassé-Attobrou', (SELECT id FROM sous_prefecture WHERE code = '302'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('193', 'Yamoussoukro', (SELECT id FROM sous_prefecture WHERE code = '009'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('194', 'Yopougon', (SELECT id FROM sous_prefecture WHERE code = '001'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('195', 'Zikisso', (SELECT id FROM sous_prefecture WHERE code = '317'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('196', 'Zouan-Hounien', (SELECT id FROM sous_prefecture WHERE code = '461'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('197', 'Zoukougbeu', (SELECT id FROM sous_prefecture WHERE code = '245'), 0);
INSERT INTO commune (code, libelle, sous_prefecture_id, population) VALUES ('198', 'Zuénoula', (SELECT id FROM sous_prefecture WHERE code = '334'), 0);

-- TYPES DE COLLECTIVITÉ
INSERT INTO type_collectivite (code, libelle) VALUES ('001', 'DISTRICT AUTONOME');
INSERT INTO type_collectivite (code, libelle) VALUES ('002', 'DISTRICT');
INSERT INTO type_collectivite (code, libelle) VALUES ('003', 'REGION');
INSERT INTO type_collectivite (code, libelle) VALUES ('004', 'COMMUNE');

-- RÉGIMES D'IMPOSITION
INSERT INTO regime_imposition (code, libelle_court, libelle, ca_borne_inf, ca_borne_sup) VALUES ('001', 'REN', 'Régime de l''Entreprenant', 0, 50000000);

-- DOMAINES DE TAXE
INSERT INTO domaine_taxe (code, libelle_court, libelle) VALUES ('001', 'TAXD', 'Taxes de District');
INSERT INTO domaine_taxe (code, libelle_court, libelle) VALUES ('002', 'TAXR', 'Taxes régionales');
INSERT INTO domaine_taxe (code, libelle_court, libelle) VALUES ('003', 'TAXC', 'Taxes communales');

-- CATÉGORIES D'IMPÔT/TAXE
INSERT INTO categorie_impot_taxe (code, libelle) VALUES ('001', 'Impôts d''Etat dont le produit est attribué aux collectivités territoriales');
INSERT INTO categorie_impot_taxe (code, libelle) VALUES ('002', 'Taxes locales perçues par voies de rôles');
INSERT INTO categorie_impot_taxe (code, libelle) VALUES ('003', 'Taxes locales perçues sur titres de recettes');

-- QUALITÉS DE DIRIGEANT
INSERT INTO qualite_dirigeant (code, libelle_court, libelle) VALUES (0, 'MAIRE', 'MAIRE');

-- GRADES D'AGENT
INSERT INTO grade_agent (code, libelle) VALUES ('01', 'CADRE SUPERIEUR');

-- FORMES JURIDIQUES
INSERT INTO forme_juridique (code, nom_court, libelle) VALUES ('001', 'SA', 'Société anonyme');

-- =====================================================================
--  RÉFÉRENTIELS FISCAUX (absents de l'ancienne base - valeurs proposées)
-- =====================================================================

-- PÉRIODICITÉS
INSERT INTO periodicite (code, libelle_court, libelle, nb_mois) VALUES
('ANN','Annuel','Annuel',12),
('SEM','Semestriel','Semestriel',6),
('TRI','Trimestriel','Trimestriel',3),
('MEN','Mensuel','Mensuel',1),
('UNI','Unique','Paiement unique',NULL);

-- MODES DE RÈGLEMENT
INSERT INTO mode_reglement (code, libelle) VALUES
('ESP','Espèces'),
('CHQ','Chèque'),
('VIR','Virement bancaire'),
('MOB','Mobile Money'),
('TPE','Carte / TPE');

-- TYPES DE RÈGLEMENT
INSERT INTO type_reglement (code, libelle) VALUES
('TOT','Règlement total'),
('PAR','Règlement partiel'),
('ACO','Acompte'),
('REL','Relance / pénalité');

-- BANQUES (principales banques de Côte d'Ivoire - UEMOA)
INSERT INTO banque (code, libelle_court, libelle) VALUES
('SGC','SGCI','Société Générale Côte d''Ivoire'),
('BIC','BICICI','Banque Internationale pour le Commerce et l''Industrie de la CI'),
('ECO','ECOBANK','Ecobank Côte d''Ivoire'),
('SIB','SIB','Société Ivoirienne de Banque'),
('NSI','NSIA','NSIA Banque Côte d''Ivoire'),
('BNI','BNI','Banque Nationale d''Investissement'),
('BOA','BOA','Bank of Africa Côte d''Ivoire'),
('CBI','CORIS','Coris Bank International'),
('UBA','UBA','United Bank for Africa'),
('BDU','BDU','Banque de l''Union');

-- FONCTIONS D'AGENT
INSERT INTO fonction_agent (code, libelle) VALUES
('01','Directeur'),
('02','Chef de service'),
('03','Agent de recensement'),
('04','Agent de liquidation'),
('05','Agent de recouvrement'),
('06','Contrôleur'),
('07','Agent de saisie');

-- SECTEURS D'ACTIVITÉ
INSERT INTO secteur_activite (code, libelle) VALUES
('PRI','Secteur primaire (agriculture, élevage, pêche)'),
('SEC','Secteur secondaire (industrie, BTP)'),
('TER','Secteur tertiaire (commerce, services)'),
('INF','Secteur informel');

-- CATÉGORIES D'ACTIVITÉ
INSERT INTO categorie_activite (code, libelle) VALUES
('COM','Commerce'),
('SER','Prestations de services'),
('IND','Industrie / production'),
('ART','Artisanat'),
('TRA','Transport'),
('RES','Restauration / hôtellerie'),
('PRO','Professions libérales');

-- NATURES DE TAXE (taxes locales courantes - FK domaine + catégorie par code)
INSERT INTO nature_taxe (code, libelle_court, libelle, domaine_taxe_id, categorie_impot_taxe_id)
VALUES
('TPV','Patente','Patente / taxe sur les activités',
  (SELECT id FROM domaine_taxe WHERE code='001'),
  (SELECT id FROM categorie_impot_taxe WHERE code='002')),
('TFP','Tax. foncière','Taxe foncière sur le patrimoine',
  (SELECT id FROM domaine_taxe WHERE code='001'),
  (SELECT id FROM categorie_impot_taxe WHERE code='002')),
('TST','Tax. station.','Taxe de stationnement',
  (SELECT id FROM domaine_taxe WHERE code='002'),
  (SELECT id FROM categorie_impot_taxe WHERE code='002')),
('TPU','Tax. publicité','Taxe sur la publicité',
  (SELECT id FROM domaine_taxe WHERE code='002'),
  (SELECT id FROM categorie_impot_taxe WHERE code='002')),
('TEN','Tax. enlèv.','Taxe d''enlèvement des ordures',
  (SELECT id FROM domaine_taxe WHERE code='001'),
  (SELECT id FROM categorie_impot_taxe WHERE code='002'));

-- =====================================================================
--  FIN DU JEU DE DONNÉES DE RÉFÉRENCE
-- =====================================================================
