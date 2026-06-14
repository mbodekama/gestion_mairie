<?php

namespace App\Http\FiltreDataForm;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class BaremeTaxeFiltreForm extends FiltreDataForm
{
    public function __construct(
        public readonly ?int $natureTaxeId       = null,
        public readonly ?int $periodiciteId      = null,
        public readonly ?int $categorieActiviteId = null,
    ) {}

    public static function regles(): array
    {
        return [
            'nature_taxe_id'       => ['nullable', 'integer', 'min:1'],
            'periodicite_id'       => ['nullable', 'integer', 'min:1'],
            'categorie_activite_id' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public static function fromRequest(Request $request): static
    {
        static::valider($request);

        return new static(
            natureTaxeId:        $request->filled('nature_taxe_id')        ? (int) $request->input('nature_taxe_id')        : null,
            periodiciteId:       $request->filled('periodicite_id')        ? (int) $request->input('periodicite_id')        : null,
            categorieActiviteId: $request->filled('categorie_activite_id') ? (int) $request->input('categorie_activite_id') : null,
        );
    }

    public function appliquer(Builder $query): Builder
    {
        if (filled($this->natureTaxeId)) {
            $query->where('nature_taxe_id', $this->natureTaxeId);
        }

        if (filled($this->periodiciteId)) {
            $query->where('periodicite_id', $this->periodiciteId);
        }

        if (filled($this->categorieActiviteId)) {
            $query->where('categorie_activite_id', $this->categorieActiviteId);
        }

        return $query;
    }
}
