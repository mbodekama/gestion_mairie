<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Saisie / mise à jour de l'instruction d'un contrôle fiscal.
 */
class ControleStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'etablissement_id'     => ['required', 'integer', 'exists:etablissement,id'],
            'agent_instructeur_id' => ['nullable', 'integer', 'exists:agent,id'],
            'periode_debut'        => ['nullable', 'date'],
            'periode_fin'          => ['nullable', 'date', 'after_or_equal:periode_debut'],
            'motif'                => ['nullable', 'string', 'max:512'],
        ];
    }

    public function messages(): array
    {
        return [
            'etablissement_id.required' => "L'établissement à contrôler est obligatoire.",
            'periode_fin.after_or_equal' => 'La fin de période doit suivre le début.',
        ];
    }
}
