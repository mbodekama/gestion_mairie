<?php

namespace App\Http\FiltreDataForm;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class EmissionTaxeFiltreForm extends FiltreDataForm
{
    public function __construct(
        public readonly ?string $numeroEmission    = null,
        public readonly ?string $etablissement     = null,
        public readonly ?int    $natureTaxeId      = null,
        public readonly ?int    $periodiciteId     = null,
        public readonly ?int    $exerciceFiscalId  = null,
    ) {}

    public static function regles(): array
    {
        return [
            'numero_emission'    => ['nullable', 'string', 'max:20'],
            'etablissement'      => ['nullable', 'string', 'max:128'],
            'nature_taxe_id'     => ['nullable', 'integer', 'min:1'],
            'periodicite_id'     => ['nullable', 'integer', 'min:1'],
            'exercice_fiscal_id' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public static function fromRequest(Request $request): static
    {
        static::valider($request);

        return new static(
            numeroEmission:   $request->input('numero_emission'),
            etablissement:    $request->input('etablissement'),
            natureTaxeId:     $request->filled('nature_taxe_id')     ? (int) $request->input('nature_taxe_id')     : null,
            periodiciteId:    $request->filled('periodicite_id')     ? (int) $request->input('periodicite_id')     : null,
            exerciceFiscalId: $request->filled('exercice_fiscal_id') ? (int) $request->input('exercice_fiscal_id') : null,
        );
    }

    public function appliquer(Builder $query): Builder
    {
        if (filled($this->numeroEmission)) {
            $query->where('numero_emission', 'ilike', "%{$this->numeroEmission}%");
        }

        if (filled($this->etablissement)) {
            $terme = "%{$this->etablissement}%";
            $query->whereHas('etablissement', function (Builder $q) use ($terme) {
                $q->where('numero',        'ilike', $terme)
                  ->orWhere('denomination','ilike', $terme);
            });
        }

        if (filled($this->natureTaxeId)) {
            $query->where('nature_taxe_id', $this->natureTaxeId);
        }

        if (filled($this->periodiciteId)) {
            $query->where('periodicite_id', $this->periodiciteId);
        }

        if (filled($this->exerciceFiscalId)) {
            $query->where('exercice_fiscal_id', $this->exerciceFiscalId);
        }

        return $query;
    }
}
