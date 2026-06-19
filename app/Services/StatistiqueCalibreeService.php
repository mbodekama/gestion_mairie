<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Statistique calibrée : génère une série temporelle paramétrable pour un objet
 * fiscal donné (contribuables, établissements, émissions, recouvrements), sur une
 * plage de période, avec un regroupement (granularité) au choix et, en option, un
 * second objet de comparaison.
 *
 * Les agrégats monétaires sont réalisés côté PostgreSQL sur des colonnes NUMERIC
 * (jamais de calcul monétaire sur float PHP). Toutes les clés de catalogue sont des
 * listes blanches : aucune entrée utilisateur n'atteint le SQL de façon dynamique.
 */
class StatistiqueCalibreeService
{
    /**
     * Catalogue des objets analysables.
     * - `table`   : table source PostgreSQL
     * - `date`    : colonne de date pivot (axe temporel)
     * - `agregat` : 'count' (dénombrement) ou 'sum' (somme NUMERIC)
     * - `colonne` : colonne sommée si agregat = 'sum'
     * - `unite`   : unité d'affichage ('FCFA' ou '')
     * - `where`   : conditions WHERE additionnelles (ex: exclure les annulés)
     */
    public const OBJETS = [
        'contribuables' => [
            'libelle' => 'Contribuables recensés (nombre)',
            'table'   => 'contribuable',
            'date'    => 'created_at',
            'agregat' => 'count',
            'colonne' => null,
            'unite'   => '',
            'where'   => [],
        ],
        'etablissements' => [
            'libelle' => 'Établissements recensés (nombre)',
            'table'   => 'etablissement',
            'date'    => 'created_at',
            'agregat' => 'count',
            'colonne' => null,
            'unite'   => '',
            'where'   => [],
        ],
        'emissions_montant' => [
            'libelle' => 'Émissions de taxe — montant émis (FCFA)',
            'table'   => 'emission_taxe',
            'date'    => 'date_liquidation',
            'agregat' => 'sum',
            'colonne' => 'montant_annuel',
            'unite'   => 'FCFA',
            'where'   => [],
        ],
        'emissions_nombre' => [
            'libelle' => 'Émissions de taxe (nombre)',
            'table'   => 'emission_taxe',
            'date'    => 'date_liquidation',
            'agregat' => 'count',
            'colonne' => null,
            'unite'   => '',
            'where'   => [],
        ],
        'reglements_montant' => [
            'libelle' => 'Recouvrements — montant encaissé (FCFA)',
            'table'   => 'reglement_taxe',
            'date'    => 'date_reglement',
            'agregat' => 'sum',
            'colonne' => 'montant',
            'unite'   => 'FCFA',
            'where'   => [['annule_le', 'null']],
        ],
        'reglements_nombre' => [
            'libelle' => 'Recouvrements (nombre)',
            'table'   => 'reglement_taxe',
            'date'    => 'date_reglement',
            'agregat' => 'count',
            'colonne' => null,
            'unite'   => '',
            'where'   => [['annule_le', 'null']],
        ],
    ];

    /** Regroupement temporel (axe des abscisses). */
    public const GRANULARITES = [
        'jour'      => 'Jour',
        'mois'      => 'Mois',
        'trimestre' => 'Trimestre',
        'annee'     => 'Année',
    ];

    /** Types de diagramme proposés (faciles à exprimer sur une série temporelle). */
    public const DIAGRAMMES = [
        'barres'  => 'Barres',
        'courbes' => 'Courbes',
        'aires'   => 'Aires',
    ];

    /** Correspondance granularité → unité PostgreSQL date_trunc. */
    private const TRUNC = [
        'jour'      => 'day',
        'mois'      => 'month',
        'trimestre' => 'quarter',
        'annee'     => 'year',
    ];

    /**
     * Génère la charge utile du graphique.
     *
     * @return array{
     *     labels: array<int,string>,
     *     series: array<int,array{nom:string,data:array<int,float>}>,
     *     diagramme: string, unite: string, vide: bool
     * }
     */
    public function generer(
        int $collectiviteId,
        string $objet,
        string $granularite,
        string $diagramme,
        ?string $dateDebut = null,
        ?string $dateFin = null,
        ?string $objetCompare = null,
    ): array {
        $serie1 = $this->serie($collectiviteId, $objet, $granularite, $dateDebut, $dateFin);

        $series  = [];
        $periodes = $serie1;          // clés période → valeur du modèle 1
        $serie2  = null;

        if ($objetCompare !== null && $objetCompare !== '' && $objetCompare !== $objet) {
            $serie2 = $this->serie($collectiviteId, $objetCompare, $granularite, $dateDebut, $dateFin);
        }

        // Union ordonnée des périodes des deux séries pour aligner les abscisses.
        $cles = array_keys($serie1);
        if ($serie2 !== null) {
            $cles = array_unique(array_merge($cles, array_keys($serie2)));
        }
        sort($cles);

        $labels = array_map(fn ($cle) => $this->formaterLabel($cle, $granularite), $cles);

        $series[] = [
            'nom'  => self::OBJETS[$objet]['libelle'],
            'data' => array_map(fn ($cle) => $serie1[$cle] ?? 0, $cles),
        ];

        if ($serie2 !== null) {
            $series[] = [
                'nom'  => self::OBJETS[$objetCompare]['libelle'],
                'data' => array_map(fn ($cle) => $serie2[$cle] ?? 0, $cles),
            ];
        }

        return [
            'labels'    => $labels,
            'series'    => $series,
            'diagramme' => $diagramme,
            'unite'     => self::OBJETS[$objet]['unite'],
            'vide'      => count($cles) === 0,
        ];
    }

    /**
     * Calcule une série [périodeISO => valeur] pour un objet du catalogue.
     *
     * @return array<string,float>
     */
    private function serie(
        int $collectiviteId,
        string $objet,
        string $granularite,
        ?string $dateDebut,
        ?string $dateFin,
    ): array {
        $cfg   = self::OBJETS[$objet];
        $trunc = self::TRUNC[$granularite];
        $dateCol = $cfg['date'];

        $agregat = $cfg['agregat'] === 'sum'
            ? "COALESCE(SUM({$cfg['colonne']}), 0)"
            : 'COUNT(*)';

        $query = DB::table($cfg['table'])
            ->selectRaw("date_trunc('{$trunc}', {$dateCol}) AS periode, {$agregat} AS valeur")
            ->where('collectivite_id', $collectiviteId)
            ->whereNotNull($dateCol);

        foreach ($cfg['where'] as [$colonne, $condition]) {
            if ($condition === 'null') {
                $query->whereNull($colonne);
            }
        }

        if ($dateDebut !== null) {
            $query->whereDate($dateCol, '>=', $dateDebut);
        }
        if ($dateFin !== null) {
            $query->whereDate($dateCol, '<=', $dateFin);
        }

        return $query->groupBy('periode')
            ->orderBy('periode')
            ->get()
            ->mapWithKeys(fn ($row) => [
                Carbon::parse($row->periode)->toDateString() => (float) $row->valeur,
            ])
            ->all();
    }

    /** Formate la clé de période selon la granularité choisie. */
    private function formaterLabel(string $cleIso, string $granularite): string
    {
        $date = Carbon::parse($cleIso);

        return match ($granularite) {
            'jour'      => $date->format('d/m/Y'),
            'mois'      => $date->translatedFormat('M Y'),
            'trimestre' => 'T' . $date->quarter . ' ' . $date->year,
            'annee'     => (string) $date->year,
            default     => $cleIso,
        };
    }
}
