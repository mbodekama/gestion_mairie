<?php

namespace App\Http\FiltreDataForm;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ContribuableFiltreForm extends FiltreDataForm
{
    public function __construct(
        public readonly ?string $numeroIdentifiant  = null,
        public readonly ?string $nom                = null,
        public readonly ?string $typePersonne        = null,
        public readonly ?string $statut              = null,
        public readonly ?int    $regimeImpositionId  = null,
    ) {}

    public static function regles(): array
    {
        return [
            'numero_identifiant'  => ['nullable', 'string', 'max:12'],
            'nom'                 => ['nullable', 'string', 'max:128'],
            'type_personne'       => ['nullable', 'string', 'in:PP,PM'],
            'statut'              => ['nullable', 'string', 'in:ACTIF,SUSPENDU,RADIE'],
            'regime_imposition_id'=> ['nullable', 'integer', 'min:1'],
        ];
    }

    public static function fromRequest(Request $request): static
    {
        static::valider($request);

        return new static(
            numeroIdentifiant:  $request->input('numero_identifiant'),
            nom:                $request->input('nom'),
            typePersonne:       $request->input('type_personne'),
            statut:             $request->input('statut'),
            regimeImpositionId: $request->filled('regime_imposition_id')
                ? (int) $request->input('regime_imposition_id')
                : null,
        );
    }

    /**
     * Snapshot sérialisable des critères (pour persistance d'une campagne).
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'numero_identifiant'   => $this->numeroIdentifiant,
            'nom'                  => $this->nom,
            'type_personne'        => $this->typePersonne,
            'statut'               => $this->statut,
            'regime_imposition_id' => $this->regimeImpositionId,
        ];
    }

    /**
     * Reconstruit le filtre depuis un snapshot persisté (sans requête HTTP).
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): static
    {
        $regime = $data['regime_imposition_id'] ?? null;

        return new static(
            numeroIdentifiant:  $data['numero_identifiant'] ?? null,
            nom:                $data['nom'] ?? null,
            typePersonne:       $data['type_personne'] ?? null,
            statut:             $data['statut'] ?? null,
            regimeImpositionId: filled($regime) ? (int) $regime : null,
        );
    }

    public function appliquer(Builder $query): Builder
    {
        if (filled($this->numeroIdentifiant)) {
            $query->where('numero_identifiant', 'ilike', "%{$this->numeroIdentifiant}%");
        }

        if (filled($this->nom)) {
            $terme = "%{$this->nom}%";
            $query->where(function (Builder $q) use ($terme) {
                $q->where('nom',             'ilike', $terme)
                  ->orWhere('prenoms',        'ilike', $terme)
                  ->orWhere('raison_sociale', 'ilike', $terme);
            });
        }

        if (filled($this->typePersonne)) {
            $query->where('type_personne', $this->typePersonne);
        }

        if (filled($this->statut)) {
            $query->where('statut', $this->statut);
        }

        if (filled($this->regimeImpositionId)) {
            $query->where('regime_imposition_id', $this->regimeImpositionId);
        }

        return $query;
    }
}
