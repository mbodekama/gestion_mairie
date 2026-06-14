<?php

namespace App\Http\FiltreDataForm;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CommuneFiltreForm extends FiltreDataForm
{
    public function __construct(
        public readonly ?string $code    = null,
        public readonly ?string $libelle = null,
    ) {}

    public static function regles(): array
    {
        return [
            'code'    => ['nullable', 'string', 'max:3'],
            'libelle' => ['nullable', 'string', 'max:128'],
        ];
    }

    public static function fromRequest(Request $request): static
    {
        static::valider($request);

        return new static(
            code:    $request->input('code'),
            libelle: $request->input('libelle'),
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

        return $query;
    }
}
