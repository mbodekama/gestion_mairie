<?php

namespace App\Http\FiltreDataForm;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class NatureTaxeFiltreForm extends FiltreDataForm
{
    public function __construct(
        public readonly ?string $code                  = null,
        public readonly ?string $libelle               = null,
        public readonly ?int    $domaineTaxeId         = null,
        public readonly ?int    $categorieImpotTaxeId  = null,
    ) {}

    public static function regles(): array
    {
        return [
            'code'                    => ['nullable', 'string', 'max:3'],
            'libelle'                 => ['nullable', 'string', 'max:128'],
            'domaine_taxe_id'         => ['nullable', 'integer', 'min:1'],
            'categorie_impot_taxe_id' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public static function fromRequest(Request $request): static
    {
        static::valider($request);

        return new static(
            code:                 $request->input('code'),
            libelle:              $request->input('libelle'),
            domaineTaxeId:        $request->filled('domaine_taxe_id') ? (int) $request->input('domaine_taxe_id') : null,
            categorieImpotTaxeId: $request->filled('categorie_impot_taxe_id') ? (int) $request->input('categorie_impot_taxe_id') : null,
        );
    }

    public function appliquer(Builder $query): Builder
    {
        if (filled($this->code)) {
            $query->where('code', 'ilike', "%{$this->code}%");
        }

        if (filled($this->libelle)) {
            $terme = "%{$this->libelle}%";
            $query->where(function (Builder $q) use ($terme) {
                $q->where('libelle', 'ilike', $terme)
                  ->orWhere('libelle_court', 'ilike', $terme);
            });
        }

        if (filled($this->domaineTaxeId)) {
            $query->where('domaine_taxe_id', $this->domaineTaxeId);
        }

        if (filled($this->categorieImpotTaxeId)) {
            $query->where('categorie_impot_taxe_id', $this->categorieImpotTaxeId);
        }

        return $query;
    }
}
