<?php

namespace App\Services;

use App\Models\LigneExoneration;

/**
 * Recherche et applique l'exonération fiscale d'un contribuable lors de
 * l'émission d'une taxe : abattement du montant au taux de la ligne
 * d'exonération active (nature de taxe + année d'application).
 */
class ExonerationService
{
    /**
     * Calcule l'abattement applicable à un montant brut pour un contribuable,
     * une nature de taxe et un exercice donnés. Retourne null si aucune
     * exonération active.
     *
     * @return array{exoneration_id:int, taux:string, montant_exonere:string, facteur:string}|null
     */
    public function appliquer(?int $contribuableId, int $natureTaxeId, int $annee, string $montantBrut): ?array
    {
        if (! $contribuableId) {
            return null;
        }

        // Ligne d'exonération active : même contribuable, même nature, année d'application,
        // dans la période de validité de l'exonération (si renseignée).
        $ligne = LigneExoneration::query()
            ->where('nature_taxe_id', $natureTaxeId)
            ->where('annee_application', $annee)
            ->whereHas('exoneration', function ($q) use ($contribuableId, $annee) {
                $q->where('contribuable_id', $contribuableId)
                  ->where(fn ($d) => $d->whereNull('date_debut')->orWhereYear('date_debut', '<=', $annee))
                  ->where(fn ($d) => $d->whereNull('date_fin')->orWhereYear('date_fin', '>=', $annee));
            })
            ->orderByDesc('taux')
            ->first();

        if (! $ligne || (float) $ligne->taux <= 0) {
            return null;
        }

        $taux = (string) $ligne->taux;
        // montant exonéré = montant brut × taux / 100 ; facteur résiduel = (100 − taux)/100
        $montantExonere = bcdiv(bcmul($montantBrut, $taux, 6), '100', 2);
        $facteur        = bcdiv(bcsub('100', $taux, 6), '100', 6);

        return [
            'exoneration_id'  => (int) $ligne->exoneration_id,
            'taux'            => $taux,
            'montant_exonere' => $montantExonere,
            'facteur'         => $facteur,
        ];
    }
}
