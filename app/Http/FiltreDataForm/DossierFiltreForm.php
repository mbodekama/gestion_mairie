<?php

namespace App\Http\FiltreDataForm;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DossierFiltreForm extends FiltreDataForm
{
    public function __construct(
        public readonly ?string $numero                  = null,
        public readonly ?string $etablissement           = null,
        public readonly ?int    $familleEtatDossierId    = null,
        public readonly ?int    $categorieEtatDossierId  = null,
        public readonly ?string $archive                 = null,
    ) {}

    public static function regles(): array
    {
        return [
            'numero'                    => ['nullable', 'string', 'max:8'],
            'etablissement'             => ['nullable', 'string', 'max:128'],
            'famille_etat_dossier_id'   => ['nullable', 'integer', 'min:1'],
            'categorie_etat_dossier_id' => ['nullable', 'integer', 'min:1'],
            'archive'                   => ['nullable', 'in:0,1'],
        ];
    }

    public static function fromRequest(Request $request): static
    {
        static::valider($request);

        return new static(
            numero:                 $request->input('numero'),
            etablissement:          $request->input('etablissement'),
            familleEtatDossierId:   $request->filled('famille_etat_dossier_id') ? (int) $request->input('famille_etat_dossier_id') : null,
            categorieEtatDossierId: $request->filled('categorie_etat_dossier_id') ? (int) $request->input('categorie_etat_dossier_id') : null,
            archive:                $request->input('archive'),
        );
    }

    public function appliquer(Builder $query): Builder
    {
        if (filled($this->numero)) {
            $query->where('numero', 'ilike', "%{$this->numero}%");
        }

        if (filled($this->etablissement)) {
            $terme = "%{$this->etablissement}%";
            $query->whereHas('etablissement', function (Builder $q) use ($terme) {
                $q->where('denomination', 'ilike', $terme)
                  ->orWhereHas('contribuable', function (Builder $cq) use ($terme) {
                      $cq->where('nom', 'ilike', $terme)
                         ->orWhere('raison_sociale', 'ilike', $terme)
                         ->orWhere('numero_identifiant', 'ilike', $terme);
                  });
            });
        }

        if (filled($this->familleEtatDossierId)) {
            $query->where('famille_etat_dossier_id', $this->familleEtatDossierId);
        }

        if (filled($this->categorieEtatDossierId)) {
            $query->where('categorie_etat_dossier_id', $this->categorieEtatDossierId);
        }

        if ($this->archive !== null && $this->archive !== '') {
            $query->where('archive', (bool) $this->archive);
        }

        return $query;
    }
}
