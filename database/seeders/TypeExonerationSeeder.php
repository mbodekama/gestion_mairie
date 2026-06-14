<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Référentiel des types d'exonération fiscale (requis par le module Exonérations).
 */
class TypeExonerationSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('type_exoneration')->insertOrIgnore([
            ['code' => 'EXT', 'libelle' => 'Exonération totale'],
            ['code' => 'EXP', 'libelle' => 'Exonération partielle'],
            ['code' => 'TMP', 'libelle' => 'Exonération temporaire'],
            ['code' => 'INV', 'libelle' => 'Code des investissements'],
            ['code' => 'ZFR', 'libelle' => 'Zone franche'],
            ['code' => 'CNV', 'libelle' => 'Convention / agrément'],
        ]);
    }
}
