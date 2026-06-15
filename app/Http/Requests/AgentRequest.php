<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Création et mise à jour d'un agent.
 */
class AgentRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        // Normalise la case à cocher « actif » (absente = false).
        $this->merge(['actif' => $this->boolean('actif')]);

        if ($this->filled('matricule')) {
            $this->merge(['matricule' => strtoupper(trim($this->input('matricule')))]);
        }
    }

    public function rules(): array
    {
        $agentId = optional($this->route('agent'))->id;

        return [
            'matricule'         => ['required', 'string', 'max:32', Rule::unique('agent', 'matricule')->ignore($agentId)],
            'nom'               => ['nullable', 'string', 'max:64'],
            'prenoms'           => ['nullable', 'string', 'max:128'],
            'fonction_agent_id' => ['nullable', 'integer', 'exists:fonction_agent,id'],
            'grade_agent_id'    => ['nullable', 'integer', 'exists:grade_agent,id'],
            'service_id'        => ['nullable', 'integer', 'exists:service,id'],
            'superieur_id'      => ['nullable', 'integer', 'exists:agent,id', Rule::notIn([$agentId])],
            'observation'       => ['nullable', 'string', 'max:255'],
            'actif'             => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'matricule.required' => 'Le matricule est obligatoire.',
            'matricule.unique'   => 'Ce matricule est déjà attribué à un autre agent.',
            'superieur_id.not_in'=> 'Un agent ne peut pas être son propre supérieur.',
        ];
    }
}
