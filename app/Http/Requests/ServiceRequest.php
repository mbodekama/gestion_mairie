<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Création et mise à jour d'un service.
 */
class ServiceRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->filled('code')) {
            $this->merge(['code' => strtoupper(trim($this->input('code')))]);
        }
    }

    public function rules(): array
    {
        $serviceId = optional($this->route('service'))->id;

        return [
            'code'                   => ['required', 'string', 'max:6', Rule::unique('service', 'code')->ignore($serviceId)],
            'libelle'                => ['required', 'string', 'max:128'],
            'sigle'                  => ['nullable', 'string', 'max:64'],
            'departement_service_id' => ['nullable', 'integer', 'exists:departement_service,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required'    => 'Le code du service est obligatoire.',
            'code.unique'      => 'Ce code de service est déjà utilisé.',
            'libelle.required' => 'Le libellé est obligatoire.',
        ];
    }
}
