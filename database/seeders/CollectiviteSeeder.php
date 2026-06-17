<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Données d'organisation indispensables à l'exploitation (non démo) :
 *  - la collectivité gérée (mono-mairie : Abidjan) — portée `collectivite_id`,
 *  - le poste de collecte / recette requis par les règlements
 *    (reglement_taxe.recette_id NOT NULL).
 *
 * Dépend de type_collectivite (ReferentielSqlSeeder). Idempotent (insertOrIgnore).
 */
class CollectiviteSeeder extends Seeder
{
    public function run(): void
    {
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
    }
}
