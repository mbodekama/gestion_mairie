<?php

namespace App\Http\FiltreDataForm;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ExonerationFiltreForm extends FiltreDataForm
{
    public function __construct(
        public readonly ?string $numero            = null,
        public readonly ?string $contribuable      = null,
        public readonly ?int    $typeExonerationId = null,
        public readonly ?string $dateDebutDu       = null,
        public readonly ?string $dateDebutAu       = null,
    ) {}

    public static function regles(): array
    {
        return [
            'numero'              => ['nullable', 'string', 'max:32'],
            'contribuable'        => ['nullable', 'string', 'max:128'],
            'type_exoneration_id' => ['nullable', 'integer', 'min:1'],
            'date_debut_du'       => ['nullable', 'date_format:d/m/Y'],
            'date_debut_au'       => ['nullable', 'date_format:d/m/Y', 'after_or_equal:date_debut_du'],
        ];
    }

    public static function fromRequest(Request $request): static
    {
        static::valider($request);

        return new static(
            numero:            $request->input('numero'),
            contribuable:      $request->input('contribuable'),
            typeExonerationId: $request->filled('type_exoneration_id') ? (int) $request->input('type_exoneration_id') : null,
            dateDebutDu:       static::parseDate($request->input('date_debut_du')),
            dateDebutAu:       static::parseDate($request->input('date_debut_au')),
        );
    }

    public function appliquer(Builder $query): Builder
    {
        if (filled($this->numero)) {
            $query->where('numero', 'ilike', "%{$this->numero}%");
        }

        if (filled($this->contribuable)) {
            $terme = "%{$this->contribuable}%";
            $query->whereHas('contribuable', function (Builder $q) use ($terme) {
                $q->where('nom', 'ilike', $terme)
                  ->orWhere('prenoms', 'ilike', $terme)
                  ->orWhere('raison_sociale', 'ilike', $terme)
                  ->orWhere('numero_identifiant', 'ilike', $terme);
            });
        }

        if (filled($this->typeExonerationId)) {
            $query->where('type_exoneration_id', $this->typeExonerationId);
        }

        if (filled($this->dateDebutDu)) {
            $query->where('date_debut', '>=', $this->dateDebutDu);
        }

        if (filled($this->dateDebutAu)) {
            $query->where('date_debut', '<=', $this->dateDebutAu);
        }

        return $query;
    }
}
