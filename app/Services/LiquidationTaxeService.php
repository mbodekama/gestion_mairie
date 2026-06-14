<?php

namespace App\Services;

use App\Models\BaremeTaxe;
use App\Models\Periodicite;

/**
 * Liquidation d'une émission de taxe proportionnelle (patente, TEN…) :
 * détermine le barème applicable (nature + périodicité + catégorie d'activité
 * + tranche de chiffre d'affaires) et calcule les montants annuel, par période
 * et au prorata temporis.
 *
 * Le taux du barème est exprimé en pourcentage du chiffre d'affaires :
 *   montant_annuel = CA × taux / 100
 * Tous les calculs monétaires sont faits en bcmath (jamais en float PHP).
 */
class LiquidationTaxeService
{
    /**
     * @return array{bareme_id:?int, taux:?string, montant_annuel:string, montant_periode:string, montant_prorata:string}
     */
    public function liquider(
        int $natureTaxeId,
        int $periodiciteId,
        string $caAnnuel,
        ?int $categorieActiviteId = null,
        ?int $nbMoisProrata = null,
    ): array {
        $ca = $caAnnuel !== '' ? $caAnnuel : '0';

        $bareme = BaremeTaxe::query()
            ->where('nature_taxe_id', $natureTaxeId)
            ->where('periodicite_id', $periodiciteId)
            ->where(function ($q) use ($categorieActiviteId) {
                // Barème général (catégorie NULL) ou spécifique à la catégorie de l'activité
                $q->whereNull('categorie_activite_id');
                if ($categorieActiviteId !== null) {
                    $q->orWhere('categorie_activite_id', $categorieActiviteId);
                }
            })
            ->where('ca_borne_inf', '<=', $ca)
            ->where(function ($q) use ($ca) {
                // ca_borne_sup = 0 ⇒ tranche ouverte (au-delà)
                $q->where('ca_borne_sup', 0)
                  ->orWhere('ca_borne_sup', '>=', $ca);
            })
            // Le barème spécifique à la catégorie prime sur le barème général (NULL)
            ->orderByRaw('categorie_activite_id IS NULL')
            ->first();

        if (! $bareme) {
            return [
                'bareme_id'       => null,
                'taux'            => null,
                'montant_annuel'  => '0.00',
                'montant_periode' => '0.00',
                'montant_prorata' => '0.00',
            ];
        }

        $taux = (string) $bareme->taux;

        // montant_annuel = CA × taux / 100
        $montantAnnuel = bcdiv(bcmul($ca, $taux, 6), '100', 2);

        // montant_periode = montant_annuel × nb_mois / 12 (= annuel si périodicité sans nb_mois)
        $nbMois = Periodicite::whereKey($periodiciteId)->value('nb_mois');
        $montantPeriode = $nbMois
            ? bcdiv(bcmul($montantAnnuel, (string) $nbMois, 2), '12', 2)
            : $montantAnnuel;

        // montant_prorata = montant_annuel × nb_mois_prorata / 12
        $montantProrata = $nbMoisProrata
            ? bcdiv(bcmul($montantAnnuel, (string) $nbMoisProrata, 2), '12', 2)
            : '0.00';

        return [
            'bareme_id'       => $bareme->id,
            'taux'            => $taux,
            'montant_annuel'  => $montantAnnuel,
            'montant_periode' => $montantPeriode,
            'montant_prorata' => $montantProrata,
        ];
    }
}
