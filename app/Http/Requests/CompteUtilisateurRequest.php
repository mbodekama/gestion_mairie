<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Création et mise à jour d'un compte utilisateur rattaché à un agent.
 *
 * Le mot de passe est obligatoire à la création et facultatif en modification
 * (laisser vide pour le conserver). Les rôles référencent le catalogue spatie.
 */
class CompteUtilisateurRequest extends FormRequest
{
    private function estCreation(): bool
    {
        return $this->route('compte') === null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'actif' => $this->boolean('actif'),
            'email' => $this->filled('email') ? strtolower(trim($this->input('email'))) : null,
        ]);
    }

    public function rules(): array
    {
        $compteId = optional($this->route('compte'))->id;

        return [
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($compteId)],
            'password'   => [$this->estCreation() ? 'required' : 'nullable', 'confirmed', 'string', 'min:8'],
            'actif'      => ['boolean'],
            'roles'      => ['nullable', 'array'],
            'roles.*'    => ['string', Rule::exists('roles', 'name')],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Le nom du compte est obligatoire.',
            'email.required'     => "L'adresse e-mail est obligatoire.",
            'email.email'        => "L'adresse e-mail n'est pas valide.",
            'email.unique'       => 'Cette adresse e-mail est déjà utilisée par un compte.',
            'password.required'  => 'Le mot de passe est obligatoire à la création.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'password.min'       => 'Le mot de passe doit contenir au moins 8 caractères.',
            'roles.*.exists'     => 'Rôle inconnu.',
        ];
    }
}
