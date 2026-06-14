<?php

namespace App\Http\FiltreDataForm;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ActiviteFiltreForm extends FiltreDataForm
{
    public function __construct(
        public readonly ?string $code               = null,
        public readonly ?string $libelle            = null,
        public readonly ?int    $secteurActiviteId  = null,
        public readonly ?int    $categorieActiviteId = null,
    ) {}

    public static function regles(): array
    {
        return [
            'code'                 => ['nullable', 'string', 'max:8'],
            'libelle'              => ['nullable', 'string', 'max:128'],
            'secteur_activite_id'  => ['nullable', 'integer', 'min:1'],
            'categorie_activite_id' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public static function fromRequest(Request $request): static
    {
        static::valider($request);

        return new static(
            code:                $request->input('code'),
            libelle:             $request->input('libelle'),
            secteurActiviteId:   $request->filled('secteur_activite_id') ? (int) $request->input('secteur_activite_id') : null,
            categorieActiviteId: $request->filled('categorie_activite_id') ? (int) $request->input('categorie_activite_id') : null,
        );
    }

    public function appliquer(Builder $query): Builder
    {
        if (filled($this->code)) {
            $query->where('code', 'ilike', "%{$this->code}%");
        }

        if (filled($this->libelle)) {
            $query->where('libelle', 'ilike', "%{$this->libelle}%");
        }

        if (filled($this->secteurActiviteId)) {
            $query->where('secteur_activite_id', $this->secteurActiviteId);
        }

        if (filled($this->categorieActiviteId)) {
            $query->where('categorie_activite_id', $this->categorieActiviteId);
        }

        return $query;
    }
}
