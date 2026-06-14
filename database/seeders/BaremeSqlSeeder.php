<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Charge les barèmes de taxe depuis le script SQL de Phase 1 :
 * bareme_taxe (patente, TEN…), categorie_cotisation_fonciere et
 * bareme_cotisation_fonciere (taxe foncière par zone).
 *
 * Ces tables ne sont peuplées par aucune migration ni autre seeder. Le script
 * résout ses FK par code (nature_taxe, periodicite) : il doit donc tourner APRÈS
 * ReferentielSqlSeeder. Garde d'idempotence (le SQL n'a pas de ON CONFLICT, et
 * bareme_taxe n'a pas de clé unique) : on ne rejoue pas si la table est remplie.
 */
class BaremeSqlSeeder extends Seeder
{
    private const CHEMIN_SQL = 'docs/phase1/fiscct_seed_baremes.sql';

    public function run(): void
    {
        if (DB::table('bareme_taxe')->exists()) {
            $this->command?->info('Barèmes déjà présents — chargement ignoré.');

            return;
        }

        $chemin = base_path(self::CHEMIN_SQL);

        if (! is_file($chemin)) {
            throw new RuntimeException("Script des barèmes introuvable : {$chemin}");
        }

        $sql = file_get_contents($chemin);

        if ($sql === false || trim($sql) === '') {
            throw new RuntimeException("Script des barèmes illisible ou vide : {$chemin}");
        }

        DB::unprepared($sql);

        $this->command?->info('Barèmes chargés (taxe proportionnelle + cotisation foncière).');
    }
}
