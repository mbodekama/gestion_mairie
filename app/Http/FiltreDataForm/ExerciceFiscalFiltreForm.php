<?php

namespace App\Http\FiltreDataForm;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ExerciceFiscalFiltreForm extends FiltreDataForm
{
    public function __construct(
        public readonly ?int  $annee    = null,
        public readonly ?bool $cloture  = null,
    ) {}

    public static function regles(): array
    {
        return [
            'annee'   => ['nullable', 'integer', 'digits:4', 'min:2000', 'max:2099'],
            'cloture' => ['nullable', 'in:0,1'],
        ];
    }

    public static function fromRequest(Request $request): static
    {
        static::valider($request);

        return new static(
            annee:   $request->filled('annee')   ? (int) $request->input('annee')        : null,
            cloture: $request->filled('cloture') ? ($request->input('cloture') === '1')  : null,
        );
    }

    public function appliquer(Builder $query): Builder
    {
        if (filled($this->annee)) {
            $query->where('annee', $this->annee);
        }

        if ($this->cloture !== null) {
            $query->where('cloture', $this->cloture);
        }

        return $query;
    }
}
