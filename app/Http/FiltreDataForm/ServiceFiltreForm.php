<?php

namespace App\Http\FiltreDataForm;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Filtre de la liste des services.
 */
class ServiceFiltreForm extends FiltreDataForm
{
    public function __construct(
        public readonly ?string $recherche             = null,
        public readonly ?int    $departementServiceId  = null,
    ) {}

    public static function regles(): array
    {
        return [
            'recherche'              => ['nullable', 'string', 'max:128'],
            'departement_service_id' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public static function fromRequest(Request $request): static
    {
        static::valider($request);

        return new static(
            recherche:            $request->input('recherche'),
            departementServiceId: $request->filled('departement_service_id') ? (int) $request->input('departement_service_id') : null,
        );
    }

    public function appliquer(Builder $query): Builder
    {
        if (filled($this->recherche)) {
            $terme = "%{$this->recherche}%";
            $query->where(function (Builder $q) use ($terme) {
                $q->where('code', 'ilike', $terme)
                  ->orWhere('libelle', 'ilike', $terme)
                  ->orWhere('sigle', 'ilike', $terme);
            });
        }

        if (filled($this->departementServiceId)) {
            $query->where('departement_service_id', $this->departementServiceId);
        }

        return $query;
    }
}
