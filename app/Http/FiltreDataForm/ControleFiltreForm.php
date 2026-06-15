<?php

namespace App\Http\FiltreDataForm;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Filtre de la liste des contrôles fiscaux (entité controle_fiscal).
 */
class ControleFiltreForm extends FiltreDataForm
{
    public function __construct(
        public readonly ?string $numero        = null,
        public readonly ?string $etablissement = null,
        public readonly ?int    $etatControleId = null,
        public readonly ?string $dateDu        = null,
        public readonly ?string $dateAu        = null,
    ) {}

    public static function regles(): array
    {
        return [
            'numero'           => ['nullable', 'string', 'max:16'],
            'etablissement'    => ['nullable', 'string', 'max:128'],
            'etat_controle_id' => ['nullable', 'integer'],
            'date_du'          => ['nullable', 'date_format:d/m/Y'],
            'date_au'          => ['nullable', 'date_format:d/m/Y', 'after_or_equal:date_du'],
        ];
    }

    public static function fromRequest(Request $request): static
    {
        static::valider($request);

        return new static(
            numero:         $request->input('numero'),
            etablissement:  $request->input('etablissement'),
            etatControleId: $request->filled('etat_controle_id') ? (int) $request->input('etat_controle_id') : null,
            dateDu:         static::parseDate($request->input('date_du')),
            dateAu:         static::parseDate($request->input('date_au')),
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

        if (filled($this->etatControleId)) {
            $query->where('etat_controle_id', $this->etatControleId);
        }

        if (filled($this->dateDu)) {
            $query->where('date_instruction', '>=', $this->dateDu);
        }

        if (filled($this->dateAu)) {
            $query->where('date_instruction', '<=', $this->dateAu);
        }

        return $query;
    }
}
