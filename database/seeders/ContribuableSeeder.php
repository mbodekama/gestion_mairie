<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContribuableSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Prérequis : nationalités et collectivité ───────────────────────
        // pays / type_collectivite / forme_juridique / regime_imposition
        // sont gérés par fiscct_seed_referentiel.sql — ne pas dupliquer ici.

        DB::table('nationalite')->insertOrIgnore([
            ['code' => 'CIV', 'libelle' => 'Ivoirienne', 'pays_id' => DB::table('pays')->where('code', 'CIV')->value('id')],
            ['code' => 'FRA', 'libelle' => 'Française',  'pays_id' => DB::table('pays')->where('code', 'FRA')->value('id')],
            ['code' => 'SEN', 'libelle' => 'Sénégalaise','pays_id' => DB::table('pays')->where('code', 'SEN')->value('id')],
        ]);

        DB::table('collectivite')->insertOrIgnore([
            [
                'code'                 => 'ABJ',
                'libelle'              => 'Mairie d\'Abidjan',
                'type_collectivite_id' => DB::table('type_collectivite')->where('code', 'MRE')->value('id'),
                'active'               => true,
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
        ]);

        // Poste de collecte (recette) — requis par les règlements (reglement_taxe.recette_id NOT NULL)
        DB::table('recette')->insertOrIgnore([
            ['code' => 'R01', 'libelle' => 'Recette principale'],
        ]);

        // ── 2. Raccourcis d'IDs ───────────────────────────────────────────────

        $collectiviteId   = DB::table('collectivite')->where('code', 'ABJ')->value('id');
        $nationaliteCiv   = DB::table('nationalite')->where('code', 'CIV')->value('id');
        $nationaliteFra   = DB::table('nationalite')->where('code', 'FRA')->value('id');
        $nationaliteSen   = DB::table('nationalite')->where('code', 'SEN')->value('id');
        $fjSarl           = DB::table('forme_juridique')->where('code', 'SAR')->value('id');
        $fjSa             = DB::table('forme_juridique')->where('code', 'SAN')->value('id');
        $fjEi             = DB::table('forme_juridique')->where('code', 'EIN')->value('id');
        $fjAsso           = DB::table('forme_juridique')->where('code', 'ASS')->value('id');
        $regimeRsi        = DB::table('regime_imposition')->where('code', 'RSI')->value('id');
        $regimeRni        = DB::table('regime_imposition')->where('code', 'RNI')->value('id');
        $regimeMicro      = DB::table('regime_imposition')->where('code', 'RME')->value('id');

        // ── 3. Contribuables ─────────────────────────────────────────────────

        $contribuables = [
            // ── Personnes physiques ──
            [
                'numero_identifiant'   => 'CI2024000001',
                'numero_compte'        => 'CP2024000001',
                'collectivite_id'      => $collectiviteId,
                'type_personne'        => 'PP',
                'nom'                  => 'KONAN',
                'prenoms'              => 'Kouassi Jean-Baptiste',
                'sexe'                 => 'M',
                'date_naissance'       => '1978-03-15',
                'lieu_naissance'       => 'Bouaké',
                'nationalite_id'       => $nationaliteCiv,
                'telephone'            => '+225 27 22 48 00 10',
                'cellulaire'           => '+225 07 07 12 34 56',
                'email'                => 'jb.konan@email.ci',
                'regime_imposition_id' => $regimeRsi,
                'statut'               => 'ACTIF',
            ],
            [
                'numero_identifiant'   => 'CI2024000002',
                'numero_compte'        => 'CP2024000002',
                'collectivite_id'      => $collectiviteId,
                'type_personne'        => 'PP',
                'nom'                  => 'BAMBA',
                'prenoms'              => 'Fatoumata',
                'sexe'                 => 'F',
                'date_naissance'       => '1985-07-22',
                'lieu_naissance'       => 'Abidjan',
                'nationalite_id'       => $nationaliteCiv,
                'cellulaire'           => '+225 05 55 98 76 54',
                'email'                => 'f.bamba@gmail.com',
                'regime_imposition_id' => $regimeMicro,
                'statut'               => 'ACTIF',
            ],
            [
                'numero_identifiant'   => 'CI2024000003',
                'numero_compte'        => 'CP2024000003',
                'collectivite_id'      => $collectiviteId,
                'type_personne'        => 'PP',
                'nom'                  => 'OUATTARA',
                'prenoms'              => 'Ibrahim Seydou',
                'sexe'                 => 'M',
                'date_naissance'       => '1970-11-05',
                'lieu_naissance'       => 'Korhogo',
                'nationalite_id'       => $nationaliteCiv,
                'telephone'            => '+225 27 36 00 12 34',
                'cellulaire'           => '+225 01 01 23 45 67',
                'regime_imposition_id' => $regimeRsi,
                'statut'               => 'ACTIF',
            ],
            [
                'numero_identifiant'   => 'CI2024000004',
                'numero_compte'        => 'CP2024000004',
                'collectivite_id'      => $collectiviteId,
                'type_personne'        => 'PP',
                'nom'                  => 'DIALLO',
                'prenoms'              => 'Mariame',
                'sexe'                 => 'F',
                'date_naissance'       => '1992-04-18',
                'lieu_naissance'       => 'Daloa',
                'nationalite_id'       => $nationaliteSen,
                'cellulaire'           => '+225 07 08 88 77 66',
                'email'                => 'mariame.diallo@yahoo.fr',
                'regime_imposition_id' => $regimeMicro,
                'statut'               => 'SUSPENDU',
            ],
            [
                'numero_identifiant'   => 'CI2024000005',
                'numero_compte'        => 'CP2024000005',
                'collectivite_id'      => $collectiviteId,
                'type_personne'        => 'PP',
                'nom'                  => 'DUPONT',
                'prenoms'              => 'Pierre',
                'sexe'                 => 'M',
                'date_naissance'       => '1965-09-30',
                'lieu_naissance'       => 'Paris',
                'nationalite_id'       => $nationaliteFra,
                'telephone'            => '+225 27 22 40 50 60',
                'cellulaire'           => '+225 05 50 11 22 33',
                'email'                => 'p.dupont@enterprise.fr',
                'regime_imposition_id' => $regimeRni,
                'statut'               => 'ACTIF',
            ],
            [
                'numero_identifiant'   => 'CI2024000006',
                'numero_compte'        => 'CP2024000006',
                'collectivite_id'      => $collectiviteId,
                'type_personne'        => 'PP',
                'nom'                  => 'YAO',
                'prenoms'              => 'Akissi Christiane',
                'sexe'                 => 'F',
                'date_naissance'       => '1988-12-01',
                'lieu_naissance'       => 'Yamoussoukro',
                'nationalite_id'       => $nationaliteCiv,
                'cellulaire'           => '+225 07 77 55 44 33',
                'regime_imposition_id' => $regimeRsi,
                'statut'               => 'RADIE',
            ],

            // ── Personnes morales ──
            [
                'numero_identifiant'   => 'CI2024000007',
                'numero_compte'        => 'CP2024000007',
                'collectivite_id'      => $collectiviteId,
                'type_personne'        => 'PM',
                'raison_sociale'       => 'SOCIÉTÉ ABIDJAN COMMERCE',
                'sigle'                => 'SAC',
                'denomination_commerciale' => 'Abidjan Commerce',
                'forme_juridique_id'   => $fjSarl,
                'registre_commerce'    => 'CI-ABJ-2018-B-01234',
                'date_registre_commerce' => '2018-06-15',
                'ville_registre_commerce' => 'Abidjan',
                'nombre_associes'      => 3,
                'capital_social'       => 5_000_000,
                'telephone'            => '+225 27 22 40 00 01',
                'email'                => 'contact@sac.ci',
                'regime_imposition_id' => $regimeRsi,
                'statut'               => 'ACTIF',
            ],
            [
                'numero_identifiant'   => 'CI2024000008',
                'numero_compte'        => 'CP2024000008',
                'collectivite_id'      => $collectiviteId,
                'type_personne'        => 'PM',
                'raison_sociale'       => 'GROUPE IMMOBILIER DE LA LAGUNE',
                'sigle'                => 'GIL',
                'denomination_commerciale' => 'GIL Immobilier',
                'forme_juridique_id'   => $fjSa,
                'registre_commerce'    => 'CI-ABJ-2010-A-05678',
                'date_registre_commerce' => '2010-01-20',
                'ville_registre_commerce' => 'Abidjan',
                'nombre_associes'      => 7,
                'capital_social'       => 50_000_000,
                'telephone'            => '+225 27 22 44 55 66',
                'fax'                  => '+225 27 22 44 55 67',
                'email'                => 'direction@gil-immo.ci',
                'regime_imposition_id' => $regimeRni,
                'statut'               => 'ACTIF',
            ],
            [
                'numero_identifiant'   => 'CI2024000009',
                'numero_compte'        => 'CP2024000009',
                'collectivite_id'      => $collectiviteId,
                'type_personne'        => 'PM',
                'raison_sociale'       => 'TRANSPORTS RAPID CÔTE D\'IVOIRE',
                'sigle'                => 'TRCI',
                'forme_juridique_id'   => $fjSarl,
                'registre_commerce'    => 'CI-ABJ-2015-B-09012',
                'date_registre_commerce' => '2015-09-10',
                'ville_registre_commerce' => 'Abidjan',
                'nombre_associes'      => 2,
                'capital_social'       => 10_000_000,
                'cellulaire'           => '+225 07 08 77 66 55',
                'email'                => 'trci@transport.ci',
                'regime_imposition_id' => $regimeRsi,
                'statut'               => 'ACTIF',
            ],
            [
                'numero_identifiant'   => 'CI2024000010',
                'numero_compte'        => 'CP2024000010',
                'collectivite_id'      => $collectiviteId,
                'type_personne'        => 'PM',
                'raison_sociale'       => 'ASSOCIATION DES COMMERÇANTS DU MARCHÉ COCODY',
                'sigle'                => 'ACMC',
                'forme_juridique_id'   => $fjAsso,
                'telephone'            => '+225 27 22 48 99 00',
                'email'                => 'acmc@gmail.com',
                'regime_imposition_id' => $regimeMicro,
                'statut'               => 'ACTIF',
            ],
            [
                'numero_identifiant'   => 'CI2024000011',
                'numero_compte'        => 'CP2024000011',
                'collectivite_id'      => $collectiviteId,
                'type_personne'        => 'PM',
                'raison_sociale'       => 'KOFFI & ASSOCIÉS CONSEILS',
                'sigle'                => 'KAC',
                'denomination_commerciale' => 'Koffi Conseils',
                'forme_juridique_id'   => $fjEi,
                'registre_commerce'    => 'CI-ABJ-2020-C-33210',
                'date_registre_commerce' => '2020-03-01',
                'ville_registre_commerce' => 'Abidjan',
                'capital_social'       => 1_000_000,
                'cellulaire'           => '+225 05 50 44 33 22',
                'email'                => 'kac@koffi-conseils.ci',
                'regime_imposition_id' => $regimeMicro,
                'statut'               => 'SUSPENDU',
            ],
            [
                'numero_identifiant'   => 'CI2024000012',
                'numero_compte'        => 'CP2024000012',
                'collectivite_id'      => $collectiviteId,
                'type_personne'        => 'PM',
                'raison_sociale'       => 'ENTREPRISE GÉNÉRALE DU BÂTIMENT CÔTE D\'IVOIRE',
                'sigle'                => 'EGBCI',
                'forme_juridique_id'   => $fjSa,
                'registre_commerce'    => 'CI-ABJ-2005-A-00789',
                'date_registre_commerce' => '2005-11-30',
                'ville_registre_commerce' => 'Abidjan',
                'nombre_associes'      => 12,
                'capital_social'       => 200_000_000,
                'telephone'            => '+225 27 22 50 60 70',
                'fax'                  => '+225 27 22 50 60 71',
                'email'                => 'dg@egbci.ci',
                'regime_imposition_id' => $regimeRni,
                'statut'               => 'ACTIF',
            ],
        ];

        foreach ($contribuables as $data) {
            DB::table('contribuable')->insertOrIgnore(array_merge($data, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
