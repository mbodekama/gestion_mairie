<?php

namespace App\Http\FiltreDataForm;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ObjectifFiltreForm extends FiltreDataForm
{
    public function __construct(
        public readonly ?int    $annee       = null,
        public readonly ?string $montantMin  = null,
        public readonly ?string $montantMax  = null,
    ) {}

    public static function regles(): array
    {
        return [
            'annee'       => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'montant_min' => ['nullable', 'numeric', 'min:0'],
            'montant_max' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public static function fromRequest(Request $request): static
    {
        static::valider($request);

        return new static(
            annee:      $request->filled('annee')       ? (int) $request->input('annee')       : null,
            montantMin: $request->filled('montant_min') ? $request->input('montant_min')        : null,
            montantMax: $request->filled('montant_max') ? $request->input('montant_max')        : null,
        );
    }

    public function appliquer(Builder $query): Builder
    {
        if (filled($this->annee)) {
            $query->where('annee', $this->annee);
        }

        if (filled($this->montantMin)) {
            $query->where('montant', '>=', $this->montantMin);
        }

        if (filled($this->montantMax)) {
            $query->where('montant', '<=', $this->montantMax);
        }

        return $query;
    }
}
