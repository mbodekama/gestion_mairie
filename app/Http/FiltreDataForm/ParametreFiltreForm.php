<?php

namespace App\Http\FiltreDataForm;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ParametreFiltreForm extends FiltreDataForm
{
    public function __construct(
        public readonly ?string $cle    = null,
        public readonly ?string $valeur = null,
    ) {}

    public static function regles(): array
    {
        return [
            'cle'    => ['nullable', 'string', 'max:64'],
            'valeur' => ['nullable', 'string', 'max:255'],
        ];
    }

    public static function fromRequest(Request $request): static
    {
        static::valider($request);

        return new static(
            cle:    $request->input('cle'),
            valeur: $request->input('valeur'),
        );
    }

    public function appliquer(Builder $query): Builder
    {
        if (filled($this->cle)) {
            $query->where('cle', 'ilike', "%{$this->cle}%");
        }

        if (filled($this->valeur)) {
            $query->where('valeur', 'ilike', "%{$this->valeur}%");
        }

        return $query;
    }
}
