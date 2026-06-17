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
 * pilotage. Idempotent : contrainte unique (collectivite_id, annee) → insertOrIgnore.
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

        $lignes = [
            ['annee' => $anneeCourante - 1, 'montant' => 2_500_000],
            ['annee' => $anneeCourante,     'montant' => 3_000_000],
        ];

        DB::table('objectif')->insertOrIgnore(array_map(fn ($l) => [
            'collectivite_id' => $collectiviteId,
            'annee'           => $l['annee'],
            'montant'         => $l['montant'],
        ], $lignes));
    }
}
