<?php

namespace Database\Seeders\Demo;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Données de DÉMONSTRATION : établissements rattachés aux contribuables de démo.
 * Ne pas charger en exploitation 0.
 *
 * Prérequis : contribuables de démo (Demo\ContribuableSeeder), communes et zones
 * fiscales (ReferentielSqlSeeder + ZoneFiscaleSeeder), activités (ReferentielSqlSeeder),
 * collectivité ABJ (CollectiviteSeeder).
 *
 * Numéros préfixés DEMOETAB pour repérage / suppression. Idempotent (numero unique).
 */
class EtablissementSeeder extends Seeder
{
    /**
     * Établissements de démo. La zone fiscale est résolue automatiquement
     * (première zone de la commune). Une ligne = un établissement.
     */
    private const ETABLISSEMENTS = [
        ['numero' => 'DEMOETAB01', 'contribuable' => 'CI2024000001', 'activite' => 'COM01', 'commune' => 'Cocody',      'denomination' => 'Épicerie KONAN',          'type' => 'PRINCIPAL', 'surface' => 45.00,  'debut' => '2021-02-01'],
        ['numero' => 'DEMOETAB02', 'contribuable' => 'CI2024000003', 'activite' => 'RES01', 'commune' => 'Yopougon',    'denomination' => 'Maquis Le Korhogo',       'type' => 'PRINCIPAL', 'surface' => 120.00, 'debut' => '2019-06-15'],
        ['numero' => 'DEMOETAB03', 'contribuable' => 'CI2024000007', 'activite' => 'COM03', 'commune' => 'Plateau',     'denomination' => 'Abidjan Commerce',         'type' => 'PRINCIPAL', 'surface' => 80.00,  'debut' => '2018-06-15'],
        ['numero' => 'DEMOETAB04', 'contribuable' => 'CI2024000009', 'activite' => 'TRA01', 'commune' => 'Marcory',     'denomination' => 'TRCI Dépôt central',       'type' => 'PRINCIPAL', 'surface' => 300.00, 'debut' => '2015-09-10'],
        ['numero' => 'DEMOETAB05', 'contribuable' => 'CI2024000012', 'activite' => 'ART09', 'commune' => 'Treichville', 'denomination' => 'EGBCI Base chantier',      'type' => 'PRINCIPAL', 'surface' => 500.00, 'debut' => '2010-01-20'],
        ['numero' => 'DEMOETAB06', 'contribuable' => 'CI2024000008', 'activite' => 'COM01', 'commune' => 'Cocody',      'denomination' => 'GIL Immobilier — Agence',  'type' => 'PRINCIPAL', 'surface' => 95.00,  'debut' => '2012-03-01'],
    ];

    public function run(): void
    {
        $collectiviteId = DB::table('collectivite')->where('code', 'ABJ')->value('id');

        if ($collectiviteId === null) {
            $this->command?->warn('Collectivité ABJ absente — établissements de démo ignorés.');

            return;
        }

        foreach (self::ETABLISSEMENTS as $e) {
            $contribuableId = DB::table('contribuable')->where('numero_identifiant', $e['contribuable'])->value('id');
            $activiteId     = DB::table('activite')->where('code', $e['activite'])->value('id');
            $communeId      = DB::table('commune')->where('libelle', $e['commune'])->value('id');
            $zoneId         = DB::table('zone_fiscale')->where('commune_id', $communeId)->orderBy('code')->value('id');

            // Sécurité : on n'insère que si toutes les dépendances sont présentes.
            if (! $contribuableId || ! $activiteId || ! $communeId || ! $zoneId) {
                $this->command?->warn("Établissement {$e['numero']} ignoré (dépendance manquante).");

                continue;
            }

            DB::table('etablissement')->insertOrIgnore([
                'numero'             => $e['numero'],
                'contribuable_id'    => $contribuableId,
                'collectivite_id'    => $collectiviteId,
                'activite_id'        => $activiteId,
                'denomination'       => $e['denomination'],
                'type_etablissement' => $e['type'],
                'commune_id'         => $communeId,
                'zone_fiscale_id'    => $zoneId,
                'surface'            => $e['surface'],
                'date_debut_activite' => $e['debut'],
                'statut'             => 'ACTIF',
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }
    }
}
