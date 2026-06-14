<?php

namespace App\Http\FiltreDataForm;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class EtablissementFiltreForm extends FiltreDataForm
{
    public function __construct(
        public readonly ?string $numero             = null,
        public readonly ?string $denomination       = null,
        public readonly ?string $contribuable       = null,
        public readonly ?string $typeEtablissement  = null,
        public readonly ?string $statut             = null,
        public readonly ?int    $communeId          = null,
        public readonly ?int    $activiteId         = null,
    ) {}

    public static function regles(): array
    {
        return [
            'numero'             => ['nullable', 'string', 'max:12'],
            'denomination'       => ['nullable', 'string', 'max:128'],
            'contribuable'       => ['nullable', 'string', 'max:128'],
            'type_etablissement' => ['nullable', 'string', 'in:PRINCIPAL,SECONDAIRE,ANNEXE'],
            'statut'             => ['nullable', 'string', 'in:ACTIF,FERME,SUSPENDU'],
            'commune_id'         => ['nullable', 'integer', 'min:1'],
            'activite_id'        => ['nullable', 'integer', 'min:1'],
        ];
    }

    public static function fromRequest(Request $request): static
    {
        static::valider($request);

        return new static(
            numero:            $request->input('numero'),
            denomination:      $request->input('denomination'),
            contribuable:      $request->input('contribuable'),
            typeEtablissement: $request->input('type_etablissement'),
            statut:            $request->input('statut'),
            communeId:         $request->filled('commune_id')  ? (int) $request->input('commune_id')  : null,
            activiteId:        $request->filled('activite_id') ? (int) $request->input('activite_id') : null,
        );
    }

    public function appliquer(Builder $query): Builder
    {
        if (filled($this->numero)) {
            $query->where('numero', 'ilike', "%{$this->numero}%");
        }

        if (filled($this->denomination)) {
            $query->where('denomination', 'ilike', "%{$this->denomination}%");
        }

        if (filled($this->contribuable)) {
            $terme = "%{$this->contribuable}%";
            $query->whereHas('contribuable', function (Builder $q) use ($terme) {
                $q->where('nom',                 'ilike', $terme)
                  ->orWhere('prenoms',            'ilike', $terme)
                  ->orWhere('raison_sociale',     'ilike', $terme)
                  ->orWhere('numero_identifiant', 'ilike', $terme);
            });
        }

        if (filled($this->typeEtablissement)) {
            $query->where('type_etablissement', $this->typeEtablissement);
        }

        if (filled($this->statut)) {
            $query->where('statut', $this->statut);
        }

        if (filled($this->communeId)) {
            $query->where('commune_id', $this->communeId);
        }

        if (filled($this->activiteId)) {
            $query->where('activite_id', $this->activiteId);
        }

        return $query;
    }
}
