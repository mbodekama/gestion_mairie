<?php

namespace Database\Seeders\Demo;

use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Données de DÉMONSTRATION : recouvrements (patente) répartis sur les 12 derniers
 * mois pour la collectivité ABJ, afin d'alimenter le graphique du tableau de bord.
 * Ne pas charger en exploitation 0.
 *
 * Profil en « dents de scie » : un GRAND PIC par trimestre (au moins 4 pics
 * marqués), dont la hauteur porte la narration (montants pré-définis, aucun
 * calcul flottant) :
 *   • T1 (mois 1-3)   : pic de référence  — CONSTANCE
 *   • T2 (mois 4-6)   : pic plus bas      — RÉGRESSION
 *   • T3 (mois 7-9)   : pic plus haut     — ÉVOLUTION
 *   • T4 (mois 10-12) : pic le plus haut  — SUITE (poursuite de la hausse)
 *
 * Pour chaque mois : une émission de patente + un règlement TOTAL (émission
 * soldée le mois même), rattachés à l'exercice de l'année concernée et à un
 * établissement de démo (rotation). Numéros datés → idempotent (insertOrIgnore).
 *
 * Prérequis : établissements (Demo\EtablissementSeeder), exercices année courante
 * et précédente (Demo\ExerciceFiscalSeeder), recette R01 (CollectiviteSeeder),
 * référentiels nature_taxe / periodicite / mode_reglement / type_reglement.
 */
class EmissionTaxeSeeder extends Seeder
{
    /**
     * Recouvrement mensuel (FCFA), du mois le plus ancien (index 0) au mois
     * courant (index 11). Chaque trimestre porte un GRAND PIC central encadré de
     * mois bas → au moins 4 pics nets ; la hauteur des pics suit la narration.
     */
    private const RECOUVREMENTS_MENSUELS = [
         90000, 300000,  90000,   // T1 — pic de référence (constance)
         70000, 220000,  60000,   // T2 — pic plus bas (régression)
        110000, 360000, 100000,   // T3 — pic plus haut (évolution)
        130000, 540000, 150000,   // T4 — pic le plus haut (suite)
    ];

    /** Établissements de démo utilisés en rotation pour porter les émissions. */
    private const ETABLISSEMENTS = [
        'DEMOETAB01', 'DEMOETAB02', 'DEMOETAB03', 'DEMOETAB04', 'DEMOETAB05', 'DEMOETAB06',
    ];

    /** Modes de règlement utilisés en rotation (réalisme). */
    private const MODES = ['ESP', 'CHQ', 'VIR', 'MOB', 'TPE'];

    public function run(): void
    {
        $collectiviteId = DB::table('collectivite')->where('code', 'ABJ')->value('id');
        $natureTpvId    = DB::table('nature_taxe')->where('code', 'TPV')->value('id');
        $periodiciteId  = DB::table('periodicite')->where('code', 'ANN')->value('id');
        $recetteId      = DB::table('recette')->where('code', 'R01')->value('id');
        $typeTotalId    = DB::table('type_reglement')->where('code', 'TOT')->value('id');

        if (! $collectiviteId || ! $natureTpvId || ! $periodiciteId || ! $recetteId || ! $typeTotalId) {
            $this->command?->warn('Prérequis manquants (collectivité / référentiels) — recouvrements de démo ignorés.');

            return;
        }

        $modes      = DB::table('mode_reglement')->pluck('id', 'code');
        $exercices  = DB::table('exercice_fiscal')
            ->where('collectivite_id', $collectiviteId)
            ->pluck('id', 'annee'); // annee => id

        // Établissements de démo (numero => id).
        $etablissements = DB::table('etablissement')
            ->whereIn('numero', self::ETABLISSEMENTS)
            ->pluck('id', 'numero');

        // Mois le plus ancien de la fenêtre glissante de 12 mois (aligné sur le tableau de bord).
        $premierMois = CarbonImmutable::now()->startOfMonth()->subMonths(11);

        foreach (self::RECOUVREMENTS_MENSUELS as $i => $montant) {
            $mois        = $premierMois->addMonths($i);
            $annee       = (int) $mois->format('Y');
            $exerciceId  = $exercices[$annee] ?? null;
            $etabNumero  = self::ETABLISSEMENTS[$i % count(self::ETABLISSEMENTS)];
            $etabId      = $etablissements[$etabNumero] ?? null;

            if (! $exerciceId || ! $etabId) {
                $this->command?->warn("Recouvrement {$mois->format('Y-m')} ignoré (exercice ou établissement manquant).");

                continue;
            }

            $numeroEmission = 'DEMOEM' . $mois->format('Ym');       // ex. DEMOEM202507
            $numeroArticle  = 'ART-' . $mois->format('Y-m');        // ex. ART-2025-07

            DB::table('emission_taxe')->insertOrIgnore([
                'numero_emission'    => $numeroEmission,
                'numero_article'     => $numeroArticle,
                'etablissement_id'   => $etabId,
                'collectivite_id'    => $collectiviteId,
                'nature_taxe_id'     => $natureTpvId,
                'periodicite_id'     => $periodiciteId,
                'exercice_fiscal_id' => $exerciceId,
                'ca_annuel'          => $montant * 250,             // CA implicite (~0,40 %), pour cohérence d'affichage
                'montant_annuel'     => $montant,
                'montant_periode'    => $montant,
                'date_declaration'   => $mois->startOfMonth()->toDateString(),
                'date_liquidation'   => $mois->startOfMonth()->addDay()->toDateString(),
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            $emissionId = DB::table('emission_taxe')->where('numero_emission', $numeroEmission)->value('id');

            DB::table('reglement_taxe')->insertOrIgnore([
                'numero_reglement'   => 'DEMORG' . $mois->format('ym'),   // ex. DEMORG2507
                'emission_taxe_id'   => $emissionId,
                'collectivite_id'    => $collectiviteId,
                'recette_id'         => $recetteId,
                'exercice_fiscal_id' => $exerciceId,
                'date_reglement'     => $mois->day(15)->toDateString(),
                'montant'            => $montant,
                'montant_impute'     => $montant,
                'mode_reglement_id'  => $modes[self::MODES[$i % count(self::MODES)]],
                'type_reglement_id'  => $typeTotalId,
                'numero_quittance'   => 'QUIT-' . $mois->format('Y-m'),
            ]);
        }
    }
}
