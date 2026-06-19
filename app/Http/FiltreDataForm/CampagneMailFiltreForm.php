<?php

namespace App\Http\FiltreDataForm;

use App\Models\CampagneMail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Filtre de la liste des campagnes de mails groupés.
 * La plage de dates s'applique à la date prévue pour l'envoi.
 */
class CampagneMailFiltreForm extends FiltreDataForm
{
    public function __construct(
        public readonly ?string $objet      = null,
        public readonly ?string $dateDebut  = null,
        public readonly ?string $dateFin    = null,
        public readonly ?string $statut     = null,
    ) {}

    public static function regles(): array
    {
        return [
            'objet'      => ['nullable', 'string', 'max:150'],
            'date_debut' => ['nullable', 'date_format:d/m/Y'],
            'date_fin'   => ['nullable', 'date_format:d/m/Y', 'after_or_equal:date_debut'],
            'statut'     => ['nullable', 'string', 'in:EN_ATTENTE,EN_COURS,ENVOYE,ECHEC'],
        ];
    }

    public static function fromRequest(Request $request): static
    {
        static::valider($request);

        return new static(
            objet:     $request->input('objet'),
            dateDebut: static::parseDate($request->input('date_debut')),
            dateFin:   static::parseDate($request->input('date_fin')),
            statut:    $request->input('statut'),
        );
    }

    public function appliquer(Builder $query): Builder
    {
        if (filled($this->objet)) {
            $query->where('objet', 'ilike', "%{$this->objet}%");
        }

        if (filled($this->dateDebut)) {
            $query->whereDate('date_envoi_prevue', '>=', $this->dateDebut);
        }

        if (filled($this->dateFin)) {
            $query->whereDate('date_envoi_prevue', '<=', $this->dateFin);
        }

        if (filled($this->statut)) {
            $query->where('statut', $this->statut);
        }

        return $query;
    }
}
