<?php

namespace App\Http\FiltreDataForm;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class JournalConnexionFiltreForm extends FiltreDataForm
{
    public function __construct(
        public readonly ?string $login    = null,
        public readonly ?string $succes   = null,
        public readonly ?string $dateDu   = null,
        public readonly ?string $dateAu   = null,
    ) {}

    public static function regles(): array
    {
        return [
            'login'    => ['nullable', 'string', 'max:64'],
            'succes'   => ['nullable', 'in:0,1'],
            'date_du'  => ['nullable', 'date_format:d/m/Y'],
            'date_au'  => ['nullable', 'date_format:d/m/Y', 'after_or_equal:date_du'],
        ];
    }

    public static function fromRequest(Request $request): static
    {
        static::valider($request);

        return new static(
            login:  $request->input('login'),
            succes: $request->input('succes'),
            dateDu: static::parseDate($request->input('date_du')),
            dateAu: static::parseDate($request->input('date_au')),
        );
    }

    public function appliquer(Builder $query): Builder
    {
        if (filled($this->login)) {
            $query->where('login', 'ilike', "%{$this->login}%");
        }

        if ($this->succes !== null && $this->succes !== '') {
            $query->where('succes', (bool) $this->succes);
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
