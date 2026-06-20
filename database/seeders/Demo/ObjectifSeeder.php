<?php

namespace Database\Seeders\Demo;

use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Données de DÉMONSTRATION : objectifs annuels de recouvrement pour la
 * collectivité ABJ (année courante et précédente), afin d'alimenter la jauge
 * « Objectif de recouvrement » du tableau de bord.
 *
 * Valeurs indicatives — en exploitation, l'objectif est saisi via l'écran de
 * pilotage. Chaque objectif de démo est rattaché à l'exercice de l'année et
 * couvre sa période complète. Idempotent via updateOrInsert.
 */
class ObjectifSeeder extends Seeder
{
    public function run(): void
    {
        $collectiviteId = DB::table('collectivite')->where('code', 'ABJ')->value('id');

        if ($collectiviteId === null) {
            $this->command?->warn('Collectivité ABJ absente — objectifs de démo ignorés.');

            return;
        }

        $anneeCourante = (int) CarbonImmutable::now()->format('Y');

        $montants = [
            $anneeCourante - 1 => 2_500_000,
            $anneeCourante     => 3_000_000,
        ];

        $exercices = DB::table('exercice_fiscal')
            ->where('collectivite_id', $collectiviteId)
            ->whereIn('annee', array_keys($montants))
            ->get();

        foreach ($exercices as $ex) {
            DB::table('objectif')->updateOrInsert(
                [
                    'collectivite_id'    => $collectiviteId,
                    'exercice_fiscal_id' => $ex->id,
                ],
                [
                    'annee'         => $ex->annee,
                    'periode_debut' => $ex->date_debut,
                    'periode_fin'   => $ex->date_fin,
                    'montant'       => $montants[$ex->annee],
                ],
            );
        }
    }
}
