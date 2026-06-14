<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReferentielContribuableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('type_personne')->insertOrIgnore([
            ['code' => 'PP', 'libelle' => 'Personne physique'],
            ['code' => 'PM', 'libelle' => 'Personne morale'],
        ]);

        DB::table('statut_contribuable')->insertOrIgnore([
            ['code' => 'ACTIF',    'libelle' => 'Actif'],
            ['code' => 'SUSPENDU', 'libelle' => 'Suspendu'],
            ['code' => 'RADIE',    'libelle' => 'Radié'],
        ]);
    }
}
