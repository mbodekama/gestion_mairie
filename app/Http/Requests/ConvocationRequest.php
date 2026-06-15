<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Création / mise à jour d'une convocation autonome (hors workflow de contrôle).
 */
class ConvocationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'etablissement_id'  => ['required', 'integer', 'exists:etablissement,id'],
            'service_id'        => ['required', 'integer', 'exists:service,id'],
            'agent_id'          => ['required', 'integer', 'exists:agent,id'],
            'annee'             => ['required', 'integer', 'min:2000', 'max:2100'],
            'motif'             => ['nullable', 'string', 'max:512'],
            'date_convocation'  => ['nullable', 'date'],
            'delai_reponse'     => ['nullable', 'integer', 'min:1'],
            'date_limite'       => ['nullable', 'date'],
            'date_reponse'      => ['nullable', 'date'],
            'periode_due_debut' => ['nullable', 'date'],
            'periode_due_fin'   => ['nullable', 'date', 'after_or_equal:periode_due_debut'],
            'nb_mois_du'        => ['nullable', 'integer', 'min:0'],
            'nb_jours_du'       => ['nullable', 'integer', 'min:0'],
            'montant_du'        => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'etablissement_id.required' => "L'établissement est obligatoire.",
            'service_id.required'       => 'Le service est obligatoire.',
            'agent_id.required'         => "L'agent chargé est obligatoire.",
            'annee.required'            => "L'année est obligatoire.",
        ];
    }
}
