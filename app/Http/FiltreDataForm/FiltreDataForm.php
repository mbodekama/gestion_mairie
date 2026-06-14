<?php

namespace App\Http\FiltreDataForm;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class FiltreDataForm
{
    /**
     * Retourne les règles de validation Laravel pour les champs du filtre.
     * Tous les champs sont optionnels par nature ; on valide uniquement le
     * format/type lorsqu'ils sont fournis.
     *
     * @return array<string, mixed>
     */
    abstract public static function regles(): array;

    /**
     * Valide la requête puis construit l'objet filtre.
     * En cas d'échec de validation, Laravel redirige automatiquement en arrière
     * avec les erreurs et les anciennes valeurs (comportement web standard).
     */
    abstract public static function fromRequest(Request $request): static;

    /**
     * Applique les critères de filtrage sur le Builder Eloquent fourni.
     */
    abstract public function appliquer(Builder $query): Builder;

    /**
     * Valide la requête via les règles définies par la sous-classe.
     * À appeler en début de fromRequest().
     */
    protected static function valider(Request $request): void
    {
        $request->validate(static::regles());
    }

    /**
     * Parse une date au format d/m/Y vers Y-m-d (format SQL).
     * Retourne null si la valeur est vide ou invalide.
     */
    protected static function parseDate(?string $valeur): ?string
    {
        if (blank($valeur)) {
            return null;
        }

        try {
            return Carbon::createFromFormat('d/m/Y', $valeur)->toDateString();
        } catch (\Exception) {
            return null;
        }
    }
}
