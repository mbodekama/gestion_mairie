<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

/**
 * Statistiques alimentant le tableau de bord.
 *
 * Les agrégations monétaires sont réalisées côté PostgreSQL (SUM sur NUMERIC) ;
 * les montants ne sont convertis en float que pour l'affichage graphique.
 */
class TableauBordService
{
    /** Libellés courts des mois en français (index 1 = janvier). */
    private const MOIS_FR = [
        1 => 'janv.', 2 => 'févr.', 3 => 'mars', 4 => 'avr.', 5 => 'mai', 6 => 'juin',
        7 => 'juil.', 8 => 'août', 9 => 'sept.', 10 => 'oct.', 11 => 'nov.', 12 => 'déc.',
    ];

    /**
     * Recouvrements (règlements encaissés, hors annulés) des 12 derniers mois
     * pour une collectivité, regroupés par mois et complétés des mois sans
     * encaissement (montant = 0).
     *
     * @return array{labels: list<string>, montants: list<float>, total: float, mois_courant: float, mois_precedent: float}
     */
    public function recouvrementsDouzeDerniersMois(int $collectiviteId): array
    {
        $debut = CarbonImmutable::now()->startOfMonth()->subMonths(11);
        $fin   = CarbonImmutable::now()->endOfMonth();

        // Somme des montants par mois (clé 'YYYY-MM'), hors règlements annulés.
        $parMois = DB::table('reglement_taxe')
            ->selectRaw("to_char(date_reglement, 'YYYY-MM') as ym, SUM(montant) as total")
            ->where('collectivite_id', $collectiviteId)
            ->whereNull('annule_le')
            ->whereNotNull('date_reglement')
            ->whereBetween('date_reglement', [$debut->toDateString(), $fin->toDateString()])
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $labels   = [];
        $montants = [];

        for ($i = 0; $i < 12; $i++) {
            $mois       = $debut->addMonths($i);
            $labels[]   = self::MOIS_FR[(int) $mois->format('n')] . ' ' . $mois->format('y');
            $montants[] = (float) ($parMois[$mois->format('Y-m')] ?? 0);
        }

        return [
            'labels'         => $labels,
            'montants'       => $montants,
            'total'          => array_sum($montants),
            'mois_courant'   => $montants[11] ?? 0.0,
            'mois_precedent' => $montants[10] ?? 0.0,
        ];
    }

    /**
     * Montants émis (émissions de taxe liquidées) des 12 derniers mois pour une
     * collectivité, regroupés par mois de liquidation et complétés des mois sans
     * émission (montant = 0). Montant net = montant_periode + pénalité − exonéré.
     *
     * @return array{labels: list<string>, montants: list<float>, total: float, mois_courant: float, mois_precedent: float}
     */
    public function emissionsDouzeDerniersMois(int $collectiviteId): array
    {
        $debut = CarbonImmutable::now()->startOfMonth()->subMonths(11);
        $fin   = CarbonImmutable::now()->endOfMonth();

        $parMois = DB::table('emission_taxe')
            ->selectRaw("to_char(date_liquidation, 'YYYY-MM') as ym, SUM(montant_periode + penalite - montant_exonere) as total")
            ->where('collectivite_id', $collectiviteId)
            ->whereNotNull('date_liquidation')
            ->whereBetween('date_liquidation', [$debut->toDateString(), $fin->toDateString()])
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $labels   = [];
        $montants = [];

        for ($i = 0; $i < 12; $i++) {
            $mois       = $debut->addMonths($i);
            $labels[]   = self::MOIS_FR[(int) $mois->format('n')] . ' ' . $mois->format('y');
            $montants[] = (float) ($parMois[$mois->format('Y-m')] ?? 0);
        }

        return [
            'labels'         => $labels,
            'montants'       => $montants,
            'total'          => array_sum($montants),
            'mois_courant'   => $montants[11] ?? 0.0,
            'mois_precedent' => $montants[10] ?? 0.0,
        ];
    }

    /**
     * Indicateurs clés (KPI) du tableau de bord pour une collectivité, calculés
     * sur l'exercice fiscal ouvert (le plus récent non clôturé).
     *
     * Les agrégats monétaires sont sommés côté PostgreSQL sur des colonnes NUMERIC
     * (montant émis net = montant_periode + pénalité − montant exonéré) et ne sont
     * castés en float que pour l'affichage.
     *
     * @return array{
     *     contribuables_actifs: int,
     *     etablissements_actifs: int,
     *     exercice_annee: int|null,
     *     montant_emis: float,
     *     montant_recouvre: float,
     *     reste_a_recouvrer: float,
     *     taux_recouvrement: float,
     *     nb_emissions: int
     * }
     */
    public function indicateursCles(int $collectiviteId): array
    {
        // Exercice fiscal ouvert le plus récent de la collectivité.
        $exercice = $this->exerciceOuvert($collectiviteId);

        // Recensement : contribuables et établissements actifs (non supprimés).
        $contribuablesActifs = (int) DB::table('contribuable')
            ->where('collectivite_id', $collectiviteId)
            ->where('statut', 'ACTIF')
            ->whereNull('supprime_le')
            ->count();

        $etablissementsActifs = (int) DB::table('etablissement')
            ->where('collectivite_id', $collectiviteId)
            ->count();

        $montantEmis     = 0.0;
        $montantRecouvre = 0.0;
        $nbEmissions     = 0;

        if ($exercice !== null) {
            // Montant émis net de l'exercice (pénalités incluses, exonérations déduites).
            $emis = DB::table('emission_taxe')
                ->where('collectivite_id', $collectiviteId)
                ->where('exercice_fiscal_id', $exercice->id)
                ->selectRaw('COUNT(*) as nb, COALESCE(SUM(montant_periode + penalite - montant_exonere), 0) as total')
                ->first();

            $montantEmis = (float) $emis->total;
            $nbEmissions = (int) $emis->nb;

            // Recouvré sur l'exercice (règlements encaissés, hors annulés).
            $montantRecouvre = (float) DB::table('reglement_taxe')
                ->where('collectivite_id', $collectiviteId)
                ->where('exercice_fiscal_id', $exercice->id)
                ->whereNull('annule_le')
                ->whereNotNull('date_reglement')
                ->sum('montant');
        }

        $resteARecouvrer = max($montantEmis - $montantRecouvre, 0.0);
        $taux = $montantEmis > 0 ? round($montantRecouvre / $montantEmis * 100, 1) : 0.0;

        return [
            'contribuables_actifs'  => $contribuablesActifs,
            'etablissements_actifs' => $etablissementsActifs,
            'exercice_annee'        => $exercice?->annee,
            'montant_emis'          => $montantEmis,
            'montant_recouvre'      => $montantRecouvre,
            'reste_a_recouvrer'     => $resteARecouvrer,
            'taux_recouvrement'     => $taux,
            'nb_emissions'          => $nbEmissions,
        ];
    }

    /**
     * Répartitions analytiques de l'exercice fiscal ouvert, destinées aux cartes
     * à mini-graphique du tableau de bord :
     *  - objectif       : réalisé vs objectif annuel de recouvrement (jauge) ;
     *  - natures_taxe   : montant émis par nature de taxe (barres, top 6) ;
     *  - modes_reglement: recouvrements par mode de règlement (anneau) ;
     *  - personnes      : structure des contribuables PP / PM (anneau).
     *
     * Tous les agrégats monétaires sont sommés côté PostgreSQL sur des NUMERIC.
     *
     * @return array{
     *     objectif: array{montant: float, recouvre: float, taux: float},
     *     natures_taxe: array{labels: list<string>, montants: list<float>},
     *     modes_reglement: array{labels: list<string>, montants: list<float>},
     *     personnes: array{physiques: int, morales: int}
     * }
     */
    public function repartitions(int $collectiviteId): array
    {
        $exercice = $this->exerciceOuvert($collectiviteId);

        // --- Objectif de recouvrement (réalisé vs cible révisée le cas échéant) ---
        $objectifMontant  = 0.0;
        $recouvreExercice = 0.0;
        $natures          = ['labels' => [], 'montants' => []];
        $modes            = ['labels' => [], 'montants' => []];

        if ($exercice !== null) {
            $objectif = DB::table('objectif')
                ->where('collectivite_id', $collectiviteId)
                ->where('annee', $exercice->annee)
                ->first(['montant', 'montant_revise']);

            $objectifMontant = (float) ($objectif->montant_revise ?? $objectif->montant ?? 0);

            $recouvreExercice = (float) DB::table('reglement_taxe')
                ->where('collectivite_id', $collectiviteId)
                ->where('exercice_fiscal_id', $exercice->id)
                ->whereNull('annule_le')
                ->whereNotNull('date_reglement')
                ->sum('montant');

            // --- Montant émis par nature de taxe (top 5) ---
            $lignesNatures = DB::table('emission_taxe as e')
                ->join('nature_taxe as n', 'n.id', '=', 'e.nature_taxe_id')
                ->where('e.collectivite_id', $collectiviteId)
                ->where('e.exercice_fiscal_id', $exercice->id)
                ->selectRaw('COALESCE(n.libelle_court, n.libelle, n.code) as libelle, SUM(e.montant_periode) as total')
                ->groupByRaw('COALESCE(n.libelle_court, n.libelle, n.code)')
                ->orderByDesc('total')
                ->limit(5)
                ->get();

            $natures = [
                'labels'   => $lignesNatures->pluck('libelle')->all(),
                'montants' => $lignesNatures->map(fn ($l) => (float) $l->total)->all(),
            ];

            // --- Recouvrements par mode de règlement (hors annulés) ---
            $lignesModes = DB::table('reglement_taxe as r')
                ->join('mode_reglement as m', 'm.id', '=', 'r.mode_reglement_id')
                ->where('r.collectivite_id', $collectiviteId)
                ->where('r.exercice_fiscal_id', $exercice->id)
                ->whereNull('r.annule_le')
                ->whereNotNull('r.date_reglement')
                ->selectRaw('m.libelle as libelle, SUM(r.montant) as total')
                ->groupBy('m.libelle')
                ->orderByDesc('total')
                ->get();

            $modes = [
                'labels'   => $lignesModes->pluck('libelle')->all(),
                'montants' => $lignesModes->map(fn ($l) => (float) $l->total)->all(),
            ];
        }

        $taux = $objectifMontant > 0 ? round($recouvreExercice / $objectifMontant * 100, 1) : 0.0;

        // --- Structure des contribuables actifs (PP / PM) ---
        $parType = DB::table('contribuable')
            ->where('collectivite_id', $collectiviteId)
            ->where('statut', 'ACTIF')
            ->whereNull('supprime_le')
            ->selectRaw('type_personne, COUNT(*) as nb')
            ->groupBy('type_personne')
            ->pluck('nb', 'type_personne');

        return [
            'objectif' => [
                'montant'  => $objectifMontant,
                'recouvre' => $recouvreExercice,
                'taux'     => $taux,
            ],
            'natures_taxe'    => $natures,
            'modes_reglement' => $modes,
            'personnes'       => [
                'physiques' => (int) ($parType['PP'] ?? 0),
                'morales'   => (int) ($parType['PM'] ?? 0),
            ],
        ];
    }

    /**
     * Top des contribuables d'une collectivité classés sur le montant total
     * recouvré (règlements encaissés, hors annulés), toutes natures d'impôt
     * (émissions de taxe et cotisations foncières) et tous établissements
     * confondus.
     *
     * @return list<array{
     *     contribuable_id: int,
     *     nom_affiche: string,
     *     initiale: string,
     *     numero: string,
     *     type_personne: string,
     *     total: float,
     *     pourcentage: float
     * }>
     */
    public function topContribuables(int $collectiviteId, int $limite = 5): array
    {
        // Le règlement cible une émission de taxe OU une cotisation foncière ;
        // on remonte au contribuable via l'établissement dans les deux cas.
        $lignes = DB::table('reglement_taxe as r')
            ->leftJoin('emission_taxe as et', 'et.id', '=', 'r.emission_taxe_id')
            ->leftJoin('emission_cotisation_fonciere as ec', 'ec.id', '=', 'r.emission_cotisation_id')
            ->join('etablissement as etab', 'etab.id', '=', DB::raw('COALESCE(et.etablissement_id, ec.etablissement_id)'))
            ->join('contribuable as c', 'c.id', '=', 'etab.contribuable_id')
            ->where('r.collectivite_id', $collectiviteId)
            ->whereNull('r.annule_le')
            ->whereNotNull('r.date_reglement')
            ->groupBy('c.id', 'c.type_personne', 'c.nom', 'c.prenoms', 'c.raison_sociale', 'c.numero_identifiant')
            ->selectRaw('c.id, c.type_personne, c.nom, c.prenoms, c.raison_sociale, c.numero_identifiant, SUM(r.montant) as total')
            ->orderByDesc('total')
            ->limit($limite)
            ->get();

        $max = (float) ($lignes->max('total') ?? 0);

        return $lignes->map(function ($l) use ($max) {
            $nom = $l->type_personne === 'PM'
                ? (string) $l->raison_sociale
                : trim($l->nom . ' ' . ($l->prenoms ?? ''));

            $total = (float) $l->total;

            return [
                'contribuable_id' => (int) $l->id,
                'nom_affiche'     => $nom !== '' ? $nom : $l->numero_identifiant,
                'initiale'        => mb_strtoupper(mb_substr($nom !== '' ? $nom : $l->numero_identifiant, 0, 1)),
                'numero'          => (string) $l->numero_identifiant,
                'type_personne'   => (string) $l->type_personne,
                'total'           => $total,
                'pourcentage'     => $max > 0 ? round($total / $max * 100, 1) : 0.0,
            ];
        })->all();
    }

    /**
     * Exercice fiscal ouvert le plus récent (non clôturé) d'une collectivité.
     */
    private function exerciceOuvert(int $collectiviteId): ?object
    {
        return DB::table('exercice_fiscal')
            ->where('collectivite_id', $collectiviteId)
            ->where('cloture', false)
            ->orderByDesc('annee')
            ->first(['id', 'annee']);
    }
}
