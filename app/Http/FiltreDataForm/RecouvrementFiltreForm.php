<?php

namespace App\Http\FiltreDataForm;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class RecouvrementFiltreForm extends FiltreDataForm
{
    public function __construct(
        public readonly ?string $numeroReglement   = null,
        public readonly ?string $numeroQuittance   = null,
        public readonly ?string $contribuable      = null,
        public readonly ?int    $modeReglementId   = null,
        public readonly ?int    $typeReglementId   = null,
        public readonly ?int    $exerciceFiscalId  = null,
        public readonly ?string $dateDu            = null,
        public readonly ?string $dateAu            = null,
    ) {}

    public static function regles(): array
    {
        return [
            'numero_reglement'   => ['nullable', 'string', 'max:20'],
            'numero_quittance'   => ['nullable', 'string', 'max:20'],
            'contribuable'       => ['nullable', 'string', 'max:128'],
            'mode_reglement_id'  => ['nullable', 'integer', 'min:1'],
            'type_reglement_id'  => ['nullable', 'integer', 'min:1'],
            'exercice_fiscal_id' => ['nullable', 'integer', 'min:1'],
            'date_du'            => ['nullable', 'date_format:d/m/Y'],
            'date_au'            => ['nullable', 'date_format:d/m/Y', 'after_or_equal:date_du'],
        ];
    }

    public static function fromRequest(Request $request): static
    {
        static::valider($request);

        return new static(
            numeroReglement:  $request->input('numero_reglement'),
            numeroQuittance:  $request->input('numero_quittance'),
            contribuable:     $request->input('contribuable'),
            modeReglementId:  $request->filled('mode_reglement_id')  ? (int) $request->input('mode_reglement_id')  : null,
            typeReglementId:  $request->filled('type_reglement_id')  ? (int) $request->input('type_reglement_id')  : null,
            exerciceFiscalId: $request->filled('exercice_fiscal_id') ? (int) $request->input('exercice_fiscal_id') : null,
            dateDu:           static::parseDate($request->input('date_du')),
            dateAu:           static::parseDate($request->input('date_au')),
        );
    }

    public function appliquer(Builder $query): Builder
    {
        if (filled($this->numeroReglement)) {
            $query->where('numero_reglement', 'ilike', "%{$this->numeroReglement}%");
        }

        if (filled($this->numeroQuittance)) {
            $query->where('numero_quittance', 'ilike', "%{$this->numeroQuittance}%");
        }

        if (filled($this->contribuable)) {
            $terme = "%{$this->contribuable}%";
            $query->where(function (Builder $q) use ($terme) {
                $q->whereHas('emissionTaxe.etablissement.contribuable', function (Builder $sq) use ($terme) {
                    $sq->where('nom',                 'ilike', $terme)
                       ->orWhere('prenoms',            'ilike', $terme)
                       ->orWhere('raison_sociale',     'ilike', $terme)
                       ->orWhere('numero_identifiant', 'ilike', $terme);
                })->orWhereHas('emissionCotisation.etablissement.contribuable', function (Builder $sq) use ($terme) {
                    $sq->where('nom',                 'ilike', $terme)
                       ->orWhere('prenoms',            'ilike', $terme)
                       ->orWhere('raison_sociale',     'ilike', $terme)
                       ->orWhere('numero_identifiant', 'ilike', $terme);
                });
            });
        }

        if (filled($this->modeReglementId)) {
            $query->where('mode_reglement_id', $this->modeReglementId);
        }

        if (filled($this->typeReglementId)) {
            $query->where('type_reglement_id', $this->typeReglementId);
        }

        if (filled($this->exerciceFiscalId)) {
            $query->where('exercice_fiscal_id', $this->exerciceFiscalId);
        }

        if (filled($this->dateDu)) {
            $query->whereDate('date_reglement', '>=', $this->dateDu);
        }

        if (filled($this->dateAu)) {
            $query->whereDate('date_reglement', '<=', $this->dateAu);
        }

        return $query;
    }
}
