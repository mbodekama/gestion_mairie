<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Génération des émissions complémentaires d'un redressement. Une ligne par
 * taxe redressée : nature, exercice, périodicité et montant.
 */
class RedressementEmissionRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $lignes = $this->input('lignes', []);
        if (is_array($lignes)) {
            foreach ($lignes as $i => $l) {
                foreach (['montant', 'penalite'] as $champ) {
                    if (isset($l[$champ]) && is_string($l[$champ])) {
                        $lignes[$i][$champ] = preg_replace('/[\s\x{00A0}\x{202F}]/u', '', $l[$champ]);
                    }
                }
            }
            $this->merge(['lignes' => $lignes]);
        }
    }

    public function rules(): array
    {
        return [
            'lignes'                     => ['required', 'array', 'min:1'],
            'lignes.*.nature_taxe_id'    => ['required', 'integer', 'exists:nature_taxe,id'],
            'lignes.*.exercice_fiscal_id'=> ['required', 'integer', 'exists:exercice_fiscal,id'],
            'lignes.*.periodicite_id'    => ['required', 'integer', 'exists:periodicite,id'],
            'lignes.*.montant'           => ['required', 'numeric', 'min:1'],
            'lignes.*.penalite'          => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'lignes.required' => 'Ajoutez au moins une ligne d\'émission complémentaire.',
        ];
    }
}
