<?php

namespace App\Http\FiltreDataForm;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AuditLogFiltreForm extends FiltreDataForm
{
    public function __construct(
        public readonly ?string $tableCible = null,
        public readonly ?string $action     = null,
        public readonly ?string $dateDu     = null,
        public readonly ?string $dateAu     = null,
    ) {}

    public static function regles(): array
    {
        return [
            'table_cible' => ['nullable', 'string', 'max:64'],
            'action'      => ['nullable', 'in:INSERT,UPDATE,DELETE'],
            'date_du'     => ['nullable', 'date_format:d/m/Y'],
            'date_au'     => ['nullable', 'date_format:d/m/Y', 'after_or_equal:date_du'],
        ];
    }

    public static function fromRequest(Request $request): static
    {
        static::valider($request);

        return new static(
            tableCible: $request->input('table_cible'),
            action:     $request->input('action'),
            dateDu:     static::parseDate($request->input('date_du')),
            dateAu:     static::parseDate($request->input('date_au')),
        );
    }

    public function appliquer(Builder $query): Builder
    {
        if (filled($this->tableCible)) {
            $query->where('table_cible', 'ilike', "%{$this->tableCible}%");
        }

        if (filled($this->action)) {
            $query->where('action', $this->action);
        }

        if (filled($this->dateDu)) {
            $query->whereDate('horodatage', '>=', $this->dateDu);
        }

        if (filled($this->dateAu)) {
            $query->whereDate('horodatage', '<=', $this->dateAu);
        }

        return $query;
    }
}
