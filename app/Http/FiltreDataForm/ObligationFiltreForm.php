<?php

namespace App\Http\FiltreDataForm;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ObligationFiltreForm extends FiltreDataForm
{
    public function __construct(
        public readonly ?string $contribuable  = null,
        public readonly ?int    $natureTaxeId  = null,
        public readonly ?int    $periodiciteId = null,
    ) {}

    public static function regles(): array
    {
        return [
            'contribuable'   => ['nullable', 'string', 'max:128'],
            'nature_taxe_id' => ['nullable', 'integer', 'min:1'],
            'periodicite_id' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public static function fromRequest(Request $request): static
    {
        static::valider($request);

        return new static(
            contribuable:  $request->input('contribuable'),
            natureTaxeId:  $request->filled('nature_taxe_id') ? (int) $request->input('nature_taxe_id') : null,
            periodiciteId: $request->filled('periodicite_id') ? (int) $request->input('periodicite_id') : null,
        );
    }

    public function appliquer(Builder $query): Builder
    {
        if (filled($this->contribuable)) {
            $terme = "%{$this->contribuable}%";
            $query->whereHas('contribuable', function (Builder $q) use ($terme) {
                $q->where('nom', 'ilike', $terme)
                  ->orWhere('prenoms', 'ilike', $terme)
                  ->orWhere('raison_sociale', 'ilike', $terme)
                  ->orWhere('numero_identifiant', 'ilike', $terme);
            });
        }

        if (filled($this->natureTaxeId)) {
            $query->where('nature_taxe_id', $this->natureTaxeId);
        }

        if (filled($this->periodiciteId)) {
            $query->where('periodicite_id', $this->periodiciteId);
        }

        return $query;
    }
}
