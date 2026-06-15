<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Déclenche une transition du workflow. Le code cible détermine l'effet ;
 * les champs annexes (convocation, pénalités) ne sont requis que selon le cas.
 */
class ControleTransitionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code_cible' => ['required', 'string', Rule::in(['VALIDE', 'EXECUTE', 'CLOTURE', 'REDRESSE', 'INSTRUCTION'])],
            'motif'      => ['nullable', 'string', 'max:512'],

            // Effet « convocation » (validation)
            'service_id'       => ['nullable', 'integer', 'exists:service,id'],
            'agent_id'         => ['nullable', 'integer', 'exists:agent,id'],
            'date_convocation' => ['nullable', 'date'],
            'delai_reponse'    => ['nullable', 'integer', 'min:1'],
            'date_limite'      => ['nullable', 'date'],

            // Effet « redressement »
            'montant_penalites' => ['nullable', 'numeric', 'min:0'],
            'observation'       => ['nullable', 'string', 'max:255'],
        ];
    }

    /** Champs utiles au service de workflow (payload de l'effet). */
    public function payload(): array
    {
        return $this->only([
            'service_id', 'agent_id', 'date_convocation', 'delai_reponse',
            'date_limite', 'montant_penalites', 'observation', 'motif',
        ]);
    }
}
