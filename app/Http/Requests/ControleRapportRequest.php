<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Saisie du rapport de contrôle : les constats par nature de taxe.
 */
class ControleRapportRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        // Retire les séparateurs de milliers des montants saisis.
        $constats = $this->input('constats', []);
        if (is_array($constats)) {
            foreach ($constats as $i => $c) {
                foreach (['montant_declare', 'montant_verifie'] as $champ) {
                    if (isset($c[$champ]) && is_string($c[$champ])) {
                        $constats[$i][$champ] = preg_replace('/[\s\x{00A0}\x{202F}]/u', '', $c[$champ]);
                    }
                }
            }
            $this->merge(['constats' => $constats]);
        }
    }

    public function rules(): array
    {
        return [
            'rapport_synthese'           => ['nullable', 'string', 'max:5000'],
            'constats'                   => ['nullable', 'array'],
            'constats.*.nature_taxe_id'     => ['required', 'integer', 'exists:nature_taxe,id'],
            'constats.*.exercice_fiscal_id' => ['nullable', 'integer', 'exists:exercice_fiscal,id'],
            'constats.*.montant_declare'    => ['nullable', 'numeric', 'min:0'],
            'constats.*.montant_verifie'    => ['nullable', 'numeric', 'min:0'],
            'constats.*.sanction_fiscale_id'=> ['nullable', 'integer', 'exists:sanction_fiscale,id'],
            'constats.*.observation'        => ['nullable', 'string', 'max:255'],
        ];
    }
}
