<?php

namespace Database\Seeders\Demo;

use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Données de DÉMONSTRATION : exercices fiscaux couvrant les 12 derniers mois
 * pour la collectivité ABJ — soit l'année courante ET l'année précédente (la
 * fenêtre glissante de 12 mois chevauche deux exercices).
 *
 * Support des émissions/recouvrements de démo. Idempotent : contrainte unique
 * (annee, collectivite_id) → insertOrIgnore.
 */
class ExerciceFiscalSeeder extends Seeder
{
    public function run(): void
    {
        $collectiviteId = DB::table('collectivite')->where('code', 'ABJ')->value('id');

        if ($collectiviteId === null) {
            $this->command?->warn('Collectivité ABJ absente — exercices de démo ignorés.');

            return;
        }

        $anneeCourante = (int) CarbonImmutable::now()->format('Y');

        $lignes = [];
        foreach ([$anneeCourante - 1, $anneeCourante] as $annee) {
            $lignes[] = [
                'annee'           => $annee,
                'collectivite_id' => $collectiviteId,
                'date_debut'      => "{$annee}-01-01",
                'date_fin'        => "{$annee}-12-31",
                'cloture'         => false,
            ];
        }

        DB::table('exercice_fiscal')->insertOrIgnore($lignes);
    }
}
