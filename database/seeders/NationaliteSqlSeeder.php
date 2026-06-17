<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Charge les nationalités (une par pays du référentiel) depuis le script SQL
 * docs/phase1/fiscct_seed_nationalite.sql.
 *
 * Le script résout `pays_id` par sous-requête sur `pays.code` : il doit donc
 * tourner APRÈS ReferentielSqlSeeder (table pays peuplée). Le SQL porte une
 * clause ON CONFLICT (code) DO NOTHING : il est rejouable sans erreur, mais on
 * garde une garde d'idempotence pour éviter de relire le fichier inutilement.
 */
class NationaliteSqlSeeder extends Seeder
{
    private const CHEMIN_SQL = 'docs/phase1/fiscct_seed_nationalite.sql';

    public function run(): void
    {
        // Déjà chargé : on ne rejoue pas le SQL.
        if (DB::table('nationalite')->exists()) {
            $this->command?->info('Nationalités déjà présentes — chargement ignoré.');

            return;
        }

        $chemin = base_path(self::CHEMIN_SQL);

        if (! is_file($chemin)) {
            throw new RuntimeException("Script des nationalités introuvable : {$chemin}");
        }

        $sql = file_get_contents($chemin);

        if ($sql === false || trim($sql) === '') {
            throw new RuntimeException("Script des nationalités illisible ou vide : {$chemin}");
        }

        DB::unprepared($sql);

        $this->command?->info('Nationalités chargées (une par pays du référentiel).');
    }
}
