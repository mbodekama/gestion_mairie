<?php

namespace App\Http\FiltreDataForm;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Filtre de la liste des dossiers de redressement.
 */
class RedressementFiltreForm extends FiltreDataForm
{
    public function __construct(
        public readonly ?string $numero        = null,
        public readonly ?string $etablissement = null,
        public readonly ?string $etat          = null,
    ) {}

    public static function regles(): array
    {
        return [
            'numero'        => ['nullable', 'string', 'max:16'],
            'etablissement' => ['nullable', 'string', 'max:128'],
            'etat'          => ['nullable', 'string', 'max:16'],
        ];
    }

    public static function fromRequest(Request $request): static
    {
        static::valider($request);

        return new static(
            numero:        $request->input('numero'),
            etablissement: $request->input('etablissement'),
            etat:          $request->input('etat'),
        );
    }

    public function appliquer(Builder $query): Builder
    {
        if (filled($this->numero)) {
            $query->where('numero', 'ilike', "%{$this->numero}%");
        }

        if (filled($this->etablissement)) {
            $terme = "%{$this->etablissement}%";
            $query->whereHas('controleFiscal.etablissement', function (Builder $q) use ($terme) {
                $q->where('denomination', 'ilike', $terme)
                  ->orWhereHas('contribuable', function (Builder $cq) use ($terme) {
                      $cq->where('nom', 'ilike', $terme)
                         ->orWhere('raison_sociale', 'ilike', $terme)
                         ->orWhere('numero_identifiant', 'ilike', $terme);
                  });
            });
        }

        if (filled($this->etat)) {
            $query->where('etat', $this->etat);
        }

        return $query;
    }
}
