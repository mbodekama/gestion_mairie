<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Charge le référentiel de base (territoire CI + référentiels fiscaux) depuis le
 * script SQL de Phase 1 : pays, region, district, departement, sous_prefecture,
 * commune, type_collectivite, forme_juridique, regime_imposition, domaine_taxe,
 * nature_taxe, periodicite, banque, activite, etc.
 *
 * Ces tables ne sont peuplées par aucune migration ni autre seeder : leur contenu
 * provient exclusivement de ce fichier SQL. Doit donc tourner AVANT les seeders qui
 * en dépendent (ContribuableSeeder pour forme_juridique / regime_imposition / pays).
 *
 * Le script SQL ne contient pas de clause ON CONFLICT : on garde l'exécution
 * derrière un test d'idempotence pour pouvoir relancer `migrate --seed` sans erreur
 * de clé dupliquée.
 */
class ReferentielSqlSeeder extends Seeder
{
    private const CHEMIN_SQL = 'docs/phase1/fiscct_seed_referentiel.sql';

    public function run(): void
    {
        // Déjà chargé : on ne rejoue pas le SQL (pas de ON CONFLICT côté script).
        if (DB::table('pays')->exists()) {
            $this->command?->info('Référentiel SQL déjà présent — chargement ignoré.');

            return;
        }

        $chemin = base_path(self::CHEMIN_SQL);

        if (! is_file($chemin)) {
            throw new RuntimeException("Script de référentiel introuvable : {$chemin}");
        }

        $sql = file_get_contents($chemin);

        if ($sql === false || trim($sql) === '') {
            throw new RuntimeException("Script de référentiel illisible ou vide : {$chemin}");
        }

        DB::unprepared($sql);

        $this->command?->info('Référentiel SQL chargé (territoire CI + référentiels fiscaux).');
    }
}
